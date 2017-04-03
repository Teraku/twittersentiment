<?php

if(!isset($argv[1]) || !isset($argv[2]))
{
    exit('Usage: calculateSentiment.php <sentimentFile> <tweetFile>');
}

$sentiments_file = fopen($argv[1], 'r');
$tweets_file = file_get_contents($argv[2]);

$sentiments = [];

$states = [
    'AL' => 'Alabama',
    'AK' => 'Alaska',
    'AZ' => 'Arizona',
    'AR' => 'Arkansas',
    'CA' => 'California',
    'CO' => 'Colorado',
    'CT' => 'Connecticut',
    'DE' => 'Delaware',
    'DC' => 'District Of Columbia',
    'FL' => 'Florida',
    'GA' => 'Georgia',
    'HI' => 'Hawaii',
    'ID' => 'Idaho',
    'IL' => 'Illinois',
    'IN' => 'Indiana',
    'IA' => 'Iowa',
    'KS' => 'Kansas',
    'KY' => 'Kentucky',
    'LA' => 'Louisiana',
    'ME' => 'Maine',
    'MD' => 'Maryland',
    'MA' => 'Massachusetts',
    'MI' => 'Michigan',
    'MN' => 'Minnesota',
    'MS' => 'Mississippi',
    'MO' => 'Missouri',
    'MT' => 'Montana',
    'NE' => 'Nebraska',
    'NV' => 'Nevada',
    'NH' => 'New Hampshire',
    'NJ' => 'New Jersey',
    'NM' => 'New Mexico',
    'NY' => 'New York',
    'NC' => 'North Carolina',
    'ND' => 'North Dakota',
    'OH' => 'Ohio',
    'OK' => 'Oklahoma',
    'OR' => 'Oregon',
    'PA' => 'Pennsylvania',
    'RI' => 'Rhode Island',
    'SC' => 'South Carolina',
    'SD' => 'South Dakota',
    'TN' => 'Tennessee',
    'TX' => 'Texas',
    'UT' => 'Utah',
    'VT' => 'Vermont',
    'VA' => 'Virginia',
    'WA' => 'Washington',
    'WV' => 'West Virginia',
    'WI' => 'Wisconsin',
    'WY' => 'Wyoming',
];

//Read file line by line until end
while(($line = fgets($sentiments_file)) !== false)
{
    $sentiment = explode("\t", $line);
    $sentiments[$sentiment[0]] = (int)$sentiment[1];
}

fclose($sentiments_file);

$tweets = json_decode($tweets_file, true);

$state_scores = [];

foreach($tweets['statuses'] as $tweet)
{
    $state = '';

    //Check if the state can be determined. If not, skip this tweet.
    if(!isset($tweet['user']['location']))
    {
        continue;
    }

    //Check if any of the words in a user's location correspond to a state (abbreviated or full).
    $location_words = preg_split('/((^\p{P}+)|(\p{P}*\s+\p{P}*)|(\p{P}+$))/', $tweet['user']['location'], -1, PREG_SPLIT_NO_EMPTY);
    if(!$location_words)
    {
        continue;
    }

    foreach($location_words as $word)
    {
        if(array_key_exists($word, $states))
        {
            $state = $states[$word];
        }
        elseif(in_array($word, $states))
        {
            $state = $word;
        }
        else
        {
            continue;
        }
    }

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

    $state_scores[$state] = $score;
}

arsort($state_scores);

reset($state_scores);
$happiest_state = key($state_scores);

file_put_contents('happieststate.txt', $happiest_state);