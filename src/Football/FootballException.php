<?php

namespace Football;

/**
 * Description of FootballException
 *
 * @author mcmaroon
 */
class FootballException extends \Exception {

    const PLACE_OCCUPIED = 'place_occupied';
    const ALL_PLACE_OCCUPIED = 'all_place_occupied';
    const BAD_DIRECTION = 'bad_direction';
    const GOAL_TOP = 'goal_top';
    const GOAL_BOTTOM = 'goal_bottom';

}
