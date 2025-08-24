<?php
$x = 100;
$y = 200;
$z = 150;

if ($x >= $y && $x >= $z) {
    echo "Largest Number is: $x";
} elseif ($y >= $x && $y >= $z) {
    echo "Largest Number is: $y";
} else {
    echo "Largest Number is: $z";
}
?>
