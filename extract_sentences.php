<?php

list($source, $lang, $user) = parse_args($argv);

$res = fopen($source,'r');
if ($res===false) die();

$c=0;
while (true)
{
    $c++;
    if ($c%10000==0)
      fwrite(STDERR, $c."  \r");
    $line = fgets($res);
    if ($line===false) break;

    $h = explode("\t",trim($line));
    if ($lang!==false && $h[1]!=$lang) continue;
    if ($user!==false && ($h[3]??false)!==$user) continue;
    echo $line;
}

function parse_args($a)
{
    array_shift($a);

    $lang = false;
    $user = false;
    $source = false;

    while (!empty($a))
    {
        $first = array_shift($a);
        if (strlen($first)==0) continue;

        if ($first[0]=='-')
        {
            if ($first=='-l')
            {
                if (empty($a)) usage('No parameter after -l');
                $lang = array_shift($a);
                continue;
            }

            if ($first=='-u')
            {
                if (empty($a)) usage('No parameter after -u');
                $user = array_shift($a);
                continue;
            }

            if ($first=='-h') usage();

            usage('unknown option "'.$first.'"');
        }

        if ($source!==false) usage('extra argument given: "'.$first.'"');

        $source = $first;
    }

    if ($source===false) usage('No input file given');

    return [$source, $lang, $user];
}

function usage($error = false)
{
    if ($error!==false)
      fwrite(STDERR,'Error: '.$error.PHP_EOL.PHP_EOL);
    fwrite(STDERR,'Usage: php extract_sentences.php [-l lang] [-u user] inputfile'.PHP_EOL);
    die();
}

?>
