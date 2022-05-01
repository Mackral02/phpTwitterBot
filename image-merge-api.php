<?php
	// echo "<pre>";
	// print_r($_POST);exit();
	$twitterCharacterLimit = 270;
	$imageBackgroundURL = 'https://picsum.photos/800/800.jpg';
	$responseJSON = [];
	$quotesArray = $_POST;
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
					$mediaArray = ['media' => $filename];
					$path = $filename;
					$type = pathinfo($path, PATHINFO_EXTENSION);
					$data = file_get_contents($path);
					$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
					$responseJSON['data-img'] = $base64;
					if (file_exists($filename)){
					    if(unlink($filename)){
					    	// $responseJSON['imageFileStatus'] = "File deleted";
					    }
					}else{
					    $responseJSON['imageFileStatus'] = "File does not exist";
					}
				}else{
					$responseJSON['error'] = 'no quote/s received';
				}
			}
		// }
		
	}else{
		$responseJSON['error'] = 'json/array is empty';
	}
	header('Content-type: application/json');
	echo json_encode($responseJSON);
	// echo "</pre>";
?>