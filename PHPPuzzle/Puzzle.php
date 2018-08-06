<?php
/**
 * Copyright © 2018 denistrator. No rights were reserved.
 */

class Puzzle
{
    const MAX_FPS = 25;

    const KEY_CODE_UP = 119;
    const KEY_CODE_DOWN = 115;
    const KEY_CODE_LEFT = 97;
    const KEY_CODE_RIGHT = 100;

    const ALLOWED_INPUT_CHARACTERS = [
        self::KEY_CODE_UP,
        self::KEY_CODE_DOWN,
        self::KEY_CODE_LEFT,
        self::KEY_CODE_RIGHT
    ];

    const SCREEN_SIZE_Y_MIN = 1;
    const SCREEN_SIZE_Y_MAX = 4;
    const SCREEN_SIZE_X_MIN = 1;
    const SCREEN_SIZE_X_MAX = 4;

    const EMPTY_CHIP_CHARACTERS = '  ';

    const POINTER_CHARACTER = self::EMPTY_CHIP_CHARACTERS;

    const COMPLETE_BOARD = [
        ['01', '02', '03', '04'],
        ['05', '06', '07', '08'],
        ['09', '10', '11', '12'],
        ['13', '14', '15', self::POINTER_CHARACTER]
    ];

    private $pointerPosY = 2;

    private $pointerPosX = 3;

    private $board = [
        ['01', '02', '03', '04'],
        ['05', '06', self::POINTER_CHARACTER, '07'],
        ['09', '10', '11', '08'],
        ['13', '14', '15', '12']
    ];

    private $inputCharacter;

    private $isPuzzleComplete = false;

    public function run()
    {
        system('stty cbreak -echo');

        $inputStream = fopen('php://stdin', 'r');

        echo PHP_EOL; // so the first app output will appear on new line

        $this->showStartScreen();

        while (!$this->isPuzzleComplete) {
            $this->getInputKey($inputStream);

            $this->updateBoard();

            $this->clearScreen();

            $this->limitScreenRefreshRate();

            $this->drawBoard();

            $this->checkIfPuzzleComplete();

            $this->showFinishScreen();
        }
    }

    public function getInputKey($input)
    {
        $this->inputCharacter = ord(fgetc($input));
    }

    public function updateBoard()
    {
        if ($this->canInputControlGameBoard()) {
            $this->swapGameChips();
        }
    }

    public function swapGameChips()
    {
        $oldPointerPosY = $this->pointerPosY;
        $oldPointerPosX = $this->pointerPosX;

        $this->movePointer($this->inputCharacter);

        $oldPointerCharacter = $this->board[$oldPointerPosY - 1][$oldPointerPosX - 1];
        $newPointerCharacter = $this->board[$this->pointerPosY - 1][$this->pointerPosX - 1];

        $this->board[$oldPointerPosY - 1][$oldPointerPosX - 1] = $newPointerCharacter;
        $this->board[$this->pointerPosY - 1][$this->pointerPosX - 1] = $oldPointerCharacter;
    }

    public function canInputControlGameBoard()
    {
        if (in_array($this->inputCharacter, self::ALLOWED_INPUT_CHARACTERS)) {
            return true;
        }

        return false;
    }

