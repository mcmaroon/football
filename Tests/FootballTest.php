<?php

require __DIR__ . '/../vendor/autoload.php';

use Football\Football;
use Football\FootballPoint;
use Football\FootballException;
use Football\FootballConfigurationException;

class FootballTest extends \PHPUnit_Framework_TestCase {

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array()) {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public function testConfiguration() {

        $this->assertInstanceOf(Football::class, new Football());

        $configs = array(
            0 => array(
                array(8, 9),
                array(9, 8),
                array(8, 8),
                array('a', 8),
                array(8, 'a'),
                array('a', 'a'),
                array(3, 9),
                array(9, 3),
                array(null, 9),
                array(9, null),
                array(null, null),
                array(9, '1a'),
                array('1a', 9),
            ),
            1 => array(
                array(9, 11),
                array(11, 13)
            )
        );

        foreach ($configs as $confBool => $config) {
            foreach ($config as $values) {
                if (!$confBool) {
                    try {
                        new Football($values[0], $values[1]);
                    } catch (\Exception $exc) {
                        $this->assertInstanceOf(FootballConfigurationException::class, $exc);
                    }
                }

                if ($confBool) {
                    $this->assertInstanceOf(Football::class, new Football($values[0], $values[1]));
                }
            }
        }
    }

    // ~

    public function testBoard() {
        $f = new Football();
        $this->assertCount($f->getHeight(), $this->invokeMethod($f, 'getBoard'));

        foreach ($this->invokeMethod($f, 'getBoard') as $boardRow) {
            $this->assertCount($f->getWidth(), $boardRow);
        }
    }

    // ~

    public function testBoardCenter() {
        $f = new Football();
        $center = $f->getBoardCenter();
        $this->assertInstanceOf(FootballPoint::class, $center);
        $this->assertEquals(floor(Football::BOARD_WIDTH / 2), $center->x);
        $this->assertEquals(floor(Football::BOARD_HEIGHT / 2), $center->y);
    }

    // ~

    public function testFootballPoint() {
        $this->assertInstanceOf(FootballPoint::class, new FootballPoint(0, 0));
    }

    // ~

    public function testDirection() {
        $f = new Football();
        $center = $f->getBoardCenter();
        $x = $center->x;
        $y = $center->y;

        $directions = [
            [Football::DIRECTION_TOP_LEFT, $x - 1, $y - 1],
            [Football::DIRECTION_TOP, $x, $y - 1],
            [Football::DIRECTION_TOP_RIGHT, $x + 1, $y - 1],
            [Football::DIRECTION_RIGHT, $x + 1, $y],
            [Football::DIRECTION_BOTTOM_RIGHT, $x + 1, $y + 1],
            [Football::DIRECTION_BOTTOM, $x, $y + 1],
            [Football::DIRECTION_BOTTOM_LEFT, $x - 1, $y + 1],
            [Football::DIRECTION_LEFT, $x - 1, $y]
        ];

        foreach ($directions as $v) {
            $this->assertEquals($v[0], $this->invokeMethod($f, 'getDirection', array($center, new FootballPoint($v[1], $v[2]))));
        }
    }

    // ~

    public function testMove() {
        $f = new Football();
        $center = $f->getBoardCenter();
        $x = $center->x;
        $y = $center->y;

        $moves = [
            [$x, $y],
            [$x, $y - 1],
            [$x, $y - 2],
            [$x - 1, $y - 3],
            [$x - 1, $y - 4],
            [$x, $y - 4],
        ];

        foreach ($moves as $k => $v) {
            if (isset($moves[$k + 1])) {
                $start = $moves[$k];
                $end = $moves[$k + 1];
                try {
                    $f->move(new FootballPoint($end[0], $end[1]));
                } catch (\Exception $exc) {
                    $this->assertEquals(FootballException::GOAL_TOP, $exc->getMessage());
                }
            }
        }
    }

}
