<?php

array_shift($argv);
$s = implode(' ',$argv);

$az = mb_strlen($s);
for ($i=0;$i<$az;$i++)
{
    $c = mb_substr($s,$i,1);
    printf('%04X %5d >%s<'.PHP_EOL,mb_ord($c),mb_ord($c),$c);
}

?>
