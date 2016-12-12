<?php

namespace Football;

use Football\FootballPoint;
use Football\FootballException;
use Football\FootballConfigurationException;
use Football\FootballBin;

/**
 * Description of Football
 *
 * @author mcmaroon
 */
class Football {

    /**
     * @var integer
     */
    const BOARD_WIDTH = 7;

    /**
     * @var integer
     */
    const BOARD_HEIGHT = 9;

    /**
     * users
     */
    const USER_FIRST = 0;
    const USER_SECOND = 1;

    /**
     * directions
     */
    const DIRECTION_TOP_LEFT = 0;
    const DIRECTION_TOP = 1;
    const DIRECTION_TOP_RIGHT = 2;
    const DIRECTION_RIGHT = 3;
    const DIRECTION_BOTTOM_RIGHT = 4;
    const DIRECTION_BOTTOM = 5;
    const DIRECTION_BOTTOM_LEFT = 6;
    const DIRECTION_LEFT = 7;

    private $width = 0;
    private $height = 0;
    private $board = array();
    private $map = array(
        self::DIRECTION_TOP_LEFT => self::DIRECTION_BOTTOM_RIGHT,
        self::DIRECTION_TOP => self::DIRECTION_BOTTOM,
        self::DIRECTION_TOP_RIGHT => self::DIRECTION_BOTTOM_LEFT,
        self::DIRECTION_RIGHT => self::DIRECTION_LEFT,
        self::DIRECTION_BOTTOM_RIGHT => self::DIRECTION_TOP_LEFT,
        self::DIRECTION_BOTTOM => self::DIRECTION_TOP,
        self::DIRECTION_BOTTOM_LEFT => self::DIRECTION_TOP_RIGHT,
        self::DIRECTION_LEFT => self::DIRECTION_RIGHT,
    );
    private $points = array();
    private $reflection = false;
    private $currentUser = self::USER_FIRST;

    /**
     *
     * @param type $width
     * @param type $height
     */
    public function __construct($width = self::BOARD_WIDTH, $height = self::BOARD_HEIGHT) {
        $this->setWidth($width);
        $this->setHeight($height);
        $this->setBoard();
    }

    // ~

    private function setWidth($width) {
        $this->width = $this->validateBoardSize($width);
        if ($this->width < self::BOARD_WIDTH) {
            throw new FootballConfigurationException(FootballConfigurationException::BOARD_WIDTH);
        }
        return $this;
    }

    // ~

    public function getWidth() {
        return (int) $this->width;
    }

    // ~

    private function setHeight($height) {
        $this->height = $this->validateBoardSize($height);
        if ($this->height < self::BOARD_HEIGHT) {
            throw new FootballConfigurationException(FootballConfigurationException::BOARD_HEIGHT);
        }
        return $this;
    }

    // ~

    public function getHeight() {
        return (int) $this->height;
    }

    // ~

    private function validateBoardSize($size) {
        if (!is_numeric($size)) {
            throw new FootballConfigurationException(FootballConfigurationException::NOT_INTEGER);
        }
        if ((boolean) ($size % 2) === false) {
            throw new FootballConfigurationException(FootballConfigurationException::NOT_ODD);
        }
        return $size;
    }

    // ~

    private function setBoard() {
        $this->board = array_fill(0, $this->getHeight(), array_fill(0, $this->getWidth(), 0));

        for ($h = 1; $h <= $this->height - 1; $h++) {
            $this->board[$h][0] = FootballBin::BIN_LEFT;
            $this->board[$h][$this->width - 1] = FootballBin::BIN_RIGHT;
        }

        for ($w = 1; $w <= $this->width - 1; $w++) {
            $this->board[0][$w] = FootballBin::BIN_TOP;
            $this->board[$this->height - 1][$w] = FootballBin::BIN_BOTTOM;
        }

        $this->board[0][0] = FootballBin::BIN_TOP_LEFT;
        $this->board[$this->height - 1][0] = FootballBin::BIN_BOTTOM_LEFT;
        $this->board[0][$this->width - 1] = FootballBin::BIN_TOP_RIGHT;
        $this->board[$this->height - 1][$this->width - 1] = FootballBin::BIN_BOTTOM_RIGHT;


        $this->board[0][(($this->width - 1) / 2) - 1] = FootballBin::BIN_GATE_TOP_LEFT;
        $this->board[0][(($this->width - 1) / 2) + 1] = FootballBin::BIN_GATE_TOP_RIGHT;
        $this->board[0][(($this->width - 1) / 2)] = FootballBin::BIN_GATE_TOP;

        $this->board[($this->height - 1)][(($this->width - 1) / 2) - 1] = FootballBin::BIN_GATE_BOTTOM_LEFT;
        $this->board[($this->height - 1)][(($this->width - 1) / 2) + 1] = FootballBin::BIN_GATE_BOTTOM_RIGHT;
        $this->board[($this->height - 1)][(($this->width - 1) / 2)] = FootballBin::BIN_GATE_BOTTOM;
    }

