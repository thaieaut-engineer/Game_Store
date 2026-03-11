<?php
$pageTitle = 'Đăng ký - Game Store';
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
            <h2>Tạo tài khoản mới</h2>
            <form action="<?php echo BASE_URL; ?>auth/register" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Họ và tên</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" id="password" name="password" required minlength="6">
                </div>
                <div class="mb-4">
                    <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required
                        minlength="6">
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">Đăng Ký</button>

                <div class="auth-divider">hoặc tham gia với</div>

                <div class="social-auth">
                    <a href="#" class="social-btn facebook" title="Đăng ký bằng Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="social-btn xbox" title="Đăng ký bằng Xbox">
                        <i class="bi bi-xbox"></i>
                    </a>
                    <a href="#" class="social-btn playstation" title="Đăng ký bằng PlayStation">
                        <i class="bi bi-playstation"></i>
                    </a>
                </div>
            </form>

            <div class="auth-links">
                <p class="mb-0">Đã có tài khoản? <a href="<?php echo BASE_URL; ?>auth/login">Đăng nhập ngay</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>