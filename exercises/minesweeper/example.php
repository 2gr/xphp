<?php

function solve($minesweeperBoard)
{
    validateBorders($minesweeperBoard);

    validateBoardSize($minesweeperBoard);

    $grid = extractGrid($minesweeperBoard);

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

    return applyBorders($grid);
}

function validateBorders($board)
{
    $topBorder = array_shift($board);

    validateLineStartsAndEndsWith($topBorder, '+');

    foreach (str_split(substr($topBorder, 1, -1)) as $border) {
        if ($border !== '-') {
            throw new InvalidArgumentException('Top border is incomplete');
        }
    }

    $bottomBorder = array_pop($board);

    validateLineStartsAndEndsWith($bottomBorder, '+');

    foreach (str_split(substr($bottomBorder, 1, -1)) as $border) {
        if ($border !== '-') {
            throw new InvalidArgumentException('Bottom border is incomplete');
        }
    }

    foreach ($board as $line) {
        validateLineStartsAndEndsWith($line, '|');
    }
}

function validateLineStartsAndEndsWith($line, $char)
{
    if (substr($line, 0, 1) !== $char
        || substr($line, -1) !== $char
    ) {
        throw new InvalidArgumentException('Invalid edge' . $line . ' ' . $char);
    }
}

function extractGrid($minesweeperBoard)
{
    array_shift($minesweeperBoard);
    array_pop($minesweeperBoard);

    $grid = [];

    foreach ($minesweeperBoard as $line) {
        $grid[] = str_split(substr($line, 1, -1));
    }

    return $grid;
}

function validateGridSize($grid)
{
    if (count($grid[0]) < 2 && count($grid) < 2) {
        throw new InvalidArgumentException('Your grid is too small. Must be at least 2 squares');
    }
}

function validateBoardSize($board)
{
    $topRowWidth = strlen($board[0]);
    foreach ($board as $line) {
        if (strlen($line) !== $topRowWidth) {
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
