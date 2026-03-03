<footer class="footer-steam mt-auto">
    <div class="container py-5">
        <div class="row g-4 align-items-start">

            <!-- Brand -->
            <div class="col-lg-4">
                <div class="footer-brand">
                    <h3>🎮 Game Store</h3>
                    <p>
                        Nền tảng phân phối game bản quyền, ưu đãi mỗi ngày,
                        hỗ trợ nhanh – an toàn – tiện lợi.
                    </p>

                    <!-- Social -->
                    <div class="footer-social">
                        <a href="#" title="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" title="Discord"><i class="bi bi-discord"></i></a>
                        <a href="#" title="Youtube"><i class="bi bi-youtube"></i></a>
                        <a href="#" title="Steam"><i class="bi bi-steam"></i></a>
                    </div>
                </div>
            </div>

            <!-- Links -->
            <div class="col-lg-2 col-md-4">
                <h6 class="footer-title">STORE</h6>
                <ul class="footer-list">
                    <li><a href="<?php echo BASE_URL; ?>">Trang chủ</a></li>
                    <li><a href="<?php echo BASE_URL; ?>game">Cửa hàng</a></li>
                    <li><a href="<?php echo BASE_URL; ?>#special">Ưu đãi</a></li>
                    <li><a href="#">Game mới</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-4">
                <h6 class="footer-title">ACCOUNT</h6>
                <ul class="footer-list">
                    <li><a href="#">Tài khoản</a></li>
                    <li><a href="#">Thư viện</a></li>
                    <li><a href="#">Lịch sử mua</a></li>
                    <li><a href="#">Wishlist</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-4">
                <h6 class="footer-title">SUPPORT</h6>
                <ul class="footer-list">
                    <li><a href="<?php echo BASE_URL; ?>support">Trung tâm hỗ trợ</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Chính sách</a></li>
                    <li><a href="#">Điều khoản</a></li>
                </ul>
            </div>

            <!-- Download -->
            <div class="col-lg-2">
                <h6 class="footer-title">APP</h6>
                <a href="#" class="btn-download">
                    <i class="bi bi-download"></i> Tải ứng dụng
                </a>
            </div>

        </div>
    </div>

    <!-- Bottom -->
    <div class="footer-bottom-bar">
        <div class="container d-flex flex-column flex-md-row justify-content-between">
            <span>© <?php echo date('Y'); ?> Game Store</span>
            <span>Powered by PHP MVC • Inspired by Steam & Epic</span>
        </div>
    </div>
</footer>

<?php include __DIR__ . '/chatbot.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const BASE_URL = '<?php echo BASE_URL; ?>';
</script>
<script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/chatbot.js"></script>
</body>

</html>