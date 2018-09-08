<?php

if (!isset($argv[3])) {
    echo "Insufficient arguments";
    return;
}

$client_user_id = $argv[1];
$client_id = $argv[2];
$oauth_code = $argv[3];

$ch = curl_init();


const REQUEST_MAX_FOLLOWS = 100;

// get all followed users
curl_setopt_array($ch, [
    CURLOPT_URL => "https://api.twitch.tv/kraken/users/${client_user_id}/follows/channels?limit=" . REQUEST_MAX_FOLLOWS,
    CURLOPT_HTTPHEADER => [
        "Accept: application/vnd.twitchtv.v5+json",
        "Client-ID: ${client_id}"
    ],
    CURLOPT_RETURNTRANSFER => true
]);

$follows = json_decode(curl_exec($ch), true)["follows"];
$followed_ids = [];

foreach ($follows as $follow) {
    $followed_ids[] = $follow["channel"]["_id"];
}

curl_reset($ch);


// unfollow all users
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => [
        "Accept: application/vnd.twitchtv.v5+json",
        "Client-ID: ${client_id}",
        "Authorization: OAuth ${oauth_code}"
    ],
    CURLOPT_CUSTOMREQUEST => "DELETE",
    CURLOPT_RETURNTRANSFER => true
]);

$unfollow_url = "https://api.twitch.tv/kraken/users/${client_user_id}/follows/channels/";
$unfollow_responses = [];

foreach ($followed_ids as $id) {
   curl_setopt($ch, CURLOPT_URL, "${unfollow_url}${id}");
   $unfollow_responses[] = json_decode(curl_exec($ch), true);
}

echo json_encode($unfollow_responses, JSON_PRETTY_PRINT) . "\n";

