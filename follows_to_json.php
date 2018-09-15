<?php

if (!isset($argv[2])) {
    echo "Insufficient arguments\n";
    return;
}

$target_user_id = $argv[1];
$client_id = $argv[2];

$ch = curl_init();


const REQUEST_MAX_FOLLOWS = 100;

curl_setopt_array($ch, [
    CURLOPT_URL => "https://api.twitch.tv/kraken/users/${target_user_id}/follows/channels?direction=asc&limit=" . REQUEST_MAX_FOLLOWS,
    CURLOPT_HTTPHEADER => [
        "Accept: application/vnd.twitchtv.v5+json",
        "Client-ID: ${client_id}"
    ],
    CURLOPT_RETURNTRANSFER => true
]);

$fname = "${target_user_id}_" . date("Y-m-d_H-i-s") . ".json";
$handle = fopen($fname, "x");
$success = fwrite($handle, curl_exec($ch));
fclose($handle);

echo "follows written to ${fname}\n";

