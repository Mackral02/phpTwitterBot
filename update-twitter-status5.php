<?php
	require_once "twitter-auth-keys.php";
	echo "<pre>";


	$responseArray = json_decode(file_get_contents('twitter/dataset/json2.json', true),true);
	$quotesArray = array_pop($responseArray);
	// print_r($responseArray);
	echo "".count($responseArray)." quotes remaining";
	// print_r($quotesArray);
	$responseArray = json_encode($responseArray);
	$abs_path = __DIR__ .'/twitter/dataset/json2.json';
	$fp = fopen($abs_path, 'w');
	fwrite($fp, $responseArray);
	fclose($fp);
	$tweetQuote = $quotesArray['quote'];
	$tweetQuoteAuthor = $quotesArray['author'];
	// if($tweetQuoteAuthor != ''){
	// 	$tweetQuote .= " -".$tweetQuoteAuthor;
	// }
	$category = explode(", ",$quotesArray["category"]);
	if(count($category)>0){
		$hashTags = join(' #',$category);		
		$hashTags = ' #'.$hashTags;		
	}
	$fullTweet = $tweetQuote." ".$hashTags."";
	// echo $fullTweet;


	if(!empty($tweetQuote)){
		// echo "got quote";
		$targetFolder = '/cron-jobs/twitter/';
		$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;

		// $img0 = 'https://images.unsplash.com/photo-1584984793889-6ac1bbe1a2b3';
		$img0 = 'https://picsum.photos/800/800.jpg';
		// $overlay = $targetPath.'image-overlay.png';

	    $outputImage = imagecreatetruecolor(500, 500);

	    // set background to white
	    // $white = imagecolorallocate($outputImage, 255, 255, 255);
	    // imagefill($outputImage, 0, 0, $white);

	    $bg = imagecreatefromjpeg($img0);
	    $overlay = imagecreatefrompng($targetPath.'image-overlay.png');
	    //imagecopyresized(resource $dst_image,	resource $src_image, int $dst_x, int $dst_y, int $src_x, int $src_y,	int $dst_w, 		int $dst_h, 		int $src_w, 			int $src_h )
	    imagecopyresized($outputImage,			$bg,				 0,			  0,			0,			0, 			500, 					500, 800, 				800 				);
	    imagecopyresized($outputImage,			$overlay,				 0,			  0,			0,			0, 			500, 				500, 				50, 					50);

	    $white = imagecolorclosest($outputImage, 255, 255, 255);
	    $black = imagecolorclosest($outputImage, 0, 0, 0);
	    $gray = imagecolorclosest($outputImage, 0x55, 0x55, 0x55);
	    $gray = imagecolorclosest($outputImage, 0x55, 0x55, 0x55);
	    $textFull = $tweetQuote;
	    $words = explode(" ",$textFull);
		$wnum = count($words);
		$line = '';
		$text='';
		$maxwidth=450;
		$font_size=20;
		$line_height=30;
	    // $font = 'twitter/SquarePeg-Regular.ttf';
	    $font = 'twitter/Oswald-Light.ttf';
	    $fontRoboto = 'twitter/Roboto-Regular.ttf';
		for($i=0; $i<$wnum; $i++){
		  $line .= $words[$i];
		  $dimensions = imagettfbbox($font_size, 0, $font, $line);
		  $lineWidth = $dimensions[2] - $dimensions[0];
		  if ($lineWidth > $maxwidth) {
		    $text.=($text != '' ? '|'.$words[$i].' ' : $words[$i].' ');
		    $line = $words[$i].' ';
		  }
		  else {
		    $text.=$words[$i].' ';
		    $line.=' ';
		  }
		}
		$lines = explode("|",$text);
		$j=0;
		for($i=count($lines)-1; $i>=0; $i--){
	    	imagettftext($outputImage, $font_size, 0, 20, (440-($j*$line_height)), $white, $font, $lines[$i]);		
	    	$j++;
		}
		if($tweetQuoteAuthor != 'Anonymous'){
			imagettftext($outputImage, $font_size, 0, 20, 469, $gray, $font, " -".$tweetQuoteAuthor);	
			imagettftext($outputImage, $font_size, 0, 20, 470, $white, $font, " -".$tweetQuoteAuthor);	
		}
		// print_r($lines);
	    // imagettftext($outputImage, 20, 0, 20, 149, $gray, $font, $text);
	    // imagettftext($outputImage, $font_size, 0, 20, 150, $white, $font, $text);
	    imagettftext($outputImage, 7, 0, 165, 492, $white, $fontRoboto, "@Copyrights @i_am_mackral");

	    $imgname = round(microtime(true)).'.jpeg';
	    $filename = $targetPath .$imgname;
	    imagejpeg($outputImage, $filename);
	    chmod($filename, 0777);
	    $file_size = filesize($filename);
		$mimeType = getimagesize($filename)['mime'];
		// print_r($mimeType);
	    imagedestroy($outputImage);
	    // echo substr(decoct(fileperms($filename)), -4);



		require "vendor/autoload.php";
		$connection = new Abraham\TwitterOAuth\TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
		$content = $connection->get("account/verify_credentials");
		$mediaArray = [
			// 'media' => '/twitter/'.$imgname,
			'media' => $filename
			// 'command' => 'INIT',
			// 'total_bytes' => $file_size,
			// 'media_type' => $mimeType,
		];
		$status = $connection->upload("media/upload", $mediaArray);
		// print_r($status);
		// sleep(10);
		$parameters = [
		    'status' => $fullTweet,
		    // 'media_ids' => implode(',', [$status->media_id_string])
		    'media_ids' => [$status->media_id_string]
		];
		$result = $connection->post('statuses/update', $parameters);
		// print_r($result);
		if (file_exists($filename)){
		    if(unlink($filename)){
		       echo "File deleted";
		    }
		}else{
		     echo "File does not exist";
		}
	}else{
		echo "no quote received";
	}
	echo "</pre>";

	


?>
<!-- <img src="http://admin.gonsalves.xyz/cron-jobs/twitter/<?php echo $imgname; ?>" style="max-width: 100%;"> -->
<!-- <img src="https://picsum.photos/800/800.jpg" style="max-width: 100%;"> -->