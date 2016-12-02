<?php

namespace Football;

/**
 * Description of FootballConfigurationException
 *
 * @author mcmaroon
 */
class FootballConfigurationException extends \UnexpectedValueException {

    const BOARD_WIDTH = 'min_board_width';
    const BOARD_HEIGHT = 'min_board_height';
    const NOT_INTEGER = 'width_and_height_must_be_integer';
    const NOT_ODD = 'width_and_height_must_be_odd';

}
