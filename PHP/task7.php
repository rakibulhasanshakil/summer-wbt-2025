<?php
for ($i=1; $i<=3; $i++){
    for($j=1; $j<=$i $j++){
        echo "* ";
    }

}

$cont = 1;
for ($i = 3; $i >= 1; $i--) {
    for ($j = 1; $j <= $i; $j++) {
        echo $cont . " ";
        $cont++;
    }
    echo "<br>";
}


$charecter = 'A';
for ($i = 1; $i <= 3; $i++) {
    for ($j = 1; $j <= $i; $j++) {
        echo $ch . " ";
        $charecter++;
    }
    echo "<br>";
}
?>

