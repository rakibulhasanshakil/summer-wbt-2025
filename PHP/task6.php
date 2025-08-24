<?php
$value = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
$search = 5;
$found = false;

foreach ($value as $element) {
    if ($element == $search) {
        $found = true;
        break;
    }
}

if ($found) {
    echo "$search is found in the array.";
} else {
    echo "$search is not found in the array.";
}
?>
