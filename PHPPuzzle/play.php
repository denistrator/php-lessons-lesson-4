<?php

require_once 'Puzzle.php';

$args = getopt('s::i::');
$game = new Puzzle($args);
$game->run();
