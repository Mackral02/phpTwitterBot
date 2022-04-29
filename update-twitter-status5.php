<?php
	require_once "twitter-auth-keys.php";
	echo "<pre>";

	$twitterCharacterLimit = 270;
	$twitterDatasetPath = 'twitter/dataset/';
	$twitterCurrentDataset = 'json2.json';
	$imageBackgroundURL = 'https://picsum.photos/800/800.jpg';
	$responseJSON = [];

	$responseArray = json_decode(file_get_contents($twitterDatasetPath.$twitterCurrentDataset, true),true);
	$failedArray = json_decode(file_get_contents($twitterDatasetPath.'failed-json.json', true),true);
	$responseJSON['failedJsonCount'] = count($failedArray);
	$quotesArray = array_pop($responseArray);
	$responseJSON['remainingQuotes'] = count($responseArray);
	$responseArray = json_encode($responseArray);
	// $abs_path = __DIR__ .'/'.$twitterDatasetPath.$twitterCurrentDataset;
	// $fp = fopen($abs_path, 'w');
	// fwrite($fp, $responseArray);
	// fclose($fp);
	if($quotesArray){
		$tweetQuote = $quotesArray['quote'];
		$tweetQuoteAuthor = $quotesArray['author'];
		$category = explode(", ",$quotesArray["category"]);
		if(count($category)>0){
			$hashTags = join(' #',$category);		
			$hashTags = ' #'.$hashTags." #Mackral";		
		}
		$fullTweet = $tweetQuote." ".$hashTags;
		$responseJSON['tweet'] = $fullTweet;
		$responseJSON['tweetLength'] = strlen($fullTweet);
		// if(strlen($fullTweet)> $twitterCharacterLimit){

			$RefFullTweet = $fullTweet;
			$hashTagLength = strlen($hashTags);

			$tweetArray = explode("\n", wordwrap($tweetQuote, $twitterCharacterLimit-$hashTagLength));
			$tweetArrayCount = count($tweetArray);
			foreach ($tweetArray as $key =>$tweet) {
				$fullTweet = '';
				if($tweetArrayCount>1){
					$fullTweet = ($key+1)."/".$tweetArrayCount." - ";
				}
			  	$fullTweet = $fullTweet.$tweet.$hashTags."\n";

			  	if(!empty($tweetQuote)){
					require_once "create-image.php";
					require "vendor/autoload.php";
					$connection = new Abraham\TwitterOAuth\TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
					$content = $connection->get("account/verify_credentials");
					$mediaArray = ['media' => $filename];
					$status = $connection->upload("media/upload", $mediaArray);
					$parameters = [
					    'status' => $fullTweet,
					    'media_ids' => [$status->media_id_string]
					];
					$result = $connection->post('statuses/update', $parameters);
					if (file_exists($filename)){
					    if(unlink($filename)){
					    	$responseJSON['imageFileStatus'] = "File deleted";
					    }
					}else{
					    $responseJSON['imageFileStatus'] = "File does not exist";
					}
				}else{
					$responseJSON['error'] = 'no quote/s received';
				}
				usleep( 2 * 1000 );
			}
			// $quotesArray['tweetLength'] = strlen($fullTweet);
			// array_push($failedArray, $quotesArray);
			// $responseJSON['failedJsonCount'] = count($failedArray);
			// $responseJSON['failedError'] = "Charcter count exceeded ".$twitterCharacterLimit;
			// $failedArray = json_encode($failedArray);
			// $abs_path = __DIR__ .'/'.$twitterDatasetPath.'failed-json.json';
			// $fp = fopen($abs_path, 'w');
			// fwrite($fp, $failedArray);
			// fclose($fp);
		// }
		
	}else{
		$responseJSON['error'] = 'json/array is empty';
	}
	print_r($responseJSON);
	echo "</pre>";

	


	/*<img src="http://admin.gonsalves.xyz/cron-jobs/twitter/<?php echo $imgname; ?>" style="max-width: 100%;">
	<img src="https://picsum.photos/800/800.jpg" style="max-width: 100%;">*/
?>