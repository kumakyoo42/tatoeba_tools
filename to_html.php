<?php

$filename = parse_args($argv);

$res = fopen($filename,'r');
if ($res===false) die();

echo '<!DOCTYPE html>'.PHP_EOL;
echo '<ol>'.PHP_EOL;
$c=0;
while (true)
{
    $c++;
    if ($c%10000==0)
      fwrite(STDERR, $c."  \r");
    $line = fgets($res);
    if ($line===false) break;

    $h = explode("\t",trim($line));
    echo '<li><a href="https://tatoeba.org/de/sentences/show/'.$h[0].'">'.$h[0].' '.$h[2].'</a>'.PHP_EOL;
}
echo '</ol>'.PHP_EOL;

function parse_args($a)
{
    if (count($a)!=2) usage('Wrong number of parameters');

    return $a[1];
}

function usage($error = false)
{
    if ($error!==false)
      fwrite(STDERR,'Error: '.$error.PHP_EOL.PHP_EOL);
    fwrite(STDERR,'Usage: php to_html.php filename'.PHP_EOL);
    die();
}

?>
