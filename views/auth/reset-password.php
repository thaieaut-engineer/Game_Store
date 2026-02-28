<?php
$pageTitle = 'Đặt lại mật khẩu - Game Store';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Đặt Lại Mật Khẩu</h2>
                    <form action="<?php echo BASE_URL; ?>auth/reset-password?token=<?php echo $_GET['token']; ?>" method="POST">
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Đặt Lại Mật Khẩu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