    // ~

    private function setCurrentUser() {
        if ((boolean) (count($this->getPoints()) % 2) === true) {
            $this->currentUser = self::USER_SECOND;
        } else {
            $this->currentUser = self::USER_FIRST;
        }
    }

    // ~

    public function getCurrentUser() {
        return $this->currentUser;
    }

    // ~

    private function setReflection(FootballPoint $end) {
        $bin = FootballBin::decToBin($this->board[$end->y][$end->x]);
        if (substr_count($bin, '1') >= 2) {
            $this->reflection = true;
        } else {
            $this->reflection = false;
        }
    }

    // ~

    public function getReflection() {
        return (boolean) $this->reflection;
    }

    // ~

    private function setBoardByte($dir, FootballPoint $start, FootballPoint $end) {
        $map = $this->map[$dir];

        // ~

        $arrayBin = FootballBin::decToArrayBin($this->board[$start->y][$start->x]);

        if ($arrayBin[$dir]) {
            throw new FootballException(FootballException::PLACE_OCCUPIED);
        }
        $arrayBin[$dir] = 1;

        $this->board[$start->y][$start->x] = bindec(implode(($arrayBin)));

        // ~

        $arrayBin = FootballBin::decToArrayBin($this->board[$end->y][$end->x]);
        if ($arrayBin[$map]) {
            throw new FootballException(FootballException::PLACE_OCCUPIED);
        }
        $arrayBin[$map] = 1;

        $this->board[$end->y][$end->x] = bindec(implode(($arrayBin)));

        $allOccupiedBin = FootballBin::decToBin($this->board[$end->y][$end->x]);
        if (false == substr_count($allOccupiedBin, '0')) {
            throw new FootballException(FootballException::ALL_PLACE_OCCUPIED);
        }

        $this->setReflection($end);
        $this->setCurrentUser();
    }

    // ~

    private function getBoard() {
        return (array) $this->board;
    }

    // ~

    public function getBoardBinArray() {
        $rows = array();
        foreach ($this->getBoard() as $boardRowKey => $boardRow) {
            foreach ($boardRow as $rowKey => $row) {
                $bin = FootballBin::decToArrayBin($row);
                $rows[$boardRowKey][$rowKey] = $bin;
            }
        }
        return $rows;
    }

    // ~

    public function getBoardCenter() {
        return new FootballPoint(floor($this->getWidth() / 2), floor($this->getHeight() / 2));
    }

    // ~

    private function getDirection(FootballPoint $start, FootballPoint $end) {
        $w = $end->x - $start->x;
        $h = $end->y - $start->y;

        switch (TRUE) {
            case (($w === -1) && ($h === -1)):
                return self::DIRECTION_TOP_LEFT;
                break;
            case (($w === 0) && ($h === -1)):
                return self::DIRECTION_TOP;
                break;
            case (($w === 1) && ($h === -1)):
                return self::DIRECTION_TOP_RIGHT;
                break;
            case (($w === 1) && ($h === 0)):
                return self::DIRECTION_RIGHT;
                break;
            case (($w === 1) && ($h === 1)):
                return self::DIRECTION_BOTTOM_RIGHT;
                break;
            case (($w === 0) && ($h === 1)):
                return self::DIRECTION_BOTTOM;
                break;
            case (($w === -1) && ($h === 1)):
                return self::DIRECTION_BOTTOM_LEFT;
                break;
            case (($w === -1) && ($h === 0)):
                return self::DIRECTION_LEFT;
                break;
        }

        throw new FootballException(FootballException::BAD_DIRECTION);
    }

    // ~

    private function checkGameEnd() {
        if ($this->board[0][(($this->width - 1) / 2)] !== FootballBin::BIN_GATE_TOP) {
            throw new FootballException(FootballException::GOAL_TOP);
        }
        if ($this->board[($this->height - 1)][(($this->width - 1) / 2)] !== FootballBin::BIN_GATE_BOTTOM) {
            throw new FootballException(FootballException::GOAL_BOTTOM);
        }
    }

    // ~

    public function move(FootballPoint $point) {
        $lastPoint = $this->getLastPoint();
        if (is_numeric($dir = $this->getDirection($lastPoint, $point))) {
            $this->setBoardByte($dir, $lastPoint, $point);
            $this->addPoint($point);
            $this->checkGameEnd();
        }
    }

    private function addPoint(FootballPoint $point) {
        return $this->points[] = $point;
    }

    public function getPoints() {
        return (array) $this->points;
    }

    /**
     * @return FootballPoint
     */
    private function getLastPoint() {
        if (!isset($this->points[\count($this->points) - 1])) {
            $this->addPoint($this->getBoardCenter());
        }
        return $this->points[\count($this->points) - 1];
    }

}
