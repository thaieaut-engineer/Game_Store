<?php
$pageTitle = 'Đăng nhập - Game Store';
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
            <h2>Chào mừng trở lại</h2>
            <form
                action="<?php echo BASE_URL; ?>auth/login<?php echo !empty($_GET) ? '?' . http_build_query($_GET) : ''; ?>"
                method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="form-label mb-0">Mật khẩu</label>
                        <a href="<?php echo BASE_URL; ?>auth/forgot-password"
                            class="small text-decoration-none">Quên?</a>
                    </div>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">Đăng Nhập</button>

                <div class="auth-divider">hoặc tiếp tục với</div>

                <div class="social-auth">
                    <a href="#" class="social-btn facebook" title="Đăng nhập bằng Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="social-btn xbox" title="Đăng nhập bằng Xbox">
                        <i class="bi bi-xbox"></i>
                    </a>
                    <a href="#" class="social-btn playstation" title="Đăng nhập bằng PlayStation">
                        <i class="bi bi-playstation"></i>
                    </a>
                </div>
            </form>

            <div class="auth-links">
                <p class="mb-0">Chưa có tài khoản? <a href="<?php echo BASE_URL; ?>auth/register">Đăng ký ngay</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>