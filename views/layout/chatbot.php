<!-- Chatbot Bubble -->
<div id="chatbot-bubble" class="chatbot-bubble">
    <i class="bi bi-chat-dots-fill"></i>
    <span class="chatbot-tooltip">Hỗ trợ AI</span>
</div>

<!-- Chatbot Window -->
<div id="chatbot-window" class="chatbot-window">
    <div class="chatbot-header">
        <div class="d-flex align-items-center">
            <div class="chatbot-avatar">
                <i class="bi bi-robot"></i>
            </div>
            <div class="ms-2">
                <h6 class="mb-0">Game Store AI</h6>
                <small class="text-success"><i class="bi bi-circle-fill" style="font-size: 8px;"></i> Online</small>
            </div>
        </div>
        <button id="chatbot-close" class="btn-close btn-close-white" aria-label="Close"></button>
    </div>

    <div id="chatbot-messages" class="chatbot-messages">
        <div class="message system">
            Chào bạn! Tôi là trợ lý AI của Game Store. Tôi có thể giúp gì cho bạn?
        </div>
    </div>

    <div class="chatbot-quick-actions">
        <button class="quick-action" data-type="suggest">🎮 Gợi ý game</button>
        <button class="quick-action" data-type="play">📚 Cách chơi</button>
        <button class="quick-action" data-type="error">💡 Sửa lỗi</button>
        <button class="quick-action" data-type="guide">🛒 Mua hàng</button>
    </div>

    <div class="chatbot-input-area">
        <div class="input-group">
            <input type="text" id="chatbot-input" class="form-control" placeholder="Nhập câu hỏi..."
                aria-label="Question">
            <button class="btn btn-primary" id="chatbot-send">
                <i class="bi bi-send-fill"></i>
            </button>
        </div>
    </div>
</div>