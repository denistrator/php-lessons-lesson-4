<?php
/**
 * Copyright © 2018 denistrator. No rights were reserved.
 */

const KEY_CODE_UP = 119;
const KEY_CODE_DOWN = 115;
const KEY_CODE_LEFT = 97;
const KEY_CODE_RIGHT = 100;

const ALLOWED_INPUT_CHARACTERS = [
    KEY_CODE_UP,
    KEY_CODE_DOWN,
    KEY_CODE_LEFT,
    KEY_CODE_RIGHT
];

const SCREEN_SIZE_Y_MIN = 1;
const SCREEN_SIZE_Y_MAX = 4;
const SCREEN_SIZE_X_MIN = 1;
const SCREEN_SIZE_X_MAX = 4;

const EMPTY_CHIP_CHARACTERS = '  ';

const POINTER_CHARACTER = EMPTY_CHIP_CHARACTERS;

const COMPLETE_BOARD = [
    ['01', '02', '03', '04'],
    ['05', '06', '07', '08'],
    ['09', '10', '11', '12'],
    ['13', '14', '15', POINTER_CHARACTER]
];

$pointerPosY = 2;

$pointerPosX = 3;

$board = [
    ['01', '02', '03', '04'],
    ['05', '06', POINTER_CHARACTER, '07'],
    ['09', '10', '11', '08'],
    ['13', '14', '15', '12']
];

$inputCharacter = '';

$isPuzzleComplete = false;

run();

function run()
{
    $inputStream = fopen('php://stdin', 'r');

    echo PHP_EOL; // so the first app output will appear on new line

    showStartScreen();

    while (!$GLOBALS['isPuzzleComplete']) {
        getInputKey($inputStream);

        updateBoard();

        drawBoard();

        checkIfPuzzleComplete();

        showFinishScreen();
    }
}

function getInputKey($input)
{
    $GLOBALS['inputCharacter'] = ord(fgetc($input));
}

function updateBoard()
{
    if (canInputControlGameBoard()) {
        swapGameChips();
    }
}

function swapGameChips()
{
    $oldPointerPosY = $GLOBALS['pointerPosY'];
    $oldPointerPosX = $GLOBALS['pointerPosX'];

    movePointer($GLOBALS['inputCharacter']);

    $oldPointerCharacter = $GLOBALS['board'][$oldPointerPosY - 1][$oldPointerPosX - 1];
    $newPointerCharacter = $GLOBALS['board'][$GLOBALS['pointerPosY'] - 1][$GLOBALS['pointerPosX'] - 1];

    $GLOBALS['board'][$oldPointerPosY - 1][$oldPointerPosX - 1] = $newPointerCharacter;
    $GLOBALS['board'][$GLOBALS['pointerPosY'] - 1][$GLOBALS['pointerPosX'] - 1] = $oldPointerCharacter;
}

function canInputControlGameBoard()
{
    if (in_array($GLOBALS['inputCharacter'], ALLOWED_INPUT_CHARACTERS)) {
        return true;
    }

    return false;
}

function drawBoard()
{
    $screen = $GLOBALS['board'];

    $verticalSpacer = PHP_EOL;
    $horizontalSpacer = ' ';

    $frameCharacter = '#';
    $verticalSplitterCharacter = '-';
    $horizontalSplitterCharacter = '|';

    $boardFrameMargin = 1;
    $boardFrameMarginVertical = str_repeat($verticalSpacer, $boardFrameMargin);
    $boardFrameMarginHorizontal = str_repeat(' ', $boardFrameMargin * 2);

    $chipMarginHorizontal = str_repeat($horizontalSpacer, 1);
    $chipWidth = strlen(EMPTY_CHIP_CHARACTERS) + strlen($chipMarginHorizontal) * 2;
    $chipSplitters = SCREEN_SIZE_X_MAX - 1;
    $chipsAmount = SCREEN_SIZE_X_MAX;
    $frameWidth = ($chipWidth * $chipsAmount) + $chipSplitters + 2; /* plus left & right frames */
    $boardFrameBorderVertical = str_repeat($frameCharacter, $frameWidth);

    echo $boardFrameMarginVertical;
    echo $boardFrameMarginHorizontal . $boardFrameBorderVertical . PHP_EOL;

    foreach ($screen as $screenRowKey => $screenRow) {
        echo $boardFrameMarginHorizontal . $frameCharacter;

        foreach ($screenRow as $screenSymbolKey => $screenSymbol) {
            echo $horizontalSpacer . $screenSymbol . $horizontalSpacer;

            if ($screenSymbolKey !== count($screenRow) - 1) {
                echo $horizontalSplitterCharacter;
            }
        }

        echo $frameCharacter;
        echo PHP_EOL;

        if ($screenRowKey !== count($screen) - 1) {
            echo $boardFrameMarginHorizontal . $frameCharacter . str_repeat($verticalSplitterCharacter, $frameWidth - 2) . $frameCharacter . PHP_EOL;
        }
    }

    echo $boardFrameMarginHorizontal . $boardFrameBorderVertical . PHP_EOL;
    echo $boardFrameMarginVertical;
    echo str_repeat(PHP_EOL, 10); // to clear screen
}

function movePointer($direction)
{
    if ($direction === KEY_CODE_UP) {
        if ($GLOBALS['pointerPosY'] > SCREEN_SIZE_X_MIN) {
            $GLOBALS['pointerPosY'] -= 1;
        }
    }

    if ($direction === KEY_CODE_DOWN) {
        if ($GLOBALS['pointerPosY'] < SCREEN_SIZE_X_MAX) {
            $GLOBALS['pointerPosY'] += 1;
        }
    }

    if ($direction === KEY_CODE_LEFT) {
        if ($GLOBALS['pointerPosX'] > SCREEN_SIZE_X_MIN) {
            $GLOBALS['pointerPosX'] -= 1;
        }
    }

    if ($direction === KEY_CODE_RIGHT) {
        if ($GLOBALS['pointerPosX'] < SCREEN_SIZE_X_MAX) {
            $GLOBALS['pointerPosX'] += 1;
        }
    }
}

function checkIfPuzzleComplete()
{
    if ($GLOBALS['board'] === COMPLETE_BOARD) {
        $GLOBALS['isPuzzleComplete'] = true;
    }
}

function showStartScreen()
{
    echo 'Use W, A, S, D to move the game pointer' . PHP_EOL;
    echo 'Press any key to start' . PHP_EOL;
}

function showFinishScreen()
{
    if ($GLOBALS['isPuzzleComplete']) {
        echo 'You\'ve completed the puzzle' . PHP_EOL;
    }
}
