<?php

	// function createImage(){
		// echo "got quote";
		$targetFolder = '/cron-jobs/twitter/';
		$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;

	    $outputImage = imagecreatetruecolor(500, 500);

	    $bg = imagecreatefromjpeg($imageBackgroundURL);
	    $overlay = imagecreatefrompng($targetPath.'image-overlay.png');
	    //imagecopyresized(resource $dst_image,	resource $src_image, int $dst_x, int $dst_y, int $src_x, int $src_y,	int $dst_w, 		int $dst_h, 		int $src_w, 			int $src_h )
	    imagecopyresized($outputImage,			$bg,				 0,			  0,			0,			0, 			500, 					500, 800, 				800 				);
	    imagecopyresized($outputImage,			$overlay,				 0,			  0,			0,			0, 			500, 				500, 				50, 					50);

	    $white = imagecolorclosest($outputImage, 255, 255, 255);
	    $black = imagecolorclosest($outputImage, 0, 0, 0);
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
	    // $fontRoboto = 'twitter/Roboto-Regular.ttf';
	    $fontRoboto = 'twitter/Playball-Regular.ttf';
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
	    imagettftext($outputImage, 7, 0, 165, 492, $white, $fontRoboto, "©Copyrights @i_am_mackral");

	    $imgname = round(microtime(true)).'.jpeg';
	    $filename = $targetPath .$imgname;
	    imagejpeg($outputImage, $filename);
	    chmod($filename, 0777);
	    $file_size = filesize($filename);
		$mimeType = getimagesize($filename)['mime'];
		// print_r($mimeType);
	    imagedestroy($outputImage);

	
	// }

?>