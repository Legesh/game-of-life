<?php

declare(strict_types=1);

namespace GameOfLife;

class Grid
{

    public array $cells = [];
    private int $width;
    private int $height;

    public function __construct(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function generateCells(bool $randomize, int $randMax = 10): void
    {
        for ($x = 0; $x < $this->width; $x++) {
            for ($y = 0; $y < $this->height; $y++) {
                if ($randomize) {
                    $this->cells[$y][$x] = $this->getRandomState($randMax);
                } else {
                    $this->cells[$y][$x] = 0;
                }
            }
        }
    }

    private function getRandomState(int $randMax = 1): bool
    {
        return rand(0, $randMax) === 0;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }
}