USERNAME=$1
CLIENTID=$2

curl -H "Accept: application/vnd.twitchtv.v5+json" \
-H "Client-ID: $CLIENTID" \
-X GET "https://api.twitch.tv/kraken/users?login=$USERNAME"

echo ""

