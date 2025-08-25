<?php

$principal = 103045;  
$rate = 5;
$time = 3;

$simpleInterest = ($principal * $rate * $time) / 100;
echo "Principal Amount: $principal <br>";
echo "Rate of Interest: $rate % <br>";
echo "Time: $time years <br>";
echo "Simple Interest = $simpleInterest";

?>