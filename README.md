# Tools for dealing with download files from Tatoeba

[Tatoeba](https://tatoeba.org), a website collecting sentences and
translations for learning foreign languages, provides several files
for [download](https://tatoeba.org/downloads). The programs here are
small helpers for working with these files.

The downloaded files are packed (currently tar with bzip). Before they
can be processed by the programs listed below, they need to be
unpacked. Using Linux/Unix you can do that with `tar xjf
file.tar.bz2`. After being unpacked, they should be in a format that
is called "tab separated values" (TSV) without a header line. For
unknown reasons they are named `.csv` (comma separated values) instead
of `.tsv`.

## `extract_sentences.php`

The file with all sentences is quite large. Working with smaller files
is more convenient. Thus this program extracts some sentences (based
on language and user) and writes them to stdout. For example `php
extract_sentences.php -l ell -u kumakyoo sentences.csv >
greek_kumakyoo.csv` extracts all the greek sentences I (=user
kumakyoo) added to Tatoeba and writes them to `greek_kumakyoo.csv`.

Can deal with `sentences.csv` as well as with `sentences_detailed.csv`.

Usage:

```
php extract_sentences.php [-l lang] [-u user] inputfile
```

