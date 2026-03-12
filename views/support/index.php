<?php
$pageTitle = 'Hỗ Trợ - Game Store';
require_once __DIR__ . '/../layout/header.php';
?>

<style>
    :root {
        --support-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --glass-bg: rgba(255, 255, 255, 0.95);
        --glass-border: rgba(255, 255, 255, 0.2);
    }

    .support-hero {
        background: var(--support-gradient);
        padding: 100px 0;
        color: white;
        text-align: center;
        margin-bottom: -50px;
    }

    .support-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        padding: 40px;
        margin-bottom: 30px;
        transition: transform 0.3s ease;
    }

    .support-card:hover {
        transform: translateY(-5px);
    }

    .contact-item {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .contact-icon {
        width: 50px;
        height: 50px;
        background: #f0f2f5;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: #764ba2;
        font-size: 20px;
    }

    .faq-section {
        background: #f8f9fa;
        padding: 80px 0;
    }

    .accordion-item {
        border: none;
        margin-bottom: 15px;
        border-radius: 12px !important;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .accordion-button {
        font-weight: 600;
        padding: 20px;
    }

    .accordion-button:not(.collapsed) {
        background-color: #667eea;
        color: white;
    }

    .form-control {
        padding: 12px 20px;
        border-radius: 10px;
        border: 1px solid #dee2e6;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    }

    .btn-submit {
        background: var(--support-gradient);
        border: none;
        padding: 12px;
        border-radius: 10px;
        font-weight: 600;
        transition: opacity 0.3s ease;
    }

    .btn-submit:hover {
        opacity: 0.9;
        color: white;
    }

    /* Dark Theme Overrides */
    body.dark-theme .support-card {
        background: var(--card-bg);
        border-color: var(--card-border);
    }

    body.dark-theme .faq-section {
        background: transparent;
    }

    body.dark-theme .contact-icon {
        background: rgba(255, 255, 255, 0.05);
        color: #a78bfa;
    }

    body.dark-theme .accordion-item {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
    }

    body.dark-theme .accordion-button {
        background-color: var(--card-bg);
        color: var(--text-color);
    }

    body.dark-theme .accordion-button:not(.collapsed) {
        background-color: #667eea;
        color: white;
    }

    body.dark-theme .accordion-button::after {
        filter: invert(1) grayscale(100%) brightness(200%);
    }

    body.dark-theme .accordion-body {
        color: var(--text-color);
    }
</style>

<!-- HERO SECTION -->
<section class="support-hero">
    <div class="container">
        <h1 class="display-4 fw-bold">Trung Tâm Hỗ Trợ</h1>
        <p class="lead">Chúng tôi luôn ở đây để giúp bạn có trải nghiệm tuyệt vời nhất.</p>
    </div>
</section>

<!-- MAIN CONTENT -->
<section class="container" style="position: relative; z-index: 10;">
    <div class="row">
        <!-- CONTACT INFO -->
        <div class="col-lg-5">
            <div class="support-card h-100">
                <h3 class="mb-4">Liên hệ với chúng tôi</h3>
                <p class="text-muted mb-4">Bạn có thắc mắc hoặc cần hỗ trợ kỹ thuật? Đừng ngần ngại liên hệ qua các kênh
                    sau:</p>

                <div class="contact-item">
                    <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                    <div>
                        <div class="fw-bold">Email</div>
                        <div class="text-muted">support@gamestore.com</div>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon"><i class="fas fa-phone"></i></div>
                    <div>
                        <div class="fw-bold">Hotline</div>
                        <div class="text-muted">1900 1234 (8:00 - 22:00)</div>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div>
                        <div class="fw-bold">Địa chỉ</div>
                        <div class="text-muted">7/1 Thành Thái, Quận 10, TP. Hồ Chí Minh</div>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon"><i class="fab fa-facebook-messenger"></i></div>
                    <div>
                        <div class="fw-bold">Messenger</div>
                        <div class="text-muted">fb.com/gamestore_official</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SUPPORT FORM -->
        <div class="col-lg-7">
            <div class="support-card">
                <h3 class="mb-4">Gửi yêu cầu hỗ trợ</h3>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success'];
                        unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error'];
                        unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>/support/submit" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Họ và tên</label>
                            <input type="text" name="name" class="form-control" placeholder="Nguyễn Văn A" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="name@example.com"
                                required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chủ đề</label>
                        <select name="subject" class="form-control">
                            <option value="Hỗ trợ thanh toán">Hỗ trợ thanh toán</option>
                            <option value="Lỗi kỹ thuật">Lỗi kỹ thuật</option>
                            <option value="Tài khoản">Vấn đề tài khoản</option>
                            <option value="Khác">Khác</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nội dung chi tiết</label>
                        <textarea name="message" class="form-control" rows="5"
                            placeholder="Mô tả chi tiết vấn đề bạn đang gặp phải..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-submit w-100 text-white">Gửi Yêu Cầu</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- FAQ SECTION -->
<section class="faq-section">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold">Câu Hỏi Thường Gặp</h2>
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="accordion" id="faqAccordion">
                    <!-- Q1 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne">
                                Tôi có thể lấy lại tiền nếu game không chạy không?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Có, chúng tôi hỗ trợ hoàn tiền trong vòng 24h kể từ khi mua nếu game gặp lỗi kỹ thuật
                                không thể khắc phục và bạn chưa chơi quá 2 giờ. Vui lòng gửi yêu cầu hỗ trợ kèm ảnh chụp
                                màn hình lỗi.
                            </div>
                        </div>
                    </div>

                    <!-- Q2 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo">
                                Làm thế nào để tải game sau khi đã mua?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Ngay sau khi thanh toán thành công, game sẽ được thêm vào <strong>Thư viện</strong> của
                                bạn. Bạn chỉ cần nhấn vào nút "Tải về" hoặc "Chơi ngay" trên trang chi tiết game đó.
                            </div>
                        </div>
                    </div>

                    <!-- Q3 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseThree">
                                Website hỗ trợ những phương thức thanh toán nào?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Chúng tôi hỗ trợ nhiều phương thức thanh toán: Ví MoMo, ZaloPay, Chuyển khoản ngân hàng
                                (Vietcombank, Techcombank...) và thẻ Visa/Mastercard.
                            </div>
                        </div>
                    </div>

                    <!-- Q4 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseFour">
                                Tại sao tôi không nhận được email xác nhận đơn hàng?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Vui lòng kiểm tra hộp thư Spam hoặc Quảng cáo. Nếu vẫn không thấy, hãy liên hệ với chúng
                                tôi qua hotline hoặc form hỗ trợ phía trên để được kiểm tra trạng thái đơn hàng.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>