<?php
$pageTitle = 'Hỗ Trợ - Game Store';
require_once __DIR__ . '/../layout/header.php';
?>

<!-- BANNER -->
<section class="bg-primary text-white text-center py-5">
    <h1>Trung Tâm Hỗ Trợ</h1>
    <p>Chúng tôi luôn sẵn sàng giúp bạn 24/7</p>
</section>

<!-- CONTACT INFO -->
<section class="container py-5">
    <div class="row">
        <div class="col-md-6">
            <h4>Thông tin liên hệ</h4>
            <p>Email: support@gamestore.com</p>
            <p>Hotline: 1900 1234</p>
            <p>Địa chỉ: TP. Hồ Chí Minh</p>
        </div>

        <div class="col-md-6">
            <h4>Gửi yêu cầu hỗ trợ</h4>
            <form>
                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="Họ và tên">
                </div>
                <div class="mb-3">
                    <input type="email" class="form-control" placeholder="Email">
                </div>
                <div class="mb-3">
                    <textarea class="form-control" rows="4" placeholder="Nội dung"></textarea>
                </div>
                <button class="btn btn-primary w-100">Gửi yêu cầu</button>
            </form>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="bg-light py-5">
    <div class="container">
        <h3 class="text-center mb-4">Câu hỏi thường gặp</h3>
        <div class="accordion" id="faq">

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#q1">
                        Làm sao để mua game?
                    </button>
                </h2>
                <div id="q1" class="accordion-collapse collapse show" data-bs-parent="#faq">
                    <div class="accordion-body">
                        Chọn game → Thêm vào giỏ hàng → Thanh toán.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#q2">
                        Thanh toán bằng cách nào?
                    </button>
                </h2>
                <div id="q2" class="accordion-collapse collapse" data-bs-parent="#faq">
                    <div class="accordion-body">
                        Hỗ trợ chuyển khoản, ví điện tử và thẻ ngân hàng.
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<?php require_once __DIR__ . '/../layout/footer.php'; ?>
