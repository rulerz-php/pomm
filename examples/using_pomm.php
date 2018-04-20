<?php

declare(strict_types=1);

list($pomm, $rulerz) = require __DIR__.'/bootstrap.php';

$players = $pomm['test_rulerz']->getModel(\Entity\Pomm\TestRulerz\PublicSchema\PlayersModel::class);
$rule = 'points > 300';

foreach ($rulerz->filter($players, $rule) as $player) {
    var_dump($player->getPseudo());
}
