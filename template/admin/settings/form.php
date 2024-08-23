<?php
 error_reporting(!E_WARNING);
?>
<style>
.form-group {
    margin-bottom: 20px;
}
	
	.h11{
		font-size: 23px;
    font-weight: 400;
    margin: 0;
    padding: 30px 0 20px;
    line-height: 1.3;
	}
	
	input#api_key,input#model,input#job_id {
			width: 37%;
		    padding: 0 8px;
			line-height: 2;
			min-height: 30px;
}
	input#api_key{
					margin-left: 140px;
	}
		input#model{
				margin-left: 148px;
	}
	input#job_id {
		 margin-left: 148px;
	}
	
	label.form-check-label{
		    font-size: 14px;
    color: #1d2327;
      text-align: left;
    padding: 20px 10px 20px 0;
    width: 200px;
    line-height: 1.3;
    font-weight: 600;

	}
	button.btn.btn-success {
		
}
	select#selected_post_type {
    margin-left: 77px;

}
	
	
</style>


<!--<a  href="<?php echo 'admin.php?page=settings'; ?>" class="btn btn-info"><i class="arrow_left"></i> List</a>-->
<h1 class="h11"> Chatbot Settings</h1>
<!--Form to save data-->
<form method="post" action="admin.php?page=settings&cmd=save&id=<?=$settings[0]->id?>" enctype="multipart/form-data">
   <div class="row"> 
      <div class="col">
           <!--<div class="form-group"> 
          <label for="question" class="col-md-8 control-label ">Front visiblity</label> 
          <div class="col-md-8"> 
           <input type="text" name="front_visiblity" value="<?php echo ($_POST['front_visiblity'] ? $_POST['front_visiblity'] : $settings[0]->front_visiblity); ?>" class="form-control" id="front_visiblity" /> 
          </div> 
           </div>-->
           
           
           <div class="form-group"> 
           <div class="form-check form-switch">
              <label class="form-check-label" for="switchCheckDefault" class="ssi">Front visiblity</label>
              <input type="checkbox"  name="front_visiblity"  id="front_visiblity" <?php echo ($settings[0]->front_visiblity=='on' ? 'checked':''); ?> data-toggle="toggle">

              
            </div>
         </div>
		 
		   <div class="form-group"> 
           
              <label class="form-check-label" for="api_key">API Key</label>
              <input type="text"  name="api_key"  id="api_key"  value="<?=$settings[0]->api_key?>">

              
           
         </div>
		 
		  <div class="form-group"> 
          
              <label class="form-check-label" for="model">Modal</label>
              <input type="text"  name="model"  id="model"  value="<?=$settings[0]->model?>">

              
           
         </div>
		 
		 <div class="form-group"> 
          
              <label class="form-check-label" for="job_id">Job Id</label>
              <input type="text"  name="job_id"  id="job_id"  value="<?=$settings[0]->job_id?>">

              
            
         </div>
         
         
         
         <div class="form-group"> 
              <?php
			    
			     $post_types = get_post_types();
				 $selected_post_type = array();
				 if($settings[0]->selected_post_type){
				 	$selected_post_type = json_decode($settings[0]->selected_post_type);
				 }
				
			  ?>
              <label class="form-check-label" for="model">Select Post Types</label>
              <select  name="selected_post_type[]"  id="selected_post_type" style="height:200px;" multiple="">
                <?php
				  foreach($post_types as $key=>$value){
					   $str_select = false;
					   foreach($selected_post_type as $key2=>$value2){
						   if($value == $value2){
							   $str_select = true;
						   }
					   }
				?>
                 <option value="<?=$value?>" <?php if($str_select == true){ echo "selected";}  ?>><?=$value?></option>
                <?php
				  }
				?>  
              </select>

              
           
         </div>
         
         
		 
         
       </div>
       
</div>
<div class="form-group">
    <div class="col-sm-offset-4 col-sm-8">
        <input type="hidden" name="id" value="<?=$settings[0]->id?>" >
        <button type="submit" class="button button-primary">Apply</button>
    </div>
</div>
</form>
<!--End of Form to save data//-->	
