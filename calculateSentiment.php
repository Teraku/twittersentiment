<?php

if(!isset($argv[1]) || !isset($argv[2]))
{
    exit('Usage: calculateSentiment.php <sentimentFile> <tweetFile>');
}

$sentiments_file = fopen($argv[1], 'r');
$tweets_file = file_get_contents($argv[2]);

$sentiments = [];

//Read file line by line until end
while(($line = fgets($sentiments_file)) !== false)
{
    $sentiment = explode("\t", $line);
    $sentiments[$sentiment[0]] = (int)$sentiment[1];
}

fclose($sentiments_file);

$tweets = json_decode($tweets_file, true);

$output_file = fopen('tweetscores.txt', 'w+');

foreach($tweets['statuses'] as $tweet)
{
    $score = 0;

    //Split text into individual words, using punctuation and whitespace.
    $tweet_words = preg_split('/((^\p{P}+)|(\p{P}*\s+\p{P}*)|(\p{P}+$))/', $tweet['text'], -1, PREG_SPLIT_NO_EMPTY);
    foreach($tweet_words as $word)
    {
        if(array_key_exists($word, $sentiments))
        {
            $score += $sentiments[$word];
        }
    }
    fwrite($output_file, $score."\r\n");
}

fclose($output_file);

echo "\r\nTweet scores successfully written to tweetscores.txt.";