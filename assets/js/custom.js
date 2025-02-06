jQuery(document).ready(function () {
    $ = jQuery;

    $('#EazyBotReload').click(function () {
        $('.eazybotchatbox').empty();
        $('#suggested-questions').removeClass('d-none');
        $('.eazybotchatbox').append(`
            <div class="d-flex align-items-start mb-3">
                <i class="fas fa-robot bg-light p-2 rounded-circle me-2"></i>
                <div class="bg-light p-2 rounded">
                    <p class="text-dark">${eazyai_chatbot_vars.welcome_message || 'What can I help you with?'}</p>
                </div>
            </div>
        `);
        $('#eazybotinput').val('');
    });

    // EazyAI Chatbot Message Sending
    jQuery(document).on('click', '#eazybotsend', function () {
        const message = $('#eazybotinput').val();
        if (message) {
            $('.eazybotchatbox').append(`
                <div class="d-flex align-items-start mb-3 justify-content-end">
                    <div class="eazybot-bg-primary-foreground bg-primary text-white p-2 rounded">
                        <p>${message}</p>
                    </div>
                </div>
            `);
            $('#suggested-questions').addClass('d-none');
            $('#eazybotinput').val('');
            const chatbox = $('.eazybotchatbox')[0];
            if (chatbox) {
                $('.eazybotchatbox').scrollTop(chatbox.scrollHeight);
            }

            // Loading Animation
            $('.eazybotchatbox').append(`
                <div class="d-flex align-items-start mb-3 loading-message">
                    <img class="rounded-circle me-2" src="${eazyai_chatbot_vars.bot_avatar}" alt="Chatbot Avatar" style="width: 32px; height: 32px;" />
                    <div class="bg-light p-2 rounded">
                        <div class="typing-dots">
                            <span>.</span><span>.</span><span>.</span>
                        </div>
                    </div>
                </div>
            `);

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
                        $('.eazybotchatbox').append(`
                            <div class="d-flex align-items-start mb-3">
                                <img class="rounded-circle me-2" src="${eazyai_chatbot_vars.bot_avatar}" alt="Chatbot Avatar" style="width: 32px; height: 32px;" />
                                <div class="bg-light p-2 rounded">
                                    <p>${response.data.answer}</p>
                                    <div class="mt-1">
                                        ${response.data.references.length > 0 ? `
                                            <div class="text-muted small mb-1">
                                                <button class="btn btn-link p-0 text-decoration-none" onclick="this.nextElementSibling.classList.toggle('d-none')">
                                                    Sources &#9660;
                                                </button>
                                                <div class="d-none mt-1">
                                                    ${[...new Set(response.data.references.map(ref => ref.link))].map(link => {
                                                        const ref = response.data.references.find(r => r.link === link);
                                                        return `<a href="${link}" class="small text-primary d-block">${ref.title || 'Source'}</a>`;
                                                    }).join('')}
                                                </div>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        `);
                        $('.eazybotchatbox').scrollTop($('.eazybotchatbox')[0].scrollHeight);
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                },
                error: function () {
                    $('.loading-message').remove();
                    alert('An error occurred while processing your request.');
                }
            });
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
            $chatbox.addClass('d-none');
            $helpIcon.removeClass('d-none').fadeIn(300);
            $closeIcon.addClass('d-none');
        });
    } else {
        $chatbox.removeClass('d-none').slideDown(300);
        $helpIcon.fadeOut(300, function () {
            $helpIcon.addClass('d-none');
            $closeIcon.removeClass('d-none').fadeIn(300);
        });
    }
}
