<?php

list($source, $lang, $links, $withfn, $withoutfn) = parse_args($argv);

$l1 = readSentences('1',$source);
$l2 = readSentences('2',$lang);

$res = fopen($links,'r');
if ($res===false) die();

$con = array();
$c=0;
while (true)
{
    $c++;
    if ($c%1000000==0)
      fwrite(STDERR, 'L:'.$c."  \r");
    $line = fgets($res);
    if ($line===false) break;
    $h = explode("\t",trim($line));

    $a = intval($h[0]);
    $b = intval($h[1]);
    if (isset($l1[$a]) && isset($l2[$b]))
      $con[$a][] = $b;
}
fclose($res);

$with = fopen($withfn,'w');
$without = fopen($withoutfn,'w');
foreach ($l1 as $nr=>$s)
{
    if (isset($con[$nr]))
    {
        fwrite($with,$s);
        foreach ($con[$nr] as $n2)
          fwrite($with,'  '.$l2[$n2]);
    }
    else
      fwrite($without,$s);
}
fclose($with);
fclose($without);

function readSentences($short,$filename)
{
    $res = fopen($filename,'r');
    if ($res===false) die();

    $c=0;
    $ret = array();
    while (true)
    {
        $c++;
        if ($c%10000==0)
          fwrite(STDERR, $short.':'.$c."  \r");
        $line = fgets($res);
        if ($line===false) break;

        $h = explode("\t",trim($line));
        $ret[$h[0]] = $line;
    }
    fclose($res);

    return $ret;
}

function parse_args($a)
{
    if (count($a)!=6) usage('Wrong number of parameters');

    array_shift($a);
    return $a;
}

function usage($error = false)
{
    if ($error!==false)
      fwrite(STDERR,'Error: '.$error.PHP_EOL.PHP_EOL);
    fwrite(STDERR,'Usage: php add_translation.php inputfile langfile linkfile withfile withoutfile '.PHP_EOL);
    die();
}

?>
