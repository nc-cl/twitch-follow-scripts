<?php

if (!isset($argv[4])) {
    echo "Insufficient arguments";
    return;
}

$target_user_id = $argv[1];
$client_user_id = $argv[2];
$client_id = $argv[3];
$oauth_code = $argv[4];

$ch = curl_init();


const REQUEST_MAX_FOLLOWS = 100;

// get followed users by the target user
curl_setopt_array($ch, [
    CURLOPT_URL => "https://api.twitch.tv/kraken/users/${target_user_id}/follows/channels?direction=asc&limit=" . REQUEST_MAX_FOLLOWS,
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


// follow followed users
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => [
        "Accept: application/vnd.twitchtv.v5+json",
        "Client-ID: ${client_id}",
        "Authorization: OAuth ${oauth_code}"
    ],
    CURLOPT_PUT => true,
    CURLOPT_RETURNTRANSFER => true
]);

$follow_url = "https://api.twitch.tv/kraken/users/${client_user_id}/follows/channels/";
$follow_responses = [];

foreach ($followed_ids as $id) {
   curl_setopt($ch, CURLOPT_URL, "${follow_url}${id}");
   $follow_responses[] = json_decode(curl_exec($ch), true);
}

echo json_encode($follow_responses, JSON_PRETTY_PRINT) . "\n";

