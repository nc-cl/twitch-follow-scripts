<?php

if (!isset($argv[4])) {
    echo "Insufficient arguments\n";
    return;
}

$fname = $argv[1];
$user_id = $argv[2];
$client_id = $argv[3];
$oauth_code = $argv[4];

$handle = fopen($fname, "r");

if (!$handle) {
    echo "Bad filename\n";
    return;
}

$follows = json_decode(fread($handle, filesize($fname)), true);
fclose($handle);
$followed_ids = [];

foreach ($follows as $follow) {
    $followed_ids[] = $follow["channel"]["_id"];
}

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => [
        "Accept: application/vnd.twitchtv.v5+json",
        "Client-ID: ${client_id}",
        "Authorization: OAuth ${oauth_code}"
    ],
    CURLOPT_PUT => true,
    CURLOPT_RETURNTRANSFER => true
]);

$follow_url = "https://api.twitch.tv/kraken/users/${user_id}/follows/channels/";
$follow_responses = [];

foreach ($followed_ids as $id) {
   curl_setopt($ch, CURLOPT_URL, "${follow_url}${id}");
   $follow_responses[] = json_decode(curl_exec($ch), true);
}

echo json_encode($follow_responses, JSON_PRETTY_PRINT) . "\n";

