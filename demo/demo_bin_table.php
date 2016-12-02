<?php
require __DIR__ . '/../vendor/autoload.php';

if (!class_exists('Symfony\Component\Console\Helper\Table')) {
    die('Run composer install(dev) first');
}

use Football\Football;
use Football\FootballPoint;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\Output;

class TestOutput extends Output {

    public $output = '';

    protected function doWrite($message, $newline) {
        $message = \str_replace(' ', '&nbsp;', $message);
        $this->output .= $message . ($newline ? "</br>" : '');
    }

    public function fetch() {
        $content = $this->output;
        $this->output = '';

        return $content;
    }

}

$output = new TestOutput();

$f = new Football();
$center = $f->getBoardCenter();
$x = $center->x;
$y = $center->y;

$output->writeln('Game start');
$output->writeln('Board Center: x:' . $x . ' y:' . $y);

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
        $output->writeln('Move:' . ($k + 1) . ' Start position: x:' . $start[0] . ' y:' . $start[1] . ' End position: x:' . $end[0] . ' y:' . $end[1]);
        try {
            $f->move(new FootballPoint($end[0], $end[1]));
        } catch (\Exception $exc) {
            $output->writeln('Exception: ' . get_class($exc) . ' Code: ' . $exc->getCode() . ' Message: ' . $exc->getMessage());
        }
    }
}

$output->writeln('Game end');

$table = new Table($output);
$rows = array();

foreach ($f->getBoardBinArray() as $boardRowKey => $boardRow) {
    foreach ($boardRow as $rowKey => $bin) {
        $rows[$boardRowKey][1][] = $bin[0] . '     ' . $bin[1] . '     ' . $bin[2];
        $rows[$boardRowKey][2][] = $bin[7] . ' [' . str_pad(bindec(implode($bin)), 3, " ", STR_PAD_BOTH) . 'x' . $rowKey . 'y' . $boardRowKey . '] ' . $bin[3];
        $rows[$boardRowKey][3][] = $bin[6] . '     ' . $bin[5] . '     ' . $bin[4];
        $rows[$boardRowKey][4] = new TableSeparator();
    }
}

$newrows = [];

foreach ($rows as $r) {
    $newrows[] = $r[1];
    $newrows[] = $r[2];
    $newrows[] = $r[3];
    $newrows[] = $r[4];
}
array_pop($newrows);
$table->setRows($newrows);

$table->render();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">        
        <title>Football</title>
        <style>
            body {
                font-family: monospace;
            }
        </style>
    </head>
    <body>
        <?php echo $output->fetch(); ?>
    </body>
</html>