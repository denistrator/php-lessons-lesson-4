<?php

class Puzzle
{
    const INPUT_UP = 119;
    const INPUT_DOWN = 115;
    const INPUT_LEFT = 97;
    const INPUT_RIGHT = 100;

    const ALLOWED_INPUT_CHARACTERS = [
        self::INPUT_UP,
        self::INPUT_DOWN,
        self::INPUT_LEFT,
        self::INPUT_RIGHT
    ];

    const SCREEN_SIZE_Y_MIN = 1;
    const SCREEN_SIZE_Y_MAX = 4;
    const SCREEN_SIZE_X_MIN = 1;
    const SCREEN_SIZE_X_MAX = 4;

    /* default pointer position Y */
    private $pointerPosY = 2;

    /* default pointer position X */
    private $pointerPosX = 3;

    /* default game screen */
    private $screen = [
        ['01', '02', '03', '04'],
        ['05', '06', '  ', '07'],
        ['09', '10', '11', '08'],
        ['13', '14', '15', '12']
    ];

    private $inputCharacter;

    public function updateBoard($inputCharacter)
    {
        if (in_array($inputCharacter, self::ALLOWED_INPUT_CHARACTERS)) {
            $oldPointerPosY = $this->pointerPosY;
            $oldPointerPosX = $this->pointerPosX;

            $this->movePointer($inputCharacter);

            $oldPointerCharacter = $this->screen[$oldPointerPosY - 1][$oldPointerPosX - 1];
            $newPointerCharacter = $this->screen[$this->pointerPosY - 1][$this->pointerPosX - 1];

            $this->screen[$oldPointerPosY - 1][$oldPointerPosX - 1] = $newPointerCharacter;
            $this->screen[$this->pointerPosY - 1][$this->pointerPosX - 1] = $oldPointerCharacter;
        }
    }

    public function run()
    {
        system('stty cbreak -echo');

        $inputStream = fopen('php://stdin', 'r');

        echo 'Press any key to start' . PHP_EOL;

        while (1) {
            $this->geuInputKey($inputStream);

            $this->updateBoard($this->inputCharacter);

            $this->clearScreen();

            $this->drawBoard($this->screen);
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
        if ($direction === self::INPUT_UP) {
            if ($this->pointerPosY > self::SCREEN_SIZE_X_MIN) {
                $this->pointerPosY -= 1;
            }
        }

        if ($direction === self::INPUT_DOWN) {
            if ($this->pointerPosY < self::SCREEN_SIZE_X_MAX) {
                $this->pointerPosY += 1;
            }
        }

        if ($direction === self::INPUT_LEFT) {
            if ($this->pointerPosX > self::SCREEN_SIZE_X_MIN) {
                $this->pointerPosX -= 1;
            }
        }

        if ($direction === self::INPUT_RIGHT) {
            if ($this->pointerPosX < self::SCREEN_SIZE_X_MAX) {
                $this->pointerPosX += 1;
            }
        }
    }

    public function clearScreen()
    {
        system('clear');
    }
}
