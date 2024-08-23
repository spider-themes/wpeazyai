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
			                'question' => $_REQUEST['question'],
							'answer'  => $_REQUEST['answer'],
							'created_at' =>$created_at,
							'updated_at' =>$updated_at,
							
							);
			
			 
			if($id>0){
			unset($params['created_at']);
			}if($id<=0){
			unset($params['updated_at']);
			} 
			if($id<=0){
			$res = $wpdb->insert($wpdb->prefix ."chatbot",$params);
			}
			if($id>0){
			
			 $res = $wpdb->update($wpdb->prefix ."chatbot",$params,array('id'=>$_REQUEST['id']));
			 
			}
			 ob_start();
             ob_end_flush();
			 echo "<script>";
			  echo "window.location.href = 'admin.php?page=chatdata';";
			 echo "</script>";
	      break;
	case "delete":  
	      $wpdb->delete($wpdb->prefix ."chatbot",array('id'=>$_REQUEST['id']));
		   ob_start();
           ob_end_flush();
		   echo "<script>";
			  echo "window.location.href = 'admin.php?page=chatdata';";
			 echo "</script>";
	      break;  
    case "edit":
	         if(!empty($_REQUEST['id'])){
		     	$chatdata  = $wpdb->get_results("select * from ".$wpdb->prefix ."chatbot where id='".$_REQUEST['id']."'"); 
			 }
			 include(dirname(__FILE__) . '/template/admin/chatdata/form.php');  
		  break;		  
	default:
	   $chatdata  = $wpdb->get_results("select * from ".$wpdb->prefix ."chatbot  ORDER BY id DESC"); 
	   include(dirname(__FILE__) . '/template/admin/chatdata/index.php');  
  }
?>