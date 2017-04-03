<?php

if(!isset($argv[1]))
{
    exit('Usage: calculateCommonHashtags.php <tweetsfile>');
}

$tweets_file = file_get_contents($argv[1]);

$tweets = json_decode($tweets_file, true);
$tweets = $tweets['statuses'];

$hashtag_frequency = [];
foreach($tweets as $tweet)
{
    foreach($tweet['entities']['hashtags'] as $hashtag)
    {
        if(!isset($hashtag_frequency[$hashtag['text']]))
        {
            $hashtag_frequency[$hashtag['text']] = 1;
        }
        else
        {
            $hashtag_frequency[$hashtag['text']] += 1;
        }
    }
}

//Slice the array to only include the 10 highest hashtags
arsort($hashtag_frequency);
$hashtag_frequency = array_slice($hashtag_frequency, 0, 10, true);

$output_file = fopen('commonhashtags.txt', 'w+');
foreach($hashtag_frequency as $hashtag => $frequency)
{
    fwrite($output_file, "{$hashtag} {$frequency}\r\n");
}
fclose($output_file);