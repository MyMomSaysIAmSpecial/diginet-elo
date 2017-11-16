<?php $view->extend('../layout/layout.php') ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Solo
                </h3>
                <span class="clearfix"></span>
            </div>
            <ul class="list-group">
                <?php foreach ($players as $position => $player): ?>
                    <li class="list-group-item">
                        <?php if($position <= 2): ?>
                            <?php if(!$position): ?>
                                <i class="fa fa-trophy fa-trophy-gold" aria-hidden="true"></i>
                            <?php endif; ?>
                            <?php if($position == 1): ?>
                                <i class="fa fa-trophy fa-trophy-silver" aria-hidden="true"></i>
                            <?php endif; ?>
                            <?php if($position == 2): ?>
                                <i class="fa fa-trophy fa-trophy-bronze" aria-hidden="true"></i>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?= $player->name; ?>
                        (<?= $player->elo; ?>)
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Teams
                </h3>
                <span class="clearfix"></span>
            </div>
            <ul class="list-group">
                <?php $position = 0; ?>
                <?php foreach ($teams as $participants): ?>
                    <li class="list-group-item">
                        <?php if($position <= 2): ?>
                            <?php if(!$position): ?>
                                <i class="fa fa-trophy fa-trophy-gold" aria-hidden="true"></i>
                            <?php endif; ?>
                            <?php if($position == 1): ?>
                                <i class="fa fa-trophy fa-trophy-silver" aria-hidden="true"></i>
                            <?php endif; ?>
                            <?php if($position == 2): ?>
                                <i class="fa fa-trophy fa-trophy-bronze" aria-hidden="true"></i>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php $position++; ?>

                        <?php $i = 0; # I need increment, because position not always starts from 0, shit actually. ?>
                        <?php foreach($participants as $participant): ?>
                            <span class="text-default">
                                <?= $participant->player_name; ?>
                            </span>

                            <?php if(!$i): ?>
                                with.
                            <?php endif; ?>
                            <?php $i++; ?>
                        <?php endforeach; ?>

                        (<?=reset($participants)->team_elo;?>)
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>