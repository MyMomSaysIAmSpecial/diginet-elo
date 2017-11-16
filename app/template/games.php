<?php $view->extend('../layout/layout.php') ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Games
                </h3>

                <div class="btn-group pull-right">
                    <a class="btn btn-default btn-sm" data-toggle="modal" href="#submit">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        Submit new game
                    </a>
                </div>
                <span class="clearfix"></span>
            </div>
            <ul class="list-group">
                <?php foreach ($games as $participants): ?>
                    <li class="list-group-item">
                        <?php $i = 0; # I need increment, because position not always starts from 0, shit actually. ?>
                        <?php foreach($participants as $position => $participant): ?>
                            <span class="text-<?= $participant->elo_change > 0 ? 'success' : 'danger'; ?>">
                                <?= $participant->name; ?> (<?= $participant->elo_change; ?>)
                            </span>

                            <?php if(!$i): ?>
                                vs.
                            <?php endif; ?>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<div id="submit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    Game won versus:
                </h4>
            </div>
            <div class="modal-body">
                <div class="btn-group pull-right" data-toggle="buttons" role="group">
                    <label class="btn btn-default active">
                        <input type="radio" value="1">
                        <i class="fa fa-user"></i> Solo
                    </label>
                    <label class="btn btn-default">
                        <input type="radio" value="2">
                        <i class="fa fa-group"></i> Team
                    </label>
                </div>

                <br />
                <br />

                <ul class="list-group">
                    <?php foreach ($players as $position => $player): ?>
                        <li class="list-group-item player-selector" data-id="<?= $player->id; ?>">
                            <?= $player->name; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="modal-footer">
                <form method="POST">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        Close
                    </button>
                    <input type="hidden" name="opponent" value="" />
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-check" aria-hidden="true"></i>
                        Submit
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        var selectors = document.querySelectorAll('.player-selector');
        selectors.forEach(function(selector) {
            selector.addEventListener('click', function() {
                selectors.forEach(function(selector) {
                    selector.classList.remove('active');
                });

                this.classList.add('active');
                document.querySelector('input').value = this.dataset.id;
            });
        });
    })()
</script>