$(document).ready(function() {
    const $bubble = $('#chatbot-bubble');
    const $window = $('#chatbot-window');
    const $close = $('#chatbot-close');
    const $input = $('#chatbot-input');
    const $send = $('#chatbot-send');
    const $messages = $('#chatbot-messages');
    const $quickActions = $('.quick-action');

    // Toggle Chat Window
    $bubble.on('click', function() {
        $window.toggleClass('active');
        if ($window.hasClass('active')) {
            $input.focus();
        }
    });

    $close.on('click', function() {
        $window.removeClass('active');
    });

    // Close on escape key
    $(document).on('keydown', function(e) {
        if (e.key === "Escape" && $window.hasClass('active')) {
            $window.removeClass('active');
        }
    });

    // Send Message
    function sendMessage(message, type = 'guide') {
        if (!message.trim()) return;

        // Add user message
        appendMessage(message, 'user');
        $input.val('');

        // Show typing indicator
        const $typing = $('<div class="typing ps-2">AI đang trả lời...</div>');
        $messages.append($typing);
        scrollToBottom();

        // AJAX to TutorialController
        $.ajax({
            url: BASE_URL + 'tutorial/ask',
            method: 'POST',
            data: {
                question: message,
                type: type
            },
            success: function(response) {
                $typing.remove();
                if (response.success) {
                    appendMessage(response.answer, 'system');
                } else {
                    appendMessage('Có lỗi xảy ra: ' + response.message, 'system');
                }
            },
            error: function() {
                $typing.remove();
                appendMessage('Không thể kết nối với máy chủ AI.', 'system');
            }
        });
    }

    function appendMessage(text, side) {
        const messageHtml = `
            <div class="message ${side}">
                ${text.replace(/\n/g, '<br>')}
            </div>
        `;
        $messages.append(messageHtml);
        scrollToBottom();
    }

    function scrollToBottom() {
        $messages.scrollTop($messages[0].scrollHeight);
    }

    // Event Handlers
    $send.on('click', function() {
        sendMessage($input.val());
    });

    $input.on('keypress', function(e) {
        if (e.which === 13) {
            sendMessage($(this).val());
        }
    });

    // Quick Actions
    $quickActions.on('click', function() {
        const type = $(this).data('type');
        let question = '';

        switch(type) {
            case 'suggest':
                question = 'Gợi ý cho tôi một số tựa game hay đang hot hiện nay.';
                break;
            case 'play':
                question = 'Hướng dẫn tôi các phím tắt và mẹo cơ bản khi chơi game.';
                break;
            case 'error':
                question = 'Tôi gặp lỗi khi cài đặt/đăng nhập, hãy giúp tôi.';
                break;
            case 'guide':
                question = 'Làm thế nào để mua game và thanh toán trên website?';
                break;
        }

        sendMessage(question, type);
    });
});
