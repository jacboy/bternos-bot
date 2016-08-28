<?php
error_reporting(E_ALL);
    function buildBaseString($baseURI, $method, $params) {
        $r = array();
        ksort($params);
        foreach($params as $key=>$value){
            $r[] = "$key=" . rawurlencode($value);
        }
        return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
    }

    function buildAuthorizationHeader($oauth) {
        $r = 'Authorization: OAuth ';
        $values = array();
        foreach($oauth as $key=>$value)
            $values[] = "$key=\"" . rawurlencode($value) . "\"";
        $r .= implode(', ', $values);
        return $r;
    }

    $url = "https://api.twitter.com/1.1/statuses/user_timeline.json";

    $oauth_access_token = "XXX";
    $oauth_access_token_secret = "XXX";
    $consumer_key = "XXX";
    $consumer_secret = "XXX";

    $oauth = array( 'screen_name' => 'USERNAME',
           	    'count' => 1,
		    'oauth_consumer_key' => $consumer_key,
                    'oauth_nonce' => time(),
                    'oauth_signature_method' => 'HMAC-SHA1',
                    'oauth_token' => $oauth_access_token,
                    'oauth_timestamp' => time(),
                    'oauth_version' => '1.0');

    $base_info = buildBaseString($url, 'GET', $oauth);
    $composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
    $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
    $oauth['oauth_signature'] = $oauth_signature;

    // Make requests
    $header = array(buildAuthorizationHeader($oauth), 'Expect:');
    $options = array( CURLOPT_HTTPHEADER => $header,
                      //CURLOPT_POSTFIELDS => $postfields,
                      CURLOPT_HEADER => false,
                      CURLOPT_URL => $url . '?screen_name=USERNAME&count=1',
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_SSL_VERIFYPEER => false);

    $feed = curl_init();
    curl_setopt_array($feed, $options);
    $json = curl_exec($feed);
    curl_close($feed);

    $twitter_data = json_decode($json, true);

    $latesttweet = $twitter_data[0]["text"];

 $tweeted = file_get_contents("lasttweet.txt");
 if ($tweeted == $latesttweet) {
 	exit();
 } else {
 	$f = fopen("lasttweet.txt", "w");
 	fwrite($f, $latesttweet);
 	fclose($f);
 }

include("flip.php");

$tweet = flipstring($latesttweet);
$tweet = unicode_decode($tweet);

require_once('twitter-api-php/TwitterAPIExchange.php');

$settings = array(
    'oauth_access_token' => $oauth_access_token,
    'oauth_access_token_secret' => $oauth_access_token_secret,
    'consumer_key' => $consumer_key,
    'consumer_secret' => $consumer_secret
);

$twitter = new TwitterAPIExchange($settings);
if (isset($twitter_data[0]["entities"]["media"][0]["media_url"])) {
  echo "IMAGE";
  $image = $twitter_data[0]["entities"]["media"][0]["media_url"];
  $file = fopen("image.jpg", "w");
  fwrite($file, file_get_contents($image));
  fclose($file);

  $im = imagecreatefromjpeg("image.jpg");
  imageflip($im, IMG_FLIP_VERTICAL);
  imagejpeg($im, "image.jpg");
  imagedestroy($im);

  $file = file_get_contents('image.jpg');
  $data = base64_encode($file);

  $url = "https://upload.twitter.com/1.1/media/upload.json";
  $method = "POST";
  $params = array(
      "media_data" => $data,
    );


    $json = $twitter
            ->setPostfields($params)
            ->buildOauth($url, $method)
            ->performRequest();

            // Result is a json string
            $res = json_decode($json);
            // Extract media id
            $mediaid = $res->media_id_string;// Send tweet with uploaded image
            echo $mediaid;
}

$url = 'https://api.twitter.com/1.1/statuses/update.json';
$requestMethod = 'POST';

$postfields = array(
    'status' => $tweet
);

if (isset($mediaid)) {
  $postfields['media_ids'] = $mediaid;
}

echo $twitter->buildOauth($url, $requestMethod)
    ->setPostfields($postfields)
    ->performRequest();


?>
