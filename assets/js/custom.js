jQuery(document).ready(function () {
    $ = jQuery;

    function appendMsgEl(message, sender = "bot") {
        if(sender !== "user" && sender !== "bot") return;

        const msgEl = $(`.eazybotchatbox .eazyai-msg.${sender}`)?.first();

        const clonedMsgEl = msgEl?.clone(true);
        clonedMsgEl?.find("p")?.text(message);
        msgEl?.parent()?.append(clonedMsgEl);
        clonedMsgEl?.removeClass("placeholder");

        return clonedMsgEl;
    }

    $('#EazyBotReload').click(function () {
        const userMsgPlaceholderEl = appendMsgEl("", "user");
        userMsgPlaceholderEl.addClass("placeholder");
        const wlcMsgEl = appendMsgEl(`${eazyai_chatbot_vars.welcome_message || 'What can I help you with?'}`, "bot");
        $('.eazybotchatbox').empty().append(userMsgPlaceholderEl).append(wlcMsgEl);
        $('#suggested-questions').hide();
        $('#eazybotinput').val('');
    });
    // Handle Enter key press
    $('#eazybotinput').keypress(function(e) {
        if (e.which == 13) {
            $('#eazybotsend').click();
            return false;
        }
    });
    // Add beforeunload event listener
    window.addEventListener('beforeunload', function (e) {
        if (window.chatSessionActive) {
            e.preventDefault();
            // Use WordPress translation function if available, fallback to default message
            e.returnValue = (typeof wp !== 'undefined' && wp.i18n) 
                ? wp.i18n.__('You have an active chat session. Are you sure you want to leave?', 'wp-eazyai')
                : 'You have an active chat session. Are you sure you want to leave?';
        }
    });
    // EazyAI Chatbot Message Sending
    jQuery(document).on('click', '#eazybotsend', function () {
        const message = $('#eazybotinput').val();
        if (message) {
            appendMsgEl(message, "user");

            $('#suggested-questions').hide();
            $('#eazybotinput').val('');

            // Loading Animation
            const loadingMsgEl = appendMsgEl("...", "bot");
            loadingMsgEl?.addClass("loading-message");
            
            scrollToLastChat();
            
            // Set a flag when chat session is active
            window.chatSessionActive = true;

                
            $.ajax({
                url: eazyai_chatbot_vars.ajaxurl,
                type: 'POST',
                data: {
                    action: 'wpeazyai_chatbot_response',
                    message: message,
                },
                success: function (response) {
                    $('.loading-message').remove(); // Remove loading animation

                    if (response.success) {
                        appendMsgEl(response.data.answer, "bot");

                        // $('.eazybotchatbox').append(`
                        //     <div class="d-flex align-items-start mb-3">
                        //         <img class="rounded-circle me-2" src="${eazyai_chatbot_vars.bot_avatar}" alt="Chatbot Avatar" style="width: 32px; height: 32px;" />
                        //         <div class="bg-light p-2 rounded">
                        //             <p>${response.data.answer}</p>
                        //             <div class="mt-1">
                        //                 ${response.data.references.length > 0 ? `
                        //                     <div class="text-muted small mb-1">
                        //                         <button class="btn btn-link p-0 text-decoration-none" onclick="this.nextElementSibling.classList.toggle('d-none')">
                        //                             Sources &#9660;
                        //                         </button>
                        //                         <div class="d-none mt-1">
                        //                             ${[...new Set(response.data.references.map(ref => ref.link))].map(link => {
                        //                                 const ref = response.data.references.find(r => r.link === link);
                        //                                 return `<a href="${link}" class="small text-primary d-block">${ref.title || 'Source'}</a>`;
                        //                             }).join('')}
                        //                         </div>
                        //                     </div>
                        //                 ` : ''}
                        //             </div>
                        //         </div>
                        //     </div>
                        // `);
                    } else {
                        // alert('Error: ' + response.data.message);
                        appendMsgEl('Sorry! can\'t help you right now. Something went wrong.', 'bot');
                    }
                },
                error: function (e) {
                    $('.loading-message').remove();
                    // alert('An error occurred while processing your request.');
                    appendMsgEl('Sorry! can\'t help you right now. Something went wrong.', 'bot');
                }
            });

            scrollToLastChat();
        }
    });
});

// Toggle Chatbox
function EazyBotToggleChat() {
    const $chatbox = $('#eazyai-chatbox');
    const $helpIcon = $('#help-icon');
    const $closeIcon = $('#close-icon');

    if ($chatbox.is(':visible')) {
        $chatbox.slideUp(300, function () {
            $helpIcon.show().fadeIn(300);
            $closeIcon.hide();
        });
        $chatbox.css("display", "flex");
    } else {
        $chatbox.hide().slideDown(300);
        $helpIcon.fadeOut(300, function () {
            $helpIcon.hide();
            $closeIcon.hide().fadeIn(300);
        });
    }
}

function scrollToLastChat() {
    const chatbox = $('.eazybotchatbox')[0];
    if (chatbox) {
        setTimeout(() => {
            chatbox.scrollTo({
                top: chatbox.scrollHeight,
                behavior: "smooth"
            });
        }, 100);
    }
}