    public function drawBoard()
    {
        $screen = $this->board;

        $verticalSpacer = PHP_EOL;
        $horizontalSpacer = '  ';

        $frameCharacter = '#';
        $verticalSplitterCharacter = '-';
        $horizontalSplitterCharacter = '|';

        $boardFrameMargin = 3;
        $boardFrameMarginVertical = str_repeat($verticalSpacer, $boardFrameMargin);
        $boardFrameMarginHorizontal = str_repeat(' ', $boardFrameMargin * 2);

        $chipMarginStep = 1;
        $chipMarginVertical = 1 + $chipMarginStep * 2;
        $chipMiddleCell = ($chipMarginVertical - 1) / 2;
        $chipMarginHorizontal = str_repeat($horizontalSpacer, $chipMarginStep);

        $chipWidth = (strlen(self::EMPTY_CHIP_CHARACTERS) + strlen($chipMarginHorizontal) * 2);
        $chipSplitters = self::SCREEN_SIZE_X_MAX - 1;
        $chipsAmount = self::SCREEN_SIZE_X_MAX;
        $frameWidth = ($chipWidth * $chipsAmount) + $chipSplitters + 2; /* plus left & right frames */
        $boardFrameBorderVertical = str_repeat($frameCharacter, $frameWidth);

        echo $boardFrameMarginVertical;
        echo $boardFrameMarginHorizontal . $boardFrameBorderVertical . PHP_EOL;

        foreach ($screen as $screenRowKey => $screenRow) {

            echo $boardFrameMarginHorizontal . $frameCharacter . $chipMarginHorizontal;

            for ($chipMarginStep = 0; $chipMarginStep < $chipMarginVertical; $chipMarginStep++) {
                if ($chipMarginStep !== 0) {
                    echo $boardFrameMarginHorizontal . $frameCharacter . $chipMarginHorizontal;
                }

                foreach ($screenRow as $screenSymbolKey => $screenSymbol) {
                    if ($chipMarginStep === $chipMiddleCell) {
                        echo $screenSymbol;
                    } else {
                        echo self::EMPTY_CHIP_CHARACTERS;
                    }

                    if ($screenSymbolKey !== count($screenRow) - 1) {
                        echo $chipMarginHorizontal . $horizontalSplitterCharacter . $chipMarginHorizontal;
                    }
                }

                echo $chipMarginHorizontal . $frameCharacter;
                echo PHP_EOL;
            }

            if ($screenRowKey !== count($screen) - 1) {
                echo $boardFrameMarginHorizontal . $frameCharacter . str_repeat($verticalSplitterCharacter, $frameWidth - 2) . $frameCharacter . PHP_EOL;
            }
        }

        echo $boardFrameMarginHorizontal . $boardFrameBorderVertical . PHP_EOL;
        echo $boardFrameMarginVertical;
    }

    public function movePointer($direction)
    {
        if ($direction === self::KEY_CODE_UP) {
            if ($this->pointerPosY > self::SCREEN_SIZE_X_MIN) {
                $this->pointerPosY -= 1;
            }
        }

        if ($direction === self::KEY_CODE_DOWN) {
            if ($this->pointerPosY < self::SCREEN_SIZE_X_MAX) {
                $this->pointerPosY += 1;
            }
        }

        if ($direction === self::KEY_CODE_LEFT) {
            if ($this->pointerPosX > self::SCREEN_SIZE_X_MIN) {
                $this->pointerPosX -= 1;
            }
        }

        if ($direction === self::KEY_CODE_RIGHT) {
            if ($this->pointerPosX < self::SCREEN_SIZE_X_MAX) {
                $this->pointerPosX += 1;
            }
        }
    }

    public function clearScreen()
    {
        system('clear');
    }

    public function limitScreenRefreshRate()
    {
        sleep(1 / self::MAX_FPS);
    }

    public function checkIfPuzzleComplete()
    {
        if ($this->board === self::COMPLETE_BOARD) {
            $this->isPuzzleComplete = true;
        }
    }

    public function showStartScreen()
    {
        echo 'Use W, A, S, D to move the game pointer' . PHP_EOL;
        echo 'Press any key to start' . PHP_EOL;
    }

    public function showFinishScreen()
    {
        if ($this->isPuzzleComplete) {
            echo 'You\'ve completed the puzzle' . PHP_EOL;

            $this->showBonus();
        }
    }

    public function showBonus()
    {
        while (1) {
            echo 'Press F to respect the author or any other key exit' . PHP_EOL;

            $inputStream = fopen('php://stdin', 'r');
            $this->getInputKey($inputStream);

            // 70  = F
            // 102 = f
            if ($this->inputCharacter === 70 || $this->inputCharacter === 102) {
                exec('start https://www.youtube.com/watch?v=dQw4w9WgXcQ');
            }

            break;
        }
    }
}
