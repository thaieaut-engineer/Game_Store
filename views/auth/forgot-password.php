<?php
$pageTitle = 'Quên mật khẩu - Game Store';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Quên Mật Khẩu</h2>
                    <form action="<?php echo BASE_URL; ?>auth/forgot-password" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <small class="text-muted">Chúng tôi sẽ gửi link đặt lại mật khẩu đến email của bạn.</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Gửi Email</button>
                        <div class="text-center mt-3">
                            <a href="<?php echo BASE_URL; ?>auth/login">Quay lại đăng nhập</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
