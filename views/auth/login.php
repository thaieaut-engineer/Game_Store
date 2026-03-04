<?php
$pageTitle = 'Đăng nhập - Game Store';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Đăng Nhập</h2>
                    <form
                        action="<?php echo BASE_URL; ?>auth/login<?php echo !empty($_GET) ? '?' . http_build_query($_GET) : ''; ?>"
                        method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <a href="<?php echo BASE_URL; ?>auth/forgot-password">Quên mật khẩu?</a>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Đăng Nhập</button>
                        <div class="text-center mt-3">
                            <p>Chưa có tài khoản? <a href="<?php echo BASE_URL; ?>auth/register">Đăng ký ngay</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>