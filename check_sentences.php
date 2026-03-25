<?php

list($defs, $infile, $goodfile, $badfile) = parse_args($argv);

list($symbols, $first, $last, $regex, $goodids, $badids) = readDefinitions($defs);

$in = fopen($infile, 'r');
$good = fopen($goodfile, 'w');
$bad = fopen($badfile, 'w');

$c=0;
while (true)
{
    $c++;
    if ($c%10000==0)
      fwrite(STDERR, $c."  \r");
    $line = fgets($in);
    if ($line===false) break;

    $h = explode("\t",trim($line));

    if (isOK($h[0],$h[2]))
      fwrite($good,$line);
    else
      fwrite($bad,$line);
}

fclose($good);
fclose($bad);
fclose($in);

function isOK($id,$s)
{
    global $goodids, $badids;

    if (in_array($id,$goodids)) return true;
    if (in_array($id,$badids)) return false;

    return correctSymbols($s)
        && correctFirst($s)
        && correctLast($s)
        && correctRegex($s);
}

function correctSymbols($s)
{
    global $symbols;

    $az = mb_strlen($s);
    for ($i=0;$i<$az;$i++)
      if (!in_array(mb_substr($s,$i,1),$symbols))
        return false;

    return true;
}

function correctFirst($s)
{
    global $first;

    return in_array(mb_substr($s,0,1),$first);
}

function correctLast($s)
{
    global $last;

    return in_array(mb_substr($s,-1),$last);
}

function correctRegex($s)
{
    global $regex;

    foreach ($regex as $r)
      if (preg_match($r,$s))
        return false;

    return true;
}

//////////////////////////////////////////////////////////////////

function readDefinitions($name)
{
    $symbols = array();
    $first = array();
    $last = array();
    $regex = array();
    $goodids = array();
    $badids = array();

    $f = file($name);
    $part = 0;
    foreach ($f as $line)
    {
        $line = trim($line);
        if (strlen($line)==0) continue;
        if ($line[0]=='#')
        {
            $part++;
            continue;
        }

        switch ($part)
        {
         case 0: break;
         case 1: addSymbols($symbols,$line); break;
         case 2: addSymbols($first,$line); break;
         case 3: addSymbols($last,$line); break;
         case 4: $regex[] = $line; break;
         case 5: addIDs($goodids,$line); break;
         case 6: addIDs($goodids,$line); break;
         default:
            usage("Definition file contains extra parts");
        }
    }

    return [$symbols, $first, $last, $regex, $goodids, $badids];
}

function addSymbols(&$set, $symbols)
{
    while (mb_strlen($symbols)>0)
    {
        $first = mb_substr($symbols,0,1);
        $symbols = mb_substr($symbols,1);
        if (mb_strlen($symbols)>=2 && mb_substr($symbols,0,1)=='-')
        {
            $last = mb_substr($symbols,1,1);
            for ($i=mb_ord($first);$i<=mb_ord($last);$i++)
              $set[] = mb_chr($i);
            $symbols = mb_substr($symbols,2);
            continue;
        }
        $set[] = $first;
    }
}

function addIDs(&$set, $ids)
{
    $h = explode(',',$ids);
    foreach ($h as $id)
      $set[] = intval(trim($id));
}

function parse_args($a)
{
    if (count($a)!=5) usage('Wrong number of parameters');

    array_shift($a);
    return $a;
}

function usage($error = false)
{
    if ($error!==false)
      fwrite(STDERR,'Error: '.$error.PHP_EOL.PHP_EOL);
    fwrite(STDERR,'Usage: php check_sentences.php definition-file in-file good-file bad-file'.PHP_EOL);
    die();
}

?>
