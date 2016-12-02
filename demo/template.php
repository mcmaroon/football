<?php

$output = '';
$newrows = $football->getBoardBinArray();
$output .= '<div class="football">';
for ($y = 0; $y < $football->getHeight(); $y++) {
    $output .= '<div>';
    for ($x = 0; $x < $football->getWidth(); $x++) {
        $output .= '<div class="point" data-x="' . $x . '" data-y="' . $y . '">';
        $output .= '<i class="position">' . $x . '-' . $y . '</i>';
        for ($b = 0; $b <= 7; $b++) {
            $output .= '<i class="bit" data-id="' . $b . '">' . $newrows[$y][$x][$b] . '</i>';
        }
        $output .= '</div>';
    }
    $output .= '</div>';
}
$output .= '</div>';
return $output;
?>