<?php

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

    const POINTER_CHARACTER = '  ';

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

    public function updateBoard($inputCharacter)
    {
        if (in_array($inputCharacter, self::ALLOWED_INPUT_CHARACTERS)) {
            $oldPointerPosY = $this->pointerPosY;
            $oldPointerPosX = $this->pointerPosX;

            $this->movePointer($inputCharacter);

            $oldPointerCharacter = $this->board[$oldPointerPosY - 1][$oldPointerPosX - 1];
            $newPointerCharacter = $this->board[$this->pointerPosY - 1][$this->pointerPosX - 1];

            $this->board[$oldPointerPosY - 1][$oldPointerPosX - 1] = $newPointerCharacter;
            $this->board[$this->pointerPosY - 1][$this->pointerPosX - 1] = $oldPointerCharacter;

        }
    }

    public function run()
    {
        system('stty cbreak -echo');

        $inputStream = fopen('php://stdin', 'r');

        $this->showStartScreen();

        while (!$this->isPuzzleComplete) {
            $this->geuInputKey($inputStream);

            $this->updateBoard($this->inputCharacter);

            $this->clearScreen();

            $this->limitScreenRefreshRate();

            $this->drawBoard($this->board);

            $this->checkIfPuzzleComplete();

            $this->showFinishScreen();
        }
    }

    public function geuInputKey($input)
    {
        $this->inputCharacter = ord(fgetc($input));
    }

    public function drawBoard($screen)
    {
        foreach ($screen as $screenRow) {
            foreach ($screenRow as $screenSymbol) {
                echo $screenSymbol . '  ';
            }

            echo PHP_EOL;
            echo PHP_EOL;
        }
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
        echo 'Use W, A, S, D for moving the game pointer' . PHP_EOL;
        echo 'Press any key to start' . PHP_EOL;
    }

    public function showFinishScreen()
    {
        if ($this->isPuzzleComplete) {
            echo 'You\'ve completed the puzzle' . PHP_EOL;
        }
    }
}
