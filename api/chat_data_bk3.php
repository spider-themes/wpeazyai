<?php
	   
 phpinfo();
////define('WP_USE_THEMES', false);
	   if (file_exists("../../../../wp-load.php"))
		{
			require_once("../../../../wp-load.php");
		}
		
		
	   global $wpdb;
		
	   ob_clean();
  
	   $msg = strtolower(trim($_POST['msg']));

        $arrInput = explode(" ", $msg);
		
		$str = " ";
		for($i=0;$i<count($arrInput);$i++){
			$str .= " AND post_title Like '%".$arrInput[$i]."%'"; 
		}
		
        // debug($arrInput);
        $arr = array(); 
        $res  = $wpdb->get_results("select * from ".$wpdb->prefix ."posts WHERE `post_type`='docs' $str");
		foreach($res as $key=>$value){
		  $arr[$key]['answer'] = $res[$key]->post_content;	
		  $arr[$key]['question'] = $res[$key]->post_title;	
		}

        $arrCount = array();
		$match = array();

        for ($i = 0; $i < count((array)$arr); $i ++) {
            $question = strtolower($arr[$i]['question']);
            $arrQuestion = explode(" ", $question);
            $arrCount[$i] = 0;
            // debug($arrQuestion);
            for ($j = 0; $j < count((array)$arrInput); $j ++) {
                for ($k = 0; $k < count((array)$arrQuestion); $k ++) {
                    if ($arrInput[$j] == $arrQuestion[$k]) {
                        $arrCount[$i] = $arrCount[$i] + 1;
						$match[$i] = 'yes';
                    }
                }
            }
            // $answer = strtolower($arr[$i]['answer']);
        }
        // debug($arrCount);
        $answer ="";
        if (array_sum($arrCount) == 0) {
            echo "Sorry I can't recognize you.Please provide a bit more details"."<br>";
            exit();
        } else {
            $max = $arrCount[0];
            $indicate = 0;
            for ($i = 0; $i < count((array)$arrCount); $i ++) {
                /*if ($arrCount[$i] > $max) {
                    $max = $arrCount[$i];
                    $indicate = $i;
                }*/
				if($match[$i] == 'yes'){
					$answer .= "->".$arr[$i]['answer']."<br>";
				}
            }
            //echo $arr[$indicate]['answer'];
			echo $answer;
            exit();
        }
  ?>  