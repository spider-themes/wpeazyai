<?php
   session_start();
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  //require 'vendor/autoload.php';
  //use GuzzleHttp\Client;
			
  global $wpdb;
  $cmd='';
  $id = '';
  $cmd = isset($_REQUEST['cmd'])?$_REQUEST['cmd']:'';
  $id = isset($_REQUEST['id'])?$_REQUEST['id']:'';
  
  switch($cmd){
		
		case "import_json_file":
		
		       $settings  = $wpdb->get_results("select * from ".$wpdb->prefix ."settings  ORDER BY id DESC"); 
			   $selected_post_type = json_decode($settings[0]->selected_post_type);
			   $whr_str = "";
			   $kk = 0;
			   foreach($selected_post_type as $key2=>$value2){
								   if($kk ==0){
									$whr_str .= "`post_type`='".$value2."'";	  
									 $kk++;
								   }
								   else{
									   $whr_str .= " OR`post_type`='".$value2."'";
								   }
								   }

			$res  = $wpdb->get_results("select * from ".$wpdb->prefix ."posts WHERE $whr_str  AND post_status='publish'");
			$str = "";
			foreach($res as $key=>$value){
				$c1  = str_replace('"','\"',(string)$res[$key]->post_content);
				$c1  = str_replace("<br>","",$c1);
				$c1  = str_replace("<br/>",'',$c1);
				$c1  = str_replace("\n","",$c1);
				$c1  = str_replace("“",'\“',$c1);
				$c1  = str_replace('”','\”',$c1);
				$c1  = str_replace('/','\/',$c1);
				$c1  = str_replace("\\","",$c1);
				$c1 = str_replace(array("\n", "\r"), '', $c1);
				$c1 = htmlspecialchars($c1);
				
		  
		$str .= '{"messages": [{"role": "system", "content": "I prefer same data"}, {"role": "user", "content": "'.str_replace('"','\"',(string)$res[$key]->post_title).'"}, {"role": "assistant", "content": "'.$c1.'"}]}'."\r\n";
	   }

			 
		$filePath = dirname(__FILE__) . '/template/admin/training/YOURFILENAME.jsonl';	 
		$myfile = fopen($filePath, "w") or die("Unable to open file!");
		fwrite($myfile,$str);
		fclose($myfile);	     



		
			// Set your OpenAI API key
			$settings  = $wpdb->get_results("select * from ".$wpdb->prefix ."settings  ORDER BY id DESC");
			$apiKey = $settings[0]->api_key;
			
			// Instantiate Guzzle client
			//$client = new Client();
			
			// Define file path
			$filePath = dirname(__FILE__) . '/template/admin/training/YOURFILENAME.jsonl';
			$fileContent = file_get_contents($filePath);
			$headers = array();
			$headers[] = "Content-Type: multipart/form-data";
			$headers[] = "Authorization: Bearer ".$apiKey;
			
			$cf = new CURLFile($filePath);
			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/files");
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, ["purpose" => "fine-tune" ,"file" => $cf]);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			
			$result = curl_exec($ch);
			curl_close($ch);
			


			$obj =json_decode($result);
			
			$file_id = $obj->id;

			$_SESSION['file_id'] = $file_id;


			print_r($result );

			include(dirname(__FILE__) . '/template/admin/training/index.php');  
 
			
			exit;
			break;
			
    case "training_file":
	
	            	$settings  = $wpdb->get_results("select * from ".$wpdb->prefix ."settings  ORDER BY id DESC");
			        $apiKey = $settings[0]->api_key;
			
				$endpoint = 'https://api.openai.com/v1/fine_tuning/jobs';
				
				$headers = array();
			$headers[] = "Content-Type: application/json";
			$headers[] = "Authorization: Bearer ".$apiKey;
			
			$data = array(
				"training_file" => $_SESSION['file_id'],//"file-MgXw1U829KazccNhaCXvw3Pd",
				"model" => "gpt-3.5-turbo"
			);
			
			$payload = json_encode($data);
				
				$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, $endpoint);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			
			$result = curl_exec($ch);
			curl_close($ch);
			
			print_r($result );

			include(dirname(__FILE__) . '/template/admin/training/index.php');  
 
	   break;			
	default:
	   include(dirname(__FILE__) . '/template/admin/training/index.php');  
  }
?>