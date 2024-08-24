<?php

if (file_exists("../../../../wp-load.php"))
	{
		require_once("../../../../wp-load.php");
	}
// Function to send a request to OpenAI's API
function eazyai_openai_request($prompt) {

   global $wpdb;
   
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
   
   $arr = array(); 
   $sql = "select * from ".$wpdb->prefix ."posts WHERE 
               $whr_str
    AND post_status='publish'";
   $res  = $wpdb->get_results($sql);
 $message = array();
 $message[] = array("role" => "system", "content" => "I prefer same data");
 foreach($res as $key=>$value){   
   $message[] = array("role" => "user", "content" => str_replace('"','\"',(string)$res[$key]->post_title));
   $message[] = array("role" => "assistant", "content" => str_replace('"','\"',(string)$res[$key]->post_content));
 }
 
 $message[] = array("role" => "user", "content" => $prompt);

    $apiKey = $settings[0]->api_key; // Replace 'your-api-key' with your actual API key
    $data = array(
        'model' => $settings[0]->model,
		'temperature'=>1,
		  'max_tokens'=>256,
		  'top_p'=>1,
		  'frequency_penalty'=>0,
		  'presence_penalty'=>0,
        'messages' =>$message
			);

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.openai.com/v1/chat/completions",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer $apiKey"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return "cURL Error #:" . $err;
    } else {
        return json_decode($response, true)['choices'][0]['message']['content'];
    }
}

// Example usage
//ini_set('allow_url_fopen',1);
$obj = json_decode(file_get_contents("php://input"));
$user_input =   $obj->msg;;
$response = openai_request($user_input);
echo  json_encode(array('msg'=>$response));

// Example usage
/*$user_input = $_POST['msg'];
$response = openai_request($user_input);
echo "<br>Bot: " . $response;
*/