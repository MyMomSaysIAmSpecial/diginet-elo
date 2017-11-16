<?php
error_reporting(E_ALL);
require 'vendor/autoload.php';

use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\Helper\SlotsHelper;

use Slim\Http\Request;
use Slim\Http\Response;

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'determineRouteBeforeAppMiddleware' => true,
    ],
]);
$container = $app->getContainer();

$container['session'] = function () {
    return new Symfony\Component\HttpFoundation\Session\Session();
};

$container['view'] = function () {
    $templating = new PhpEngine(
        new TemplateNameParser(),
        new FilesystemLoader(
            [
                __DIR__ . '/app/template/%name%'
            ]
        )
    );
    $templating->set(new SlotsHelper());

    return $templating;
};

$container['database'] = function () {
    $database = new \Illuminate\Database\Capsule\Manager;

    $database->addConnection(
        [
            'driver' => 'mysql',
            'host' => 'localhost',
            'username' => 'root',
            'password' => 'toor',
            'database' => 'sizematters',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ],
        'default'
    );

    $database->setAsGlobal();

    return $database;
};

$container['elo'] = function() {
    return new EloCalculator();
};

$app->add(
    function (Request $request, Response $response, callable $next) {
        /**
         * @var $session \Symfony\Component\HttpFoundation\Session\Session
         */
        $session = $this->get('session');
        $session->start();

        return $next($request, $response);
    }
);

$app->add(
    function (Request $request, Response $response, callable $next) {
        /**
         * @var $session \Symfony\Component\HttpFoundation\Session\Session
         */
        $session = $this->get('session');
        $session->start();

//        $session->set('user', ['id' => 1]);

        $route = $request->getAttribute('route');

        if (empty($session->get('user'))) {
            if (!$route->getName() || ($route->getName() != 'login' && $route->getName() != 'login-process')) {
                $uri = $request
                    ->getUri()
//                    ->withScheme('https')
//                    ->withPort(443)
                    ->withPath(
                        $this->router->pathFor('login')
                    );

                return $response->withRedirect((string)$uri);
            }
        }

        return $next($request, $response);
    }
);

$app->get(
    '/login',
    function (Request $request, Response $response, $arguments) {
        return $this->view->render(
            '/login.php'
        );
    }
)->setName('login');

$app->post(
    '/login',
    function (Request $request, Response $response, $arguments) {
        /**
         * @var $database \Illuminate\Database\Capsule\Manager
         * @var $session \Symfony\Component\HttpFoundation\Session\Session
         */
        $database = $this->get('database');
        $session = $this->get('session');

        $input = $request->getParsedBody();

        $password = hash('sha512', $input['password']);

        $player = $database->table('players')
            ->where(
                [
                    'name' => $input['name'],
                    'password' => $password
                ]
            )
            ->get();


        if ($player->isEmpty()) {
            $userUid = $database->table('players')
                ->insertGetId(
                    [
                        'name' => $input['name'],
                        'password' => $password
                    ]
                );
        } else {
            $userUid = $player->first()->id;
        }

        $session->set('user', ['id' => $userUid]);

        $uri = $request
            ->getUri()
//            ->withScheme('https')
//            ->withPort(443)
            ->withPath(
                '/'
            );
//
//        if ($request->getParam('username') == 'developer' && $request->getParam('password') == 'developer') {
//            $uri = $request
//                ->getUri()
////                ->withScheme('https')
////                ->withPort(443)
//                ->withPath(
//                    $this->router->pathFor('landing')
//                );
//
//            $_SESSION['user'] = [
//                'name' => $request->getParam('username')
//            ];
//        }

        return $response->withRedirect((string)$uri);
    }
)->setName('login-process');

$app->get(
    '/',
    function (Request $request, Response $response, $arguments) {
        /**
         * @var $database \Illuminate\Database\Capsule\Manager
         * @var $session \Symfony\Component\HttpFoundation\Session\Session
         */
        $database = $this->get('database');
        $session = $this->get('session');

        $players = $database->table('players')
            ->distinct()
            ->select(
                [
                    'id',
                    'name',
                    'elo'
                ]
            )
            ->join(
                'game_players',
                function ($join) {
                    /**
                     * @var $join \Illuminate\Database\Query\JoinClause
                     */
                    $join->on('game_players.player_id', '=', 'players.id');
                }
            )
            ->orderByDesc('elo')
            ->get();

        $player = $database->table('players')
            ->where(
                'id',
                $session->get('user')['id']
            )->first();

        $position = $players->search(
            function ($item, $key) use ($session) {
                return $item->id  == $session->get('user')['id'];
            }
        );

        $player->position = $position !== false ? $position + 1 : 'Unranked';

        return $this->view->render(
            '/profile.php',
            [
                'player' => $player
            ]
        );
    }
);

