<?php

list($source, $characters, $lower, $symbols) = parse_args($argv);

$res = fopen($source,'r');
if ($res===false) die();

$tab = array();
$c=0;
while (true)
{
    $c++;
    if ($c%100000==0)
      fwrite(STDERR, $c."  \r");
    $line = fgets($res);
    if ($line===false) break;

    $h = explode("\t",trim($line));
    countWords($h[2]);
}

arsort($tab);
foreach ($tab as $word=>$cnt)
  echo $word."\t".$cnt.PHP_EOL;

function countWords($s)
{
    global $tab, $characters, $symbols, $lower;

    if ($lower) $s = mb_strtolower($s);
    $s = str_replace($symbols,' ',$s);

    $h = $characters?mb_str_split($s):explode(' ',$s);
    foreach ($h as $w)
      if (strlen($w)>0 && $w!=' ')
        @$tab[$w]++;
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

        if ($source!==false) usage('extra argument given: "'.$first.'"');

        $source = $first;
    }

    if ($source===false) usage('No input file given');

    return [$source, $characters, $lower, $symbols];
}

function usage($error = false)
{
    if ($error!==false)
      fwrite(STDERR,'Error: '.$error.PHP_EOL.PHP_EOL);
    fwrite(STDERR,'Usage: php count_words.php [-c] [-l] [-s separation_symbols ] inputfile'.PHP_EOL);
    die();
}

?>
