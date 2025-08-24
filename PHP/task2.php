<?php
$amnt = 50420;
$vat = $amnt * 0.15;

echo "Amount = $amnt<br>";
echo "VAT (15%) = $vat<br>";
echo "Total with VAT = " . ($amnt + $vat) . "<br>";
?>