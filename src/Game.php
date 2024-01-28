<?php

declare(strict_types=1);

namespace GameOfLife;

use templates\Glider;

class Game
{
    private const ALIVE_CELL = '*';
    private const DEAD_CELL = ' ';
    private const TIMEOUT = 50000;
    private const GRID_WIDTH = 10;
    private const GRID_HEIGHT = 10;
    private const IS_RANDOM = false;

    private array $generationHashes = [];
    private Grid $grid;

    public function __construct()
    {
        $this->grid = new Grid(self::GRID_WIDTH, self::GRID_HEIGHT);
        $this->grid->generateCells(self::IS_RANDOM);
        $this->setTemplate();
    }

    public function setTemplate(): void
    {
        $template = new Glider();
        $pattern = $template->render();

        $centerX = (int)floor(($this->grid->getWidth() - count($pattern[0])) / 2);
        $centerY = (int)floor(($this->grid->getHeight() - count($pattern)) / 2);
        $x = $centerX;
        $y = $centerY;
        foreach ($pattern as $row) {
            foreach ($row as $cell) {
                if ($cell == '*') {
                    $this->grid->cells[$y][$x] = 1;
                }
                $x++;
            }
            $y++;
            $x = $centerX;
        }
    }

    private function render(): void
    {
        foreach ($this->grid->cells as $y => $row) {
            $print_row = '';
            foreach ($row as $x => $cell) {
                $print_row .= ($cell ? self::ALIVE_CELL : self::DEAD_CELL);
            }
            print $print_row . "\n";
        }
    }

    /**
     * Start the game loop to render the Game of Life.
     */
    public function loop(): void
    {
        while (true) {
            $this->render();
            usleep(self::TIMEOUT);
            $this->clear();
            $this->newGeneration();
            if ($this->isEndlessLoop()) {
                break;
            }
        }
    }

    private function clear(): void
    {
        echo "\033[0;0H";
    }

    private function newGeneration(): void
    {
        $cells = &$this->grid->cells;
        $killQueue = $bornQueue = [];

        for ($y = 0; $y < $this->grid->getHeight(); $y++) {
            for ($x = 0; $x < $this->grid->getWidth(); $x++) {
                $neighborsCount = $this->getAliveNeighborCount($x, $y);

                if ($cells[$y][$x] && ($neighborsCount < 2 || $neighborsCount > 3)) {
                    $killQueue[] = [$y, $x];
                }
                if (!$cells[$y][$x] && $neighborsCount === 3) {
                    $bornQueue[] = [$y, $x];
                }
            }
        }

        foreach ($killQueue as $c) {
            $cells[$c[0]][$c[1]] = 0;
        }

        foreach ($bornQueue as $c) {
            $cells[$c[0]][$c[1]] = 1;
        }

        $this->trackGeneration();
    }

    private function getAliveNeighborCount($x, $y): int
    {
        $aliveCount = 0;
        for ($y2 = $y - 1; $y2 <= $y + 1; $y2++) {
            if ($y2 < 0 || $y2 >= $this->grid->getHeight()) {
                continue;
            }
            for ($x2 = $x - 1; $x2 <= $x + 1; $x2++) {
                if ($x2 == $x && $y2 == $y) {
                    continue;
                }
                if ($x2 < 0 || $x2 >= $this->grid->getWidth()) {
                    continue;
                }
                if ($this->grid->cells[$y2][$x2]) {
                    $aliveCount += 1;
                }
            }
        }
        return $aliveCount;
    }

    private function trackGeneration(): void
    {
        static $pointer;

        if (!isset($pointer)) {
            $pointer = 0;
        }

        $hash = md5(json_encode($this->grid->cells));
        $this->generationHashes[$pointer] = $hash;
        $pointer++;

        if ($pointer > 20) {
            $pointer = 0;
        }
    }

    private function isEndlessLoop(): bool
    {
        foreach ($this->generationHashes as $hash) {
            $found = -1;
            foreach ($this->generationHashes as $hash2) {
                if ($hash === $hash2) {
                    $found++;
                }
            }
            if ($found >= 3) {
                return true;
            }
        }
        return false;
    }
}