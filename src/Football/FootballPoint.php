<?php

namespace Football;

/**
 * Description of FootballPoint
 *
 * @author mcmaroon
 */
class FootballPoint {

    public $x;
    public $y;

    /**
     *
     * @param type $x
     * @param type $y
     */
    public function __construct($x, $y) {
        $this->x = (int) $x;
        $this->y = (int) $y;
    }

}
