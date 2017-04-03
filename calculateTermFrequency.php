<?php

if(!isset($argv[1]))
{
    exit('Usage: calculateTermFrequency.php <tweetsfile>');
}

$tweets_file = file_get_contents($argv[1]);

$tweets = json_decode($tweets_file, true);
$tweets = $tweets['statuses'];

$term_occurrences = [];
$total_occurrences = 0;
foreach($tweets as $tweet)
{
    //Split text into individual words, using punctuation and whitespace.
    $tweet_words = preg_split('/((^\p{P}+)|(\p{P}*\s+\p{P}*)|(\p{P}+$))/', $tweet['text'], -1, PREG_SPLIT_NO_EMPTY);
    foreach($tweet_words as $word)
    {
        if(!array_key_exists($word, $term_occurrences))
        {
            $term_occurrences[$word] = 0;
        }
        $term_occurrences[$word] += 1;
        $total_occurrences += 1;
    }
}

//Divide each term's occurrences by the total number of term occurrences.
foreach($term_occurrences as $term => $frequency)
{
    $term_occurrences[$term] = $frequency / $total_occurrences;
}

//Sort terms by frequency (descending)
arsort($term_occurrences, SORT_NUMERIC);

$output = fopen('termfrequency.txt', 'w+');
foreach($term_occurrences as $term => $frequency)
{
    fwrite($output, "{$term} {$frequency}\r\n");
}
fclose($output);