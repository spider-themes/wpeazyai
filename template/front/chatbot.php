<?php
  global $wpdb;
  $settings  = $wpdb->get_results("select * from ".$wpdb->prefix ."settings  ORDER BY id DESC"); 
  
  if($settings[0]->front_visiblity=='on'){
?>


<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

* {
  padding: 0;
  margin: 0;
  box-sizing: border-box;
}
body {
  background-color: #bdc3c7;
  font-family: 'Poppins', sans-serif;
}
.chatbot__button {
  position: fixed;
  bottom: 35px;
  right: 40px;
  width: 50px;
  height: 50px;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #227ebb;
  color: #f3f7f8;
  border: none;
  border-radius: 50%;
  outline: none;
  cursor: pointer;
}
.chatbot__button span {
  position: absolute;
}
.show-chatbot .chatbot__button span:first-child,
.chatbot__button span:last-child {
  opacity: 0;
}
.show-chatbot .chatbot__button span:last-child {
  opacity: 1;
}
.chatbot {
  position: fixed;
  bottom: 100px;
  right: 40px;
  width: 420px;
  background-color: #f3f7f8;
  border-radius: 15px;
  box-shadow: 0 0 128px 0 rgba(0, 0, 0, 0.1) 0 32px 64px -48px rgba(0, 0, 0, 0.5);
  transform: scale(0.5);
  transition: transform 0.3s ease;
  overflow: hidden;
  opacity: 0;
  pointer-events: none;
  z-index:1000;
}
.show-chatbot .chatbot {
  opacity: 1;
  pointer-events: auto;
  transform: scale(1);
}
.chatbot__header {
  position: relative;
  background-color: #227ebb;
  text-align: center;
  padding: 16px 0;
}
.chatbot__header span {
  display: none;
  position: absolute;
  top: 50%;
  right: 20px;
  color: #202020;
  transform: translateY(-50%);
  cursor: pointer;
}
.chatbox__title {
  font-size: 1.4rem;
  color: #f3f7f8;
}
.chatbot__box {
  height: 350px;
    overflow-y: scroll;
    padding: 30px 20px 100px !important;

}
.chatbot__chat {
  display: flex;
}
.chatbot__chat p {
  max-width: 75%;
  font-size: 0.95rem;
  white-space: pre-wrap;
  color: #202020;
  background-color: #019ef9;
  border-radius: 10px 10px 0 10px;
  padding: 12px 16px;
}
.chatbot__chat p.error {
  color: #721c24;
  background: #f8d7da;
}
.incoming p {
  color: #202020;
  background: #bdc3c7;
  border-radius: 10px 10px 10px 0;
}
.incoming span {
  width: 32px;
  height: 32px;
  line-height: 32px;
  color: #f3f7f8;
  background-color: #227ebb;
  border-radius: 4px;
  text-align: center;
  align-self: flex-end;
  margin: 0 10px 7px 0;
}
.outgoing {
  justify-content: flex-end;
  margin: 20px 0;
}
.incoming {
  margin: 20px 0;
}
.chatbot__input-box {
  position: absolute;
  bottom: 0;
  width: 100%;
  display: flex;
  gap: 5px;
  align-items: center;
  border-top: 1px solid #227ebb;
  background: #f3f7f8;
  padding: 5px 20px;
}
.chatbot__textarea {
  width: 100%;
  min-height: 55px;
  max-height: 180px;
  font-size: 0.95rem;
  padding: 16px 15px 16px 0;
  color: #202020;
  border: none;
  outline: none;
  resize: none;
  background: transparent;
}
.chatbot__textarea::placeholder {
  font-family: 'Poppins', sans-serif;
}
.chatbot__input-box span {
  font-size: 1.75rem;
  color: #202020;
  cursor: pointer;
  visibility: hidden;
}
.chatbot__textarea:valid ~ span {
  visibility: visible;
}

@media (max-width: 490px) {
  .chatbot {
    right: 0;
    bottom: 0;
    width: 100%;
    height: 100%;
    border-radius: 0;
  }
  .chatbot__box {
    height: 90%;
  }
  .chatbot__header span {
    display: inline;
  }
}
</style>

<!-- Created based on Youtube [CodingLab](https://www.youtube.com/@CodingLabYT)--> 
<!-- Need an individual APIkey from OpenAI to make the bot work. it's free but with limitations-->

<!-- Icon  -->
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
        <h3 class="chatbox__title">Chatbot</h3>
        <span class="material-symbols-outlined">close</span>
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
  const API_URL = '<?php echo WP_PLUGIN_URL . '/'.basename( plugin_dir_path( dirname( __FILE__ , 2 ) ) ).'/api/chat_data.php' ; ?>';
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
  fetch(API_URL, requestOptions)
    .then((res) => res.json())
    .then((data) => {
      console.log(data);
      //messageElement.textContent = data.msg;
	  messageElement.innerHTML = data.msg;
    })
    .catch((error) => {
      messageElement.classList.add('error');
      messageElement.textContent = 'Oops! Please try again!';
      console.log(error);
    })
    .finally(() => chatBox.scrollTo(0, chatBox.scrollHeight));
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
  document.body.classList.toggle('show-chatbot')
);
chatbotCloseBtn.addEventListener('click', () =>
  document.body.classList.remove('show-chatbot')
);
sendChatBtn.addEventListener('click', handleChat);
</script>   

<?php
  }
?>