<?php
	require 'libs/LIB_http.php';
	require 'libs/LIB_parse.php';
	
	function download_audio($file)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $file);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_REFERER, $file);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 4);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	
	$result = array();
	
	$url = filter_input(INPUT_POST, "input-link");
	
	$last_position = strripos($url, '/');
	$last_position += 1;
	$url_dir = substr($url, 0, $last_position);
	
	$web_page = http_get($url, $ref = "");
	$web_page = $web_page["FILE"];
	$a_tag = parse_array($web_page, "<a ", ">");
	$count = 0;
	
	foreach($a_tag as $val)
	{
		if(stristr($val, "playAudioBarPlay"))
		{
			$href_str = trim(get_attribute($val, "href"));
			$href_str = str_replace("'", "", $href_str);
			$href_str = str_replace("(", "", $href_str);
			$href_str = str_replace(")", "", $href_str);
			$href_arr = explode(",", $href_str);
			$href_str = str_replace("javascript:playAudioBarPlay", "", $href_arr[0]);
			$audio_file = download_audio($url_dir . 'aumpo/' . $href_str);
			
			if(!file_exists("audio/" . $href_str))
			{
				if(!empty($audio_file))
					file_put_contents("audio/" . $href_str, $audio_file);
				else
					echo "no audio file found";
			}
			
			$result["file_arr"][$count]["file_path"] = "../audio/" . $href_str;
			$count++;
		}
	}
	
	if(empty($result["result"]))
		$result["result"] = "valid link";
	
	echo json_encode($result);
?>