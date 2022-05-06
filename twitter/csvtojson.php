<?php 
$filePath = 'dataset/';
for ($i=1; $i < 51; $i++) { 
	echo "comment this code to generate json files";
	exit();
	$count = $i;
	$file = $filePath."output".$count.".csv";
	if (($handle = fopen($file, "r")) !== FALSE) {
	    $csvs = [];
	    while(! feof($handle)) {
	       $csvs[] = fgetcsv($handle);
	    }
	    $datas = [];
	    $column_names = [];
	    foreach ($csvs[0] as $single_csv) {
	        $column_names[] = $single_csv;
	    }
	    foreach ($csvs as $key => $csv) {
	        if ($key === 0) {
	            continue;
	        }
	        foreach ($column_names as $column_key => $column_name) {
	            $datas[$key-1][$column_name] = $csv[$column_key];
	        }
	    }
	    $json = json_encode($datas);
	    fclose($handle);
	    // print_r($json);
	    echo "json ".$count." saved!";
	    echo "<br/>";
		$abs_path = __DIR__ .'/'.$filePath.'/json'.$count.'.json';
		$fp = fopen($abs_path, 'w');
		fwrite($fp, $json);
		fclose($fp);
		chmod($filename, 0777);
	}
}
?>