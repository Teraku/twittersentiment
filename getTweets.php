<?php

require_once "TwitterAPIExchange.php";

$settings = [
    'oauth_access_token' => "Put your access token here",
    'oauth_access_token_secret' => "Put secret access token here",
    'consumer_key' => "Put consumer key here",
    'consumer_secret' => "Put consumer secret key here"
];

$url = 'https://api.twitter.com/1.1/search/tweets.json';
$getfield = '?q=microsoft&count=500';

$twitter = new TwitterAPIExchange($settings);

echo 'Getting Twitter data...';

$result = $twitter->setGetfield($getfield)
    ->buildOauth($url, 'GET')
    ->performRequest();

file_put_contents('tweets.txt', $result);

echo "\r\nTwitter data successfully written to tweets.txt.";