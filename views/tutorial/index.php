<?php $pageTitle = 'Hướng dẫn & Hỗ trợ AI'; ?>
<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary">Tutorial & AI Assistant</h1>
                <p class="lead text-muted">Chào mừng bạn đến với trung tâm hỗ trợ của Game Store. Chọn một chuyên mục
                    bên dưới để bắt đầu.</p>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="card h-100 shadow-sm border-0 tutorial-card"
                        onclick="selectType('play', '🤖 Hỏi AI cách chơi game')">
                        <div class="card-body text-center p-4">
                            <div class="icon-wrapper mb-3">
                                <i class="bi bi-robot fs-1 text-primary"></i>
                            </div>
                            <h5 class="card-title fw-bold">Cách chơi game</h5>
                            <p class="card-text small text-muted">Hỏi AI về cách chơi, cài đặt và trải nghiệm game.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 shadow-sm border-0 tutorial-card"
                        onclick="selectType('suggest', '🎮 Gợi ý game phù hợp')">
                        <div class="card-body text-center p-4">
                            <div class="icon-wrapper mb-3">
                                <i class="bi bi-controller fs-1 text-success"></i>
                            </div>
                            <h5 class="card-title fw-bold">Gợi ý game</h5>
                            <p class="card-text small text-muted">Tìm kiếm những tựa game đỉnh cao phù hợp với sở thích
                                của bạn.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 shadow-sm border-0 tutorial-card"
                        onclick="selectType('guide', '📚 Hướng dẫn sử dụng website')">
                        <div class="card-body text-center p-4">
                            <div class="icon-wrapper mb-3">
                                <i class="bi bi-book fs-1 text-info"></i>
                            </div>
                            <h5 class="card-title fw-bold">Sử dụng website</h5>
                            <p class="card-text small text-muted">Nắm vững cách mua sắm, thanh toán và quản lý tài
                                khoản.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 shadow-sm border-0 tutorial-card"
                        onclick="selectType('error', '💡 Giải thích lỗi thường gặp')">
                        <div class="card-body text-center p-4">
                            <div class="icon-wrapper mb-3">
                                <i class="bi bi-lightbulb fs-1 text-warning"></i>
                            </div>
                            <h5 class="card-title fw-bold">Lỗi thường gặp</h5>
                            <p class="card-text small text-muted">Khắc phục các sự cố về kỹ thuật, nạp tiền hoặc đăng
                                nhập.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Section -->
            <div id="ai-chat-section" class="card shadow-lg border-0 d-none">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0" id="chat-title">Hỗ trợ AI</h5>
                    <button type="button" class="btn-close btn-close-white" onclick="closeChat()"></button>
                </div>
                <div class="card-body bg-light" id="chat-box"
                    style="height: 400px; overflow-y: auto; display: flex; flex-direction: column;">
                    <div class="chat-message bot mb-3">
                        <div class="message-content p-3 bg-white shadow-sm rounded">
                            Chào bạn! Mình là trợ lý AI của Game Store. Hãy đặt câu hỏi về chuyên mục này nhé.
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 p-3">
                    <form id="ai-form" onsubmit="askAi(event)">
                        <input type="hidden" id="ai-type" value="">
                        <div class="input-group">
                            <input type="text" id="ai-question" class="form-control form-control-lg border-0 bg-light"
                                placeholder="Nhập câu hỏi của bạn tại đây..." required>
                            <button class="btn btn-primary px-4" type="submit">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .tutorial-card {
        transition: all 0.3s cubic-bezier(.25, .8, .25, 1);
        cursor: pointer;
    }

    .tutorial-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
    }

    .tutorial-card.active {
        border: 2px solid var(--bs-primary) !important;
        background-color: rgba(13, 110, 253, 0.05);
    }

    .icon-wrapper {
        width: 80px;
        height: 80px;
        background: #f8f9fa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .message-content {
        max-width: 80%;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .chat-message.user {
        align-self: flex-end;
    }

    .chat-message.user .message-content {
        background-color: var(--bs-primary) !important;
        color: white;
        border-radius: 15px 15px 0 15px;
    }

    .chat-message.bot {
        align-self: flex-start;
    }

    .chat-message.bot .message-content {
        border-radius: 15px 15px 15px 0;
    }

    #chat-box::-webkit-scrollbar {
        width: 6px;
    }

    #chat-box::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #chat-box::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 3px;
    }
</style>

<script>
    function selectType(type, title) {
        document.getElementById('ai-type').value = type;
        document.getElementById('chat-title').innerText = title;

        // UI feedback for selection
        document.querySelectorAll('.tutorial-card').forEach(card => card.classList.remove('active'));
        event.currentTarget.classList.add('active');

        document.getElementById('ai-chat-section').classList.remove('d-none');
        document.getElementById('ai-chat-section').scrollIntoView({ behavior: 'smooth' });

        // Clear previous messages if needed or add a welcome message for the specific type
        const chatBox = document.getElementById('chat-box');
        chatBox.innerHTML = `
        <div class="chat-message bot mb-3">
            <div class="message-content p-3 bg-white shadow-sm rounded">
                🤖 Bạn đã chọn mục: <strong>${title}</strong>. Mời bạn đặt câu hỏi!
            </div>
        </div>
    `;
    }

    function closeChat() {
        document.getElementById('ai-chat-section').classList.add('d-none');
        document.querySelectorAll('.tutorial-card').forEach(card => card.classList.remove('active'));
    }

    async function askAi(event) {
        event.preventDefault();
        const type = document.getElementById('ai-type').value;
        const questionInput = document.getElementById('ai-question');
        const question = questionInput.value.trim();
        const chatBox = document.getElementById('chat-box');

        if (!question) return;

        // Add user message to chat
        appendMessage('user', question);
        questionInput.value = '';

        // Add loading message
        const loadingId = 'loading-' + Date.now();
        appendMessage('bot', '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Đang suy nghĩ...', loadingId);

        try {
            const response = await fetch('<?php echo BASE_URL; ?>tutorial/ask', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `type=${encodeURIComponent(type)}&question=${encodeURIComponent(question)}`
            });

            const data = await response.json();

            // Remove loading
            document.getElementById(loadingId).remove();

            if (data.success) {
                appendMessage('bot', data.answer);
            } else {
                appendMessage('bot', '❌ Lỗi: ' + data.message);
            }
        } catch (error) {
            document.getElementById(loadingId).remove();
            appendMessage('bot', '❌ Có lỗi xảy ra trong quá trình kết nối với AI.');
        }
    }

    function appendMessage(sender, text, id = '') {
        const chatBox = document.getElementById('chat-box');
        const msgDiv = document.createElement('div');
        msgDiv.className = `chat-message ${sender} mb-3`;
        if (id) msgDiv.id = id;

        msgDiv.innerHTML = `
        <div class="message-content p-3 shadow-sm">
            ${text}
        </div>
    `;

        chatBox.appendChild(msgDiv);
        chatBox.scrollTop = chatBox.scrollHeight;
    }
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>