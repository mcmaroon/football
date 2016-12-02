<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/../vendor/autoload.php';

use Football\Football;
use Football\FootballPoint;

$renderData = array();

$width = (int) filter_input(INPUT_GET, 'width', FILTER_SANITIZE_ENCODED);
$height = (int) filter_input(INPUT_GET, 'height', FILTER_SANITIZE_ENCODED);
$x = (int) filter_input(INPUT_GET, 'x', FILTER_SANITIZE_ENCODED);
$y = (int) filter_input(INPUT_GET, 'y', FILTER_SANITIZE_ENCODED);

$renderData['debug']['width'] = $width;
$renderData['debug']['height'] = $height;
$renderData['debug']['x'] = $x;
$renderData['debug']['y'] = $y;

$football = new Football($width, $height);
$points = (isset($_SESSION["points"]) ? unserialize($_SESSION["points"]) : []);
$renderData['debug']['sessionpoints'] = $points;

if (is_array($points) && count($points) > 1) {
    foreach ($points as $pk => $point) {
        if (isset($points[$pk + 1])) {
            $football->move(new FootballPoint($points[$pk + 1]->x, $points[$pk + 1]->y));
        }
    }
}

$end = new FootballPoint($x, $y);

$renderData['end'] = [$end->x, $end->y];

try {
    $football->move($end);
} catch (\Exception $exc) {
    $renderData['error'] = [
        'code' => $exc->getCode(),
        'message' => $exc->getMessage()
    ];
}

$_SESSION["points"] = serialize($football->getPoints());

$renderData['reflection'] = $football->getReflection();
$renderData['currentuser'] = $football->getCurrentUser();
$renderData['points'] = $football->getPoints();

$renderData['template'] = require_once('./template.php');

if (class_exists('Monolog\Logger')) {
    $log = new \Monolog\Logger('mpc');
    $log->pushHandler(new \Monolog\Handler\StreamHandler('../football.log'));    
    $renderDataDebug = $renderData;
    unset($renderDataDebug['template']);
    $log->debug('football:ajax', $renderDataDebug);
}

header('Content-Type: application/json');
echo json_encode($renderData);
