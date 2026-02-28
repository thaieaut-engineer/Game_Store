<?php
$pageTitle = 'Về Chúng Tôi - Game Store';
require_once __DIR__ . '/../layout/header.php';
?>

<!-- BANNER -->
<section class="about-banner text-center text-white d-flex align-items-center">
    <div class="container">
        <h1 class="display-4 fw-bold">Về Chúng Tôi</h1>
        <p>GameStore - Nơi hội tụ những tựa game đỉnh cao</p>
    </div>
</section>

<!-- INTRO -->
<section class="container py-5">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2>GameStore là ai?</h2>
            <p>
                GameStore là website chuyên cung cấp game bản quyền,
                đa dạng thể loại từ hành động, chiến thuật, nhập vai đến thể thao.
                Chúng tôi mang đến trải nghiệm mua game nhanh chóng, an toàn và tiện lợi.
            </p>
        </div>
        <div class="col-md-6">
        <img src="<?php echo BASE_URL; ?>assets/images/about.jpg"
            class="img-fluid rounded shadow"
            alt="Gaming setup">
        </div>
    </div>
</section>

<!-- MISSION -->
<section class="bg-light py-5">
    <div class="container text-center">
        <div class="row">
            <div class="col-md-4">
                <h4>🎯 Sứ mệnh</h4>
                <p>Cung cấp game chất lượng với giá tốt nhất.</p>
            </div>
            <div class="col-md-4">
                <h4>👁 Tầm nhìn</h4>
                <p>Trở thành nền tảng bán game hàng đầu Việt Nam.</p>
            </div>
            <div class="col-md-4">
                <h4>💎 Giá trị</h4>
                <p>Uy tín – Chất lượng – Hỗ trợ nhanh chóng.</p>
            </div>
        </div>
    </div>
</section>

<!-- TEAM -->
<section class="container py-5">
    <h2 class="text-center mb-4">Đội Ngũ Phát Triển</h2>
    <div class="row text-center">
        <div class="col-md-4">
            <img src="https://randomuser.me/api/portraits/men/32.jpg" class="rounded-circle mb-3" width="120">
            <h5>Nguyễn Văn A</h5>
            <p>Founder & Developer</p>
        </div>
        <div class="col-md-4">
            <img src="https://randomuser.me/api/portraits/women/44.jpg" class="rounded-circle mb-3" width="120">
            <h5>Trần Thị B</h5>
            <p>UI/UX Designer</p>
        </div>
        <div class="col-md-4">
            <img src="https://randomuser.me/api/portraits/men/65.jpg" class="rounded-circle mb-3" width="120">
            <h5>Lê Văn C</h5>
            <p>Marketing Manager</p>
        </div>
    </div>
</section>


<?php require_once __DIR__ . '/../layout/footer.php'; ?>
