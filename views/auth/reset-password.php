<?php
$pageTitle = 'Đặt lại mật khẩu - Game Store';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/auth.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="auth-page">

    <a href="<?php echo BASE_URL; ?>" class="auth-logo">
        <i class="bi bi-controller"></i> Game Store
    </a>

    <div class="auth-container">

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i><?php echo $_SESSION['success'];
                unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i><?php echo $_SESSION['error'];
                unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="auth-card">
            <h2>Đặt lại mật khẩu</h2>
            <p class="text-center mb-4" style="color: rgba(255,255,255,0.7);">Chọn mật khẩu mới an toàn cho tài khoản
                của bạn.</p>

            <form action="<?php echo BASE_URL; ?>auth/reset-password?token=<?php echo $_GET['token']; ?>" method="POST">
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu mới</label>
                    <input type="password" class="form-control" id="password" name="password" required minlength="6">
                </div>
                <div class="mb-4">
                    <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required
                        minlength="6">
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">Đặt Lại Mật Khẩu</button>
            </form>

            <div class="auth-links">
                <p class="mb-0"><a href="<?php echo BASE_URL; ?>auth/login">Hủy và quay lại đăng nhập</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>