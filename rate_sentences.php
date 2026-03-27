<?php

list($source, $words, $characters, $lower, $symbols) = parse_args($argv);

$res = fopen($words,'r');
if ($res===false) die();

$fq = array();
$c=0;
while (true)
{
    $c++;
    if ($c%100000==0)
      fwrite(STDERR, "W:".$c."  \r");
    $line = fgets($res);
    if ($line===false) break;

    $h = explode("\t",trim($line));
    $fq[$h[0]] = ceil(log(intval($h[1]),2));
}
fclose($res);


$res = fopen($source,'r');
if ($res===false) die();

$tab = array();
$c=0;
while (true)
{
    $c++;
    if ($c%100000==0)
      fwrite(STDERR, "S:".$c."  \r");
    $line = fgets($res);
    if ($line===false) break;

    $h = explode("\t",trim($line));
    $tab[$h[0]] = array($line,rate($h[2]));
}

uasort($tab,'cmp');

foreach ($tab as $nr=>list($line,$r))
  echo $line;

function cmp($a, $b)
{
    $cnt = min(count($a[1]),count($b[1]));
    for ($i=0;$i<$cnt;$i++)
    {
        if ($a[1][$i]<$b[1][$i]) return 1;
        if ($a[1][$i]>$b[1][$i]) return -1;
    }

    return count($a[1])-count($b[1]);
}

function rate($s)
{
    global $fq, $characters, $symbols, $lower;

    if ($lower) $s = mb_strtolower($s);
    $s = str_replace($symbols,' ',$s);

    $tab = array();
    $h = $characters?mb_str_split($s):explode(' ',$s);
    foreach ($h as $w)
      if (strlen($w)>0 && $w!=' ')
        $tab[] = $fq[$w]??0;

    sort($tab);
    return $tab;
}

function parse_args($a)
{
    array_shift($a);

    $characters = false;
    $lower = false;
    $symbols = array(' ','!','"','#','$','%','&','(',')','*',
                     '+',',','-','.','/',':',';','<','=','>',
                     '0','1','2','3','4','5','6','7','8','9',
                     '?','·','[',']','@','_','»','«','—','–');
    $words = false;
    $source = false;

    while (!empty($a))
    {
        $first = array_shift($a);
        if (strlen($first)==0) continue;

        if ($first[0]=='-')
        {
            if ($first=='-c')
            {
                $characters = true;
                continue;
            }

            if ($first=='-l')
            {
                $lower = true;
                continue;
            }

            if ($first=='-s')
            {
                if (empty($a)) usage('No parameter after -s');
                $symbols = mb_str_split(array_shift($a));
                continue;
            }

            if ($first=='-h') usage();

            usage('unknown option "'.$first.'"');
        }

        if ($words===false)
        {
            $words = $first;
            continue;
        }

        if ($source!==false) usage('extra argument given: "'.$first.'"');
        $source = $first;
    }

    if ($source===false) usage('No input file given');

    return [$source, $words, $characters, $lower, $symbols];
}

function usage($error = false)
{
    if ($error!==false)
      fwrite(STDERR,'Error: '.$error.PHP_EOL.PHP_EOL);
    fwrite(STDERR,'Usage: php rate_sentences.php [-c] [-l] [-s separation_symbols ] wordfile inputfile'.PHP_EOL);
    die();
}

?>
