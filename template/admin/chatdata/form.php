
 <link 
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.css" 
  rel="stylesheet"  type='text/css'>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>


<?php
 error_reporting(!E_WARNING);
?>
<a  href="<?php echo 'admin.php?page=chatdata'; ?>" class="btn btn-info"><i class="arrow_left"></i> List</a>
<h5 class="font-20 mt-15 mb-1"><?php if($id<0){echo "Save";}else { echo "Update";} echo " "; echo str_replace('_',' ','ChatData'); ?></h5>
<!--Form to save data-->
<form method="post" action="admin.php?page=chatdata&cmd=save&id=<?=$chatdata[0]->id?>" enctype="multipart/form-data">
   <div class="row"> 
      <div class="col">
           <div class="form-group"> 
          <label for="question" class="col-md-8 control-label">Question</label> 
          <div class="col-md-8"> 
           <input type="text" name="question" value="<?php echo ($_POST['question'] ? $_POST['question'] : $chatdata[0]->question); ?>" class="form-control" id="question" /> 
          </div> 
           </div>
   
          <div class="form-group"> 
          <label for="answer" class="col-md-8 control-label">Answer</label> 
          <div class="col-md-8"> 
           <input type="text" name="answer" value="<?php echo ($_POST['answer'] ? $_POST['answer'] : $chatdata[0]->answer); ?>" class="form-control" id="answer" /> 
          </div> 
           </div>
       </div>
</div>
<div class="form-group">
    <div class="col-sm-offset-4 col-sm-8">
        <input type="hidden" name="id" value="<?=$chatdata[0]->id?>" >
        <button type="submit" class="btn btn-success"><?php if(empty($chatdata[0]->id)){?>Save<?php }else{?>Update<?php } ?></button>
    </div>
</div>
</form>
<!--End of Form to save data//-->	
