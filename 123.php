<?php

// Added something here
//abc

define('CONSUMER_KEY', '8wU6Z7SvF8BRBL0LcPdxfSH75'); 
define('CONSUMER_SECRET', 'Zwu0cG9IopVBDEgisExhsSZz9LEKyzpLhfQVKxwYWp7YhwvFl6');

$x = base64_encode(rawurlencode(CONSUMER_KEY). ":".rawurlencode(CONSUMER_SECRET));
$url = "https://api.twitter.com/oauth2/token"; 
$header = array(); 
$header[] = "Authorization: Basic $x"; 
$header[] = "Content-Type: application/x-www-form-urlencoded;charset=UTF-8"; 
$curl = curl_init(); 
curl_setopt($curl, CURLOPT_URL, $url); 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_HTTPHEADER, $header); 
curl_setopt($curl, CURLOPT_POST, 1); 
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0); 
curl_setopt($curl, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

$output = curl_exec($curl); 
$result = json_decode($output,true);
$access_token = $result['access_token'];
curl_close($curl);

$query = urlencode("steelers"); //include your query

$search_url = 
"https://api.twitter.com/1.1/search/tweets.json?q=$query&result_type=recent&count=50&lang=en"; //&since=2016-10-23&until=2016-10-24
//retrieve 5 most recent tweets

$output = file_get_contents($search_url,false,stream_context_create(array
(
	    'http'=>array(
		'method'=>"GET",
		'header'=>"Authorization:Bearer $access_token"))));
$result = json_decode($output,true); //results returned as JSON

$result = $result['statuses'];
for ($i=0;$i<count($result);$i++) {
    $id = $result[$i]['id_str']; //tweet id
    $username = $result[$i]['user']['name']; //user posting tweet
    $text = $result[$i]['text']; //tweet text
    $created_at = $result[$i]['created_at']; //tweet post time
    $location = $result[$i]['user']['location']; //tweet location
    //$location = $result[$i]['location'];// tweet location
    
    
    $senurl = "https://community-sentiment.p.mashape.com/text/";
    $sendata = array("txt"=>"$text");
    $header = array();
    $header[] = 'X-Mashape-Key: tkauOXwedGmshdXr1rmc3OE09yl0p1R4SzbjsnD2smREz6oyV5';
    $header[] = 'Content-Type: application/x-www-form-urlencoded';
    $header[] = 'Accept: application/json';

    $fields_string = '';
    foreach($sendata as $key=>$value) { 
		$fields_string .= $key.'='.$value.'&'; 
    }

    rtrim($fields_string, '&');
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $senurl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_POST, count($sendata));
    curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_string);
    $sentimentdata = curl_exec($curl);
    curl_close($curl);

    $sentimentdata= json_decode($sentimentdata,true);
    $confidence = $sentimentdata['result']['confidence'];
    $sentiment = $sentimentdata['result']['sentiment'];
//echo "Number: $i ,";
echo "$id ,";//ID
//echo "UserName: $username ,";
echo "$created_at ,";//Time
echo "$confidence ,";//Confidence
echo "$sentiment ,";// Sentiment
echo "$location ,"; // location of city
echo ","; //if any state
echo ","; //if any country
echo "Text: $text "; //Text
echo "<br>";
}

//echo $confidence
//echo $sentiment;

?>


