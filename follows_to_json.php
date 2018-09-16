<?php

if (!isset($argv[2])) {
    echo "Insufficient arguments\n";
    return;
}

$target_user_id = $argv[1];
$client_id = $argv[2];
$pprint = isset($argv[3]);

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => [
        "Accept: application/vnd.twitchtv.v5+json",
        "Client-ID: ${client_id}"
    ],
    CURLOPT_RETURNTRANSFER => true
]);

$url = "https://api.twitch.tv/kraken/users/${target_user_id}/follows/channels"
    . "?direction=asc"
    . "&limit=100";
$offset = 0;
$follows = [];

do {
    curl_setopt($ch, CURLOPT_URL, "${url}&offset=${offset}");
    $follow_data = json_decode(curl_exec($ch), true);

    foreach ($follow_data["follows"] as $follow) {
        $follows[] = $follow;
    }

    $offset += count($follow_data["follows"]);
} while ($offset < $follow_data["_total"]);

$fname = "${target_user_id}_" . date("Y-m-d_H-i-s") . ".json";
$handle = fopen($fname, "x");
$success = fwrite($handle, json_encode($follows, $pprint ? JSON_PRETTY_PRINT : 0));
fclose($handle);

echo ($success ? "follows written to ${fname}" : "failed to write to file") . "\n";

