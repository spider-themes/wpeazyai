<?php
/**
 * Plugin Name: EazyAi
 * Description: EazyAi is a chatbot plugin that uses GPT-3 to generate responses to user queries.
 * Plugin URI: https://spider-themes.net/eazyai
 * Author: spider-themes
 * Author URI: https://spider-themes.net
 * Version: 0.1
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Text Domain: eazyai
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

ob_start(); // line 1
session_start(); // line 2
$PLUGIN_URL = plugin_dir_url( __FILE__ );
define( 'CHATBOT_PLUGIN_URL', substr( $PLUGIN_URL, 0, strlen( $PLUGIN_URL ) - 1 ) );
define( 'CHATBOT_PLUGIN_PATH', str_replace( '\\', '/', dirname( __FILE__ ) ) );


register_activation_hook( __FILE__, 'chatbot_install' );
register_deactivation_hook( __FILE__, 'chatbot_remove' );
function eazyai_chatbot_install() {
	create_page( 'chatbot' );

	global $chatbot_db_version;
	$chatbot_db_version = "1.0";
	global $wpdb;
	global $chatbot_db_version;


	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );


	add_option( "chatbot_db_version", $chatbot_db_version );

	//create page
	include_once dirname( __FILE__ ) . '/create-page.php';

}

function eazyai_create_table() {
	global $wpdb;
	global $your_db_name;
	$charset_collate = $wpdb->get_charset_collate();

	$sql1 = "  CREATE TABLE " . $wpdb->prefix . "settings (
					  `id` int(10) NOT NULL AUTO_INCREMENT,	
					  `api_key`  varchar(127) DEFAULT NULL,
					  `model`  varchar(127) DEFAULT NULL,
					  `job_id`  varchar(127) DEFAULT NULL,
					  `front_visiblity`  varchar(127) DEFAULT NULL,
					  `selected_post_type`  varchar(256) DEFAULT NULL,
					  `created_at` datetime DEFAULT NULL,
					  `updated_at` datetime DEFAULT NULL,
					   UNIQUE KEY id (id)
					) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql1 );
}

// run the install scripts upon plugin activation
register_activation_hook( __FILE__, 'create_table' );

function eazyai_chatbot_remove() {
	global $wpdb;

	//remove page
	global $wpdb;

	$the_page_title = get_option( "my_plugin_page_title" );
	$the_page_name  = get_option( "my_plugin_page_name" );
	$the_page_id    = get_option( 'my_plugin_page_id' );
	if ( $the_page_id ) {
		wp_delete_post( $the_page_id );
	}
	delete_option( "my_plugin_page_title" );
	delete_option( "my_plugin_page_name" );
	delete_option( "my_plugin_page_id" );
}

function eazyai_create_page( $title ) {
	global $wpdb;

	//chatbot
	$the_page_title = $title;
	$the_page_name  = $title;

	delete_option( "my_plugin_page_title" );
	add_option( "my_plugin_page_title", $the_page_title, '', 'yes' );

	delete_option( "my_plugin_page_name" );
	add_option( "my_plugin_page_name", $the_page_name, '', 'yes' );

	delete_option( "my_plugin_page_id" );
	add_option( "my_plugin_page_id", '0', '', 'yes' );

	$the_page = get_page_by_title( $the_page_title );
	if ( ! $the_page ) {
		$_p                   = array();
		$_p['post_title']     = $the_page_title;
		$_p['post_content']   = "[" . $title . "]";
		$_p['post_status']    = 'publish';
		$_p['post_type']      = 'page';
		$_p['comment_status'] = 'closed';
		$_p['ping_status']    = 'closed';
		$_p['post_category']  = array( 1 );
		$the_page_id          = wp_insert_post( $_p );
	}
}

//Admin
add_action( 'admin_menu', 'chatbot_manage' );
function eazyai_chatbot_manage() {
	add_menu_page( 'Chatbot Settings', 'Chatbot', 'manage_options', 'chatbot', 'chatbot_settings_func' );
	//add_submenu_page( 'chatbot', 'ChatData', 'ChatData', 'manage_options', 'chatdata', 'chatdata_func');
	add_submenu_page( 'chatbot', 'Settings', 'Settings', 'manage_options', 'settings', 'settings_func' );

	add_submenu_page( 'chatbot', 'Training', 'Training', 'manage_options', 'training', 'training_func' );
}

function eazyai_chatbot_settings_func() {
	include_once dirname( __FILE__ ) . '/admin_chatbot.php';
}

function eazyai_settings_func() {
	include_once dirname( __FILE__ ) . '/admin_settings.php';
}

function eazyai_training_func() {
	include_once dirname( __FILE__ ) . '/admin_training.php';
}

//short code chatbots
function eazyai_chatbot_sort_code_func( $atts ) {
	include_once dirname( __FILE__ ) . '/template/front/chatbot.php';
}

add_shortcode( 'chatbot', 'chatbot_sort_code_func' );


add_action( 'eazydocs_assistant_tab', 'my_custom_tab', 10 );
function eazyai_my_custom_tab( $tabs ) {
	$tabs[] = array(
		'id'      => 'my-tab',
		'heading' => 'Chatbot',
		'content' => chatbot_sort_code_fsucnc()
	);

	return $tabs;
}

;

function eazyai_chatbot_sort_code_fsucnc() {

	ob_start();
	?>

    <style>
        span#send-btn {
            cursor: pointer;
        }

        button.chatbot__button {
            display: none;
        }

        ul.chatbot__box {
            overflow-y: scroll;
            height: 245px;
        }

        .chatbot__box::-webkit-scrollbar {
            display: none;
        }


        .tab-content-container {
            background: white;
            padding: 10px;
            border-radius: 10px;
        }


        .chatbot__textarea {
            width: 100%;
            min-height: 55px;
            max-height: 180px;
            font-size: 0.95rem;
            padding: 16px 15px 16px 13px;
            color: #202020;
            border: none;
            outline: none;
            resize: none;

        }

        .chatbot__textarea:valid ~ span {
            visibility: visible;
        }

        .chatbot__textarea::placeholder {
            font-family: 'Poppins', sans-serif;
        }
    </style>


    <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0"
    />
    <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,1,0"
    />
    <!-- Code :) -->
    <button class="chatbot__button">
        <span class="material-symbols-outlined">mode_comment</span>
        <span class="material-symbols-outlined">close</span>
    </button>
    <div class="chatbot">
        <div class="chatbot__header">
            <h3 class="chatbox__title"></h3>
            <span class="material-symbols-outlined"></span>
        </div>
        <ul class="chatbot__box">
            <li class="chatbot__chat incoming">
                <span class="material-symbols-outlined">smart_toy</span>
                <p>Hi there. How can I help you today?</p>
            </li>
            <li class="chatbot__chat outgoing">
                <p>...</p>
            </li>
        </ul>
        <div class="chatbot__input-box">
        <textarea
                class="chatbot__textarea"
                placeholder="Enter a message..."
                required
        ></textarea>
            <span id="send-btn" class="material-symbols-outlined">send</span>
        </div>
    </div>

    <script language="javascript">
      const chatbotToggle = document.querySelector('.chatbot__button');
      const sendChatBtn = document.querySelector('.chatbot__input-box span');
      const chatInput = document.querySelector('.chatbot__textarea');
      const chatBox = document.querySelector('.chatbot__box');
      const chatbotCloseBtn = document.querySelector('.chatbot__header span');

      let userMessage;
      // Need GPT key
      const inputInitHeight = chatInput.scrollHeight;
      const API_KEY = 'HERE';

      const createChatLi = (message, className) => {
        const chatLi = document.createElement('li');
        chatLi.classList.add('chatbot__chat', className);
        let chatContent =
            className === 'outgoing'
                ? `<p></p>`
                : `<span class="material-symbols-outlined">smart_toy</span> <p></p>`;
        chatLi.innerHTML = chatContent;
        chatLi.querySelector('p').textContent = message;
        return chatLi;
      };

      const generateResponse = (incomingChatLi) => {
        const API_URL = '<?php echo WP_PLUGIN_URL . '/' . basename( plugin_dir_path( dirname( __FILE__, 2 ) ) ) . '/api/chat_data.php'; ?>';
        const messageElement = incomingChatLi.querySelector('p');

        const requestOptions = {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            msg: userMessage,
          }),
        };
        fetch(API_URL, requestOptions).then((res) => res.json()).then((data) => {
          console.log(data);
          //messageElement.textContent = data.msg;
          messageElement.innerHTML = data.msg;
        }).catch((error) => {
          messageElement.classList.add('error');
          messageElement.textContent = 'Oops! Please try again!';
          console.log(error);
        }).finally(() => chatBox.scrollTo(0, chatBox.scrollHeight));
      };

      const handleChat = () => {
        userMessage = chatInput.value.trim();
        if (!userMessage) return;
        chatInput.value = '';
        chatInput.style.height = `${inputInitHeight}px`;

        chatBox.appendChild(createChatLi(userMessage, 'outgoing'));
        chatBox.scrollTo(0, chatBox.scrollHeight);

        setTimeout(() => {
          const incomingChatLi = createChatLi('Thinking...', 'incoming');
          chatBox.appendChild(incomingChatLi);
          chatBox.scrollTo(0, chatBox.scrollHeight);
          generateResponse(incomingChatLi);
        }, 600);
      };

      chatInput.addEventListener('input', () => {
        chatInput.style.height = `${inputInitHeight}px`;
        chatInput.style.height = `${chatInput.scrollHeight}px`;
      });
      chatInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey && window.innerWidth > 800) {
          e.preventDefault();
          handleChat();
        }
      });
      chatbotToggle.addEventListener('click', () =>
          document.body.classList.toggle('show-chatbot'),
      );
      chatbotCloseBtn.addEventListener('click', () =>
          document.body.classList.remove('show-chatbot'),
      );
      sendChatBtn.addEventListener('click', handleChat);
    </script>

	<?php
	return ob_get_clean();
}