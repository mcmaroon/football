<?php

namespace Football;

/**
 * Description of FootballBin
 *
 * @author mcmaroon
 */
class FootballBin {

    /**
     * 1 1 1
     * 1 x 1
     * 1 1 0
     */
    const BIN_TOP_LEFT = 247;

    /**
     * 1 1 1
     * 1 x 1
     * 0 0 0
     */
    const BIN_TOP = 241;

    /**
     * 1 1 1
     * 1 x 1
     * 0 1 1
     */
    const BIN_TOP_RIGHT = 253;

    /**
     * 1 1 0
     * 1 x 0
     * 1 1 0
     */
    const BIN_LEFT = 199;

    /**
     * 0 1 1
     * 0 x 1
     * 0 1 1
     */
    const BIN_RIGHT = 124;

    /**
     * 1 1 0
     * 1 x 1
     * 1 1 1
     */
    const BIN_BOTTOM_LEFT = 223;

    /**
     * 0 0 0
     * 1 x 1
     * 1 1 1
     */
    const BIN_BOTTOM = 31;

    /**
     * 0 1 1
     * 1 x 1
     * 1 1 1
     */
    const BIN_BOTTOM_RIGHT = 127;

    /**
     * 1 1 1
     * 1 x 0
     * 0 0 0
     */
    const BIN_GATE_TOP_LEFT = 225;

    /**
     * 1 1 1
     * 0 x 0
     * 0 0 0
     */
    const BIN_GATE_TOP = 224;

    /**
     * 1 1 1
     * 0 x 1
     * 0 0 0
     */
    const BIN_GATE_TOP_RIGHT = 240;

    /**
     * 0 0 0
     * 1 x 0
     * 1 1 1
     */
    const BIN_GATE_BOTTOM_LEFT = 15;

    /**
     * 0 0 0
     * 0 x 0
     * 1 1 1
     */
    const BIN_GATE_BOTTOM = 14;

    /**
     * 0 0 0
     * 0 x 1
     * 1 1 1
     */
    const BIN_GATE_BOTTOM_RIGHT = 30;

    public static function decToArrayBin($dec) {
        return str_split(self::decToBin($dec));
    }

    public static function decToBin($dec) {
        return str_pad(decbin($dec), 8, 0, STR_PAD_LEFT);
    }

}