$app->group(
    '/teams',
    function() {
        $this->get(
            (string)null,
            function (Request $request, Response $response, $arguments) {
                /**
                 * @var $database \Illuminate\Database\Capsule\Manager
                 * @var $session \Symfony\Component\HttpFoundation\Session\Session
                 */
                $database = $this->get('database');
                $session = $this->get('session');

                $teams = $database->table('teams')
                    ->select(
                        [
                            'teams.id',
                            'teams.elo as team_elo',
                            'team_players.team_id',
                            'team_players.player_id',
                            'players.name as player_name',
                            'players.elo as player_elo',
                        ]
                    )
                    ->join(
                        'team_players',
                        function ($join) {
                            /**
                             * @var $join \Illuminate\Database\Query\JoinClause
                             */
                            $join->on('teams.id', '=', 'team_players.team_id');
                        }
                    )
                    ->join(
                        'players',
                        function ($join) {
                            /**
                             * @var $join \Illuminate\Database\Query\JoinClause
                             */
                            $join->on('players.id', '=', 'team_players.player_id');
                        }
                    )
                    ->whereIn(
                        'teams.id',
                        $database->table('team_players')
                            ->distinct()
                            ->select('team_id')
                            ->where(
                                'player_id',
                                $session->get('user')['id']
                                )
                    )
                    ->get()
                    ->groupBy('team_id')
                    ->sort()
                    ->reverse()
                    ->map(
                        function ($game) {
                            return $game->sortBy('player_id');
                        }
                    );

                $players = $database->table('players')
                    ->select(
                        [
                            'id',
                            'name',
                            'elo'
                        ]
                    )
                    ->where(
                        'id',
                        '!=',
                        $session->get('user')['id']
                    )
                    ->orderByDesc('elo')
                    ->get();

                return $this->view->render(
                    '/teams.php',
                    [
                        'teams' => $teams,
                        'players' => $players->toArray()
                    ]
                );
            }
        );

        $this->post(
            (string)null,
            function (Request $request, Response $response, $arguments) {

            }
        );
    }
);

$app->group(
    '/games',
    function() {
        $this->get(
            (string)null,
            function (Request $request, Response $response, $arguments) {
                /**
                 * @var $database \Illuminate\Database\Capsule\Manager
                 * @var $session \Symfony\Component\HttpFoundation\Session\Session
                 */
                $database = $this->get('database');
                $session = $this->get('session');

                $games = $database->table('game_players')
                    ->select(
                        [
                            'game_players.game_id',
                            'game_players.player_id',
                            'game_players.elo_change',
                            'players.name',
                        ]
                    )
                    ->join(
                        'players',
                        function ($join) {
                            /**
                             * @var $join \Illuminate\Database\Query\JoinClause
                             */
                            $join->on('players.id', '=', 'game_players.player_id');
                        }
                    )
                    ->get()
                    ->groupBy('game_id')
                    ->sort()
                    ->reverse()
                    ->map(
                        function ($game) {
                            return $game->sortBy('player_id');
                        }
                    );

                $players = $database->table('players')
                    ->select(
                        [
                            'id',
                            'name',
                            'elo'
                        ]
                    )
                    ->where(
                        'id',
                        '!=',
                        $session->get('user')['id']
                    )
                    ->orderByDesc('elo')
                    ->get();

                return $this->view->render(
                    '/games.php',
                    [
                        'players' => $players->toArray(),
                        'games' => $games->toArray()
                    ]
                );
            }
        );

        $this->post(
            (string)null,
            function (Request $request, Response $response, $arguments) {
                /**
                 * @var $database \Illuminate\Database\Capsule\Manager
                 * @var $session \Symfony\Component\HttpFoundation\Session\Session
                 * @var $calculator EloCalculator
                 */
                $database = $this->get('database');
                $session = $this->get('session');
                $calculator = $this->get('elo');

                $game = $database->table('games')
                    ->insertGetId(
                        []
                    );

                # Game players
                $players = $database->table('players')
                    ->select(
                        [
                            'id',
                            'name',
                            'elo'
                        ]
                    )
                    ->whereIn(
                        'id',
                        [
                            $session->get('user')['id'],
                            $request->getParsedBody()['opponent']
                        ]
                    )
                    ->orderByDesc('elo')
                    ->get();

                # Winner
                $winner = $players
                    ->where(
                        'id',
                        $session->get('user')['id']
                    )
                    ->first();

                # Loser
                $loser = $players
                    ->where(
                        'id',
                        $request->getParsedBody()['opponent']
                    )
                    ->first();

                # New winner elo
                $elo = $calculator->calculate(
                    $winner->elo,
                    $loser->elo,
                    'win'
                );

                # Update winner with new elo
                $database->table('players')
                    ->where(
                        'id',
                        $session->get('user')['id']
                    )
                    ->update(
                        [
                            'elo' => $elo
                        ]
                    );

                $database->table('game_players')
                    ->insert(
                        [
                            'game_id' => $game,
                            'player_id' => $winner->id,
                            'elo_change' => ($winner->elo > $elo ? '-' : '+') . abs($winner->elo - $elo)
                        ]
                    );

                # Loser new elo
                $elo = $calculator->calculate(
                    $loser->elo,
                    $winner->elo,
                    'lose'
                );

                # Update loser with new elo
                $database->table('players')
                    ->where(
                        'id',
                        $request->getParsedBody()['opponent']
                    )
                    ->update(
                        [
                            'elo' => $elo
                        ]
                    );

                $database->table('game_players')
                    ->insert(
                        [
                            'game_id' => $game,
                            'player_id' => $loser->id,
                            'elo_change' => ($loser->elo > $elo ? '-' : '+') . abs($loser->elo - $elo)
                        ]
                    );

                return $response->withRedirect('/');
            }
        );
    }
);

$app->get(
    '/ladder',
    function (Request $request, Response $response, $arguments) {
        /**
         * @var $database \Illuminate\Database\Capsule\Manager
         * @var $session \Symfony\Component\HttpFoundation\Session\Session
         */
        $database = $this->get('database');
        $session = $this->get('session');

        $players = $database->table('players')
            ->distinct()
            ->select(
                [
                    'id',
                    'name',
                    'elo'
                ]
            )
            ->join(
                'game_players',
                function ($join) {
                    /**
                     * @var $join \Illuminate\Database\Query\JoinClause
                     */
                    $join->on('game_players.player_id', '=', 'players.id');
                }
            )
            ->orderByDesc('elo')
            ->get();

        return $this->view->render(
            '/ladder.php',
            [
                'players' => $players->toArray()
            ]
        );
    }
);

$app->run();