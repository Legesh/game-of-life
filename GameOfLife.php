<?php

namespace GameOfLife;

include 'src/Game.php';
include 'src/Grid.php';
include 'templates/Glider.php';

$game = new Game();
$game->loop();

print "\nGame Over!\n\n";