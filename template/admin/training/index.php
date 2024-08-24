<br>
<br>

<a  href="admin.php?page=training&cmd=import_json_file">
 Import json file
</a>

<br>
<br>
<a  href="admin.php?page=training&cmd=training_file">
 Training
  <?php
    if(isset($_SESSION['file_id'])){
                    echo "(" .$_SESSION['file_id'].")";

    }

?>
</a>