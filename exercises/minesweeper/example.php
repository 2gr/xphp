<?php

function solve($minesweeperBoard)
{
    $minesweeperBoard = array_map('str_split', explode("\n", trim($minesweeperBoard)));

    validateBoardDimensions($minesweeperBoard);

    validateBorders($minesweeperBoard);

    $grid = removeBorders($minesweeperBoard);

    validateGridSize($grid);

    validateContainsOnlyMines($grid);

    foreach ($grid as $r => &$row) {
        foreach ($row as $c => &$cell) {
            if ($cell == '*') {
                continue;
            }
            if ($cell == ' ') {
                $cell = numSurroundingMines($grid, $r, $c);
            }
        }
    }

    return "\n" . implode("\n", applyBorders($grid)) . "\n";
}

function validateBorders($board)
{
    $topBorder = current(array_slice($board, 0, 1));
    $middle = array_slice($board, 1, -1);
    $bottomBorder = current(array_slice($board, 0, 1));

    validateArrayStartsAndEndsWith($topBorder, '+');
    validateArrayStartsAndEndsWith($bottomBorder, '+');

    foreach (array_slice($topBorder, 1, -1) as $border) {
        if ($border !== '-') {
            throw new InvalidArgumentException('Top border is incomplete');
        }
    }

    foreach (array_slice($bottomBorder, 1, -1) as $border) {
        if ($border !== '-') {
            throw new InvalidArgumentException('Bottom border is incomplete');
        }
    }

    foreach ($middle as $line) {
        validateArrayStartsAndEndsWith($line, '|');
    }
}

function validateArrayStartsAndEndsWith($line, $char)
{
    if ($line[0] !== $char
        || $line[count($line) - 1] !== $char
    ) {
        throw new InvalidArgumentException('Invalid edge' . implode($line) . ' ' . $char);
    }
}

function removeBorders($minesweeperBoard)
{
    array_shift($minesweeperBoard);
    array_pop($minesweeperBoard);

    $grid = [];

    foreach ($minesweeperBoard as $line) {
        $grid[] = (array_slice($line, 1, -1));
    }

    return $grid;
}

function validateGridSize($grid)
{
    if (count($grid[0]) < 2 && count($grid) < 2) {
        throw new InvalidArgumentException('Your grid is too small. Must be at least 2 squares');
    }
}

function validateBoardDimensions($board)
{
    $topRowWidth = count($board[0]);
    foreach ($board as $line) {
        if (count($line) !== $topRowWidth) {
            throw new InvalidArgumentException('Your rows are not of equal length');
        }
    }
}

function validateContainsOnlyMines($board)
{
    foreach ($board as $row) {
        foreach ($row as $cell) {
            if (!in_array($cell, [' ', '*'])) {
                throw new InvalidArgumentException('Your board contains illegal characters: ' . $cell);
            }
        }
    }
}

function applyBorders($grid)
{
    $width = count($grid[0]);
    $mid = array_map(function ($line) {
        array_unshift($line, '|');
        array_push($line, '|');
        return $line;
    }, $grid);
    $horizontalBorder = array_fill(0, $width, '-');
    array_unshift($horizontalBorder, '+');
    array_push($horizontalBorder, '+');

    array_unshift($mid, $horizontalBorder);
    array_push($mid, $horizontalBorder);

    return array_map('join', $mid);
}

function numSurroundingMines($grid, $r, $c)
{
    $mines = 0;
    if (isset($grid[$r][$c - 1]) && $grid[$r][$c - 1] == '*') {
        $mines += 1;
    }
    if (isset($grid[$r][$c + 1]) && $grid[$r][$c + 1] == '*') {
        $mines += 1;
    }
    if (isset($grid[$r - 1][$c]) && $grid[$r - 1][$c] == '*') {
        $mines += 1;
    }
    if (isset($grid[$r + 1][$c]) && $grid[$r + 1][$c] == '*') {
        $mines += 1;
    }
    if (isset($grid[$r - 1][$c - 1]) && $grid[$r - 1][$c - 1] == '*') {
        $mines += 1;
    }
    if (isset($grid[$r + 1][$c - 1]) && $grid[$r + 1][$c - 1] == '*') {
        $mines += 1;
    }
    if (isset($grid[$r - 1][$c + 1]) && $grid[$r - 1][$c + 1] == '*') {
        $mines += 1;
    }
    if (isset($grid[$r + 1][$c + 1]) && $grid[$r + 1][$c + 1] == '*') {
        $mines += 1;
    }
    return $mines > 0 ? $mines : ' ';
}
