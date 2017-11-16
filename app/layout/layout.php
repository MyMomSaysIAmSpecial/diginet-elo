<!DOCTYPE html>
<html>
<head>
    <title>MMR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet"/>

    <style>
        body {
            padding-top: 70px;
        }

        .progress {
            margin: 0;
        }

        .panel-title {
            line-height: 30px;
            float: left;
        }

        .fa-trophy-gold {
            color: gold;
        }

        .fa-trophy-silver {
            color: silver;
        }

        .fa-trophy-bronze {
            color: sandybrown;
        }
    </style>
</head>
<body>
<div class="container">
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-left">
                    <li>
                        <a href="./">
                            <span class="fa fa-user" aria-hidden="true"></span>
                            Profile
                        </a>
                    </li>
                    <li>
                        <a href="/teams">
                            <span class="fa fa-users" aria-hidden="true"></span>
                            <s>
                                Teams
                            </s>
                            <sup>
                                Soon
                            </sup>
                        </a>
                    </li>
                    <li>
                        <a href="./games">
                            <span class="fa fa-wheelchair-alt" aria-hidden="true"></span>
                            Games
                        </a>
                    </li>
                    <li>
                        <a href="./ladder">
                            <span class="fa fa-trophy" aria-hidden="true"></span>
                            Ladder
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?php $view['slots']->output('_content') ?>
</div>

<script>
    (function() {
        var sup = document.querySelector('sup');
        sup.parentNode.addEventListener('mouseover', function () {
            sup.innerHTML = 'Nope';
        });
        sup.parentNode.addEventListener('mouseout', function () {
            sup.innerHTML = 'Soon';
        });
    })();
</script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap.native/2.0.15/bootstrap-native.min.js"></script>
</body>
</html>