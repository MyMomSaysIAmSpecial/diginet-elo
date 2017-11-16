<?php $view->extend('../layout/layout.php') ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    You
                </h3>

                <div class="btn-group pull-right">
                    <a href="#" class="btn btn-default btn-sm disabled">
                        <i class="fa fa-pencil" aria-hidden="true"></i>
                        Edit profile
                    </a>
                </div>
                <span class="clearfix"></span>
            </div>
            <ul class="list-group">
                <li class="list-group-item">
                    Username: <?= $player->name; ?>
                </li>
                <li class="list-group-item">
                    Rank:
                    <?php if($player->position != 'Unranked' && $player->position - 1 <= 2): ?>
                        <?php if(!($player->position - 1)): ?>
                            <i class="fa fa-trophy fa-trophy-gold" aria-hidden="true"></i>
                        <?php endif; ?>
                        <?php if(($player->position - 1) == 1): ?>
                            <i class="fa fa-trophy fa-trophy-silver" aria-hidden="true"></i>
                        <?php endif; ?>
                        <?php if(($player->position) - 1 == 2): ?>
                            <i class="fa fa-trophy fa-trophy-bronze" aria-hidden="true"></i>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?= $player->position; ?>
                    (<?= $player->elo; ?>)
                </li>
            </ul>
        </div>
    </div>
</div>