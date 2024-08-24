<?php
  global $wpdb;
  $cmd='';
  $id = '';
  $cmd = isset($_REQUEST['cmd'])?$_REQUEST['cmd']:'';
  $id = isset($_REQUEST['id'])?$_REQUEST['id']:'';
  
  switch($cmd){
	case "save":
	         $created_at = "";
			 $updated_at = "";

			if($id<=0){
				 $created_at = date("Y-m-d H:i:s");
			 }
			else if($id>0){
				 $updated_at = date("Y-m-d H:i:s");
			 }
            
			$params = array(
			                'front_visiblity' => $_REQUEST['front_visiblity'],
							'api_key' => $_REQUEST['api_key'],
							'model' => $_REQUEST['model'],
							'job_id' => $_REQUEST['job_id'],
							'selected_post_type' => json_encode($_REQUEST['selected_post_type']),
							'created_at' =>$created_at,
							'updated_at' =>$updated_at,
							
							);
			
			 
			if($id>0){
			unset($params['created_at']);
			}if($id<=0){
			unset($params['updated_at']);
			} 
			if($id<=0){
			$res = $wpdb->insert($wpdb->prefix ."settings",$params);
			}
			if($id>0){
			
			 $res = $wpdb->update($wpdb->prefix ."settings",$params,array('id'=>$_REQUEST['id']));
			 
			}
			 ob_start();
             ob_end_flush();
			 echo "<script>";
			  echo "window.location.href = 'admin.php?page=settings';";
			 echo "</script>";
	      break;
	/*case "delete":  
	      $wpdb->delete($wpdb->prefix ."chatbot",array('id'=>$_REQUEST['id']));
		   ob_start();
           ob_end_flush();
		   echo "<script>";
			  echo "window.location.href = 'admin.php?page=settings';";
			 echo "</script>";
	      break;  
    case "edit":
	         if(!empty($_REQUEST['id'])){
		     	$settings  = $wpdb->get_results("select * from ".$wpdb->prefix ."settings where id='".$_REQUEST['id']."'"); 
			 }
			 include(dirname(__FILE__) . '/template/admin/settings/form.php');  
		  break;*/		  
	default:
	   $settings  = $wpdb->get_results("select * from ".$wpdb->prefix ."settings  ORDER BY id DESC"); 
	   include(dirname(__FILE__) . '/template/admin/settings/form.php');  
  }
?>