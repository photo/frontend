<?php

$token = 'EwAoAq1DBAAUGCCXc8wU';

$url = 'https://apis.live.net/v5.0/me/skydrive/files?access_token=' . $token;
print_r($url);
print("\n\n");
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
#curl_setopt($ch, CURLOPT_VERBOSE, true);

if( ! $result = curl_exec($ch))
{
    trigger_error(curl_error($ch));
} 

curl_close($ch);
print_r($result);
?>
