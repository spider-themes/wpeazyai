<?php
  global $wpdb;
  $settings  = $wpdb->get_results("select * from ".$wpdb->prefix ."settings  ORDER BY id DESC"); 
  
  if($settings[0]->front_visiblity=='on'){
?>


<h1>Chatbot, world!</h1>
 <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
 
<!--Chatbot--->
<style>





div#myForm {
    width: 300px;
}

/* Button used to open the chat form - fixed at the bottom of the page */
.open-button {
	background-color: #555;
	color: white;
	padding: 8px 10px;
	border: none;
	cursor: pointer;
	opacity: 0.8;
	position: fixed;
	bottom: 0px;
	right: 60px;
	width: 100px;
}

/* The popup chat - hidden by default */
.chat-popup {
	display: none;
	position: fixed;
	bottom: 0;
	right: 15px;
	border: 3px solid #f1f1f1;
	z-index: 9999999 !important;
}

/* Add styles to the form container */
.form-container {
	max-width: 300px;
	padding: 10px;
	background-color: white; //
	min-height: 400px;
}

/* Full-width textarea */
.form-container textarea {
	width: 100%;
	padding: 15px;
	margin: 5px 0 22px 0;
	border: none;
	background: #f1f1f1;
	resize: none;
	min-height: 100px;
}

/* When the textarea gets focus, do something */
.form-container textarea:focus {
	background-color: #ddd;
	outline: none;
}

/* Set a style for the submit/send button */
.form-container .btn {
	background-color: #4CAF50;
	color: white;
	padding: 16px 20px;
	border: none;
	cursor: pointer;
	width: 100%;
	margin-bottom: 10px;
	opacity: 0.8;
}

/* Add a red background color to the cancel button */
.form-container .cancel {
	background-color: red;
}

/* Add some hover effects to buttons */
.form-container .btn:hover, .open-button:hover {
	opacity: 1;
}


div#chatmsg {
    position: relative;
    overflow: scroll;
    height: 25vh;
}




.chatmsg {
	width: 100%;
	padding: 15px;
	margin: 5px 0 22px 0;
	border: none;
	background: #f1f1f1;
	resize: none;
	min-height: 100px;
}

.chatlabel {
	width: 100%;
	padding: 15px;
	margin: 5px 0 22px 0;
	border: none;
	background: #2ED046;
	resize: none;
}
</style>

<button class="open-button" onclick="openForm()">
	<i class="fa fa-comments-o fa-5" aria-hidden="true"></i> Chat
</button>

<div class="chat-popup" id="myForm"
	style="z-index: 99999 !important; background: #fff;">

	<h3 class="chatlabel">Chat</h3>
	<div id="chatmsg" class="chatmsg" style="z-index: 99999 !important;"></div>
	<form action="javascript:void();" class="form-container"
		style="z-index: 99999 !important;">

		<label for="msg"><b>Message</b></label>
		<textarea placeholder="Type message.." name="msg" id="msg" required></textarea>

		<button type="submit" class="btn">Send</button>
		<button type="button" class="btn cancel" onclick="closeForm()">Close</button>
	</form>

</div>

<script>
var $j = jQuery;

function openForm() {
  document.getElementById("myForm").style.display = "block";
}

function closeForm() {
  document.getElementById("myForm").style.display = "none";
}

$j(document).ready(function() {

	$j(".btn").on('click',function(e){
		if($j("#msg").val()=="")
		{
			return;
		}
	   $j("#chatmsg").append("You:"+$j("#msg").val()+"<br>");
	   sendReceive($j("#msg").val());
	   $j("#msg").val("");
	});
});

function sendReceive(msg)
{
	$j.post( "<?php echo WP_PLUGIN_URL . '/'.basename( plugin_dir_path( dirname( __FILE__ , 2 ) ) ).'/api/chat_data.php' ; ?>", { msg: msg })
	  .done(function( data ) {
		//alert( "Data Loaded: " + data );
		var len = $j("#chatmsg").html().length;
		if(len>400)
		{
		   $j("#chatmsg").html( $j("#chatmsg").html().substring(len-200, len-1));
		}
		$j("#chatmsg").append(data+"<br>");
	  }).fail(function( data ) {
		alert( "Data Loaded Fail");
	  });
}
</script>
<!--Chatbot-->
<?php
  }
?>  