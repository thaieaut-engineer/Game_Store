<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Game Store'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
                <i class="bi bi-controller"></i> Game Store
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>">Store</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>about">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>support">Support</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-list"></i> Menu
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>#recommend">Recommend</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>game">Categories</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>ways-to-play">Ways to Play</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>#special">Special Section</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <form class="d-flex" action="<?php echo BASE_URL; ?>game" method="GET">
                            <input class="form-control me-2" type="search" name="search" placeholder="Tìm kiếm game...">
                            <button class="btn btn-outline-light" type="submit"><i class="bi bi-search"></i></button>
                        </form>
                    </li>
                    <?php 
                    // Ensure session is started
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    
                    // Check if user is logged in - check session first for immediate access
                    $isUserLoggedIn = false;
                    $currentUser = null;
                    
                    // First check session (works immediately after login)
                    if (isset($_SESSION['user_id']) && isset($_SESSION['user_name']) && !empty($_SESSION['user_id'])) {
                        // Build user from session - this works immediately after login
                        $currentUser = [
                            'id' => $_SESSION['user_id'],
                            'name' => $_SESSION['user_name'],
                            'email' => $_SESSION['user_email'] ?? '',
                            'role' => $_SESSION['user_role'] ?? 'user',
                            'avatar' => $_SESSION['user_avatar'] ?? 'default-avatar.png',
                            'status' => 1
                        ];
                        $isUserLoggedIn = true;
                    } else {
                        // Fallback to cookie/token check
                        $isUserLoggedIn = isLoggedIn();
                        if ($isUserLoggedIn) {
                            $currentUser = getCurrentUser();
                            // Update session if we got user from cookie
                            if ($currentUser) {
                                $_SESSION['user_id'] = $currentUser['id'];
                                $_SESSION['user_name'] = $currentUser['name'];
                                $_SESSION['user_email'] = $currentUser['email'];
                                $_SESSION['user_role'] = $currentUser['role'];
                                $_SESSION['user_avatar'] = $currentUser['avatar'] ?? 'default-avatar.png';
                            }
                        }
                    }
                    
                    if ($isUserLoggedIn && $currentUser): 
                        // User is logged in - show cart and user menu
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>cart" id="cart-link">
                                <i class="bi bi-cart"></i> <span class="d-none d-md-inline">Giỏ hàng</span>
                                <?php
                                // Show cart count if available
                                if (isset($currentUser['id'])) {
                                    try {
                                        require_once __DIR__ . '/../../models/Cart.php';
                                        $cartModel = new Cart();
                                        $cart = $cartModel->getOrCreateCart($currentUser['id']);
                                        $cartItems = $cartModel->getCartItems($cart['id']);
                                        $cartCount = count($cartItems);
                                        if ($cartCount > 0) {
                                            echo '<span class="badge bg-danger ms-1" id="cart-count">' . $cartCount . '</span>';
                                        }
                                    } catch (Exception $e) {
                                        // Silently fail if cart can't be loaded
                                    }
                                }
                                ?>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php 
                                // Get avatar path
                                $avatarFile = !empty($currentUser['avatar']) ? $currentUser['avatar'] : 'default-avatar.png';
                                $avatarPath = BASE_URL . 'uploads/avatars/' . $avatarFile;
                                $avatarFullPath = __DIR__ . '/../../uploads/avatars/' . $avatarFile;
                                
                                // Check if avatar file exists
                                if (!file_exists($avatarFullPath) || empty($currentUser['avatar'])) {
                                    // Try default avatar in assets
                                    $defaultAvatarPath = __DIR__ . '/../../assets/images/default-avatar.png';
                                    if (file_exists($defaultAvatarPath)) {
                                        $avatarPath = BASE_URL . 'assets/images/default-avatar.png';
                                    } else {
                                        // Use UI Avatars API as fallback
                                        $avatarPath = 'https://ui-avatars.com/api/?name=' . urlencode($currentUser['name']) . '&background=0d6efd&color=fff&size=128';
                                    }
                                }
                                ?>
                                <img src="<?php echo htmlspecialchars($avatarPath); ?>" 
                                     class="rounded-circle me-2" 
                                     width="32" 
                                     height="32" 
                                     alt="Avatar" 
                                     style="object-fit: cover; border: 2px solid rgba(255,255,255,0.3);"
                                     onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($currentUser['name']); ?>&background=0d6efd&color=fff&size=128'">
                                <span class="d-none d-md-inline"><?php echo htmlspecialchars($currentUser['name']); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <div class="px-3 py-2 d-flex align-items-center">
                                        <img src="<?php echo htmlspecialchars($avatarPath); ?>" 
                                             class="rounded-circle me-2" 
                                             width="40" 
                                             height="40" 
                                             alt="Avatar" 
                                             style="object-fit: cover;"
                                             onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($currentUser['name']); ?>&background=0d6efd&color=fff&size=128'">
                                        <div>
                                            <strong class="d-block"><?php echo htmlspecialchars($currentUser['name']); ?></strong>
                                            <small class="text-muted"><?php echo htmlspecialchars($currentUser['email']); ?></small>
                                        </div>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>profile">
                                    <i class="bi bi-person me-2"></i> Tài khoản
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>library">
                                    <i class="bi bi-collection-play me-2"></i> Library
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>order/history">
                                    <i class="bi bi-receipt me-2"></i> Đơn hàng
                                </a></li>
                                <?php if (isAdmin()): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>admin/dashboard">
                                        <i class="bi bi-speedometer2 me-2"></i> Admin Panel
                                    </a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>auth/logout">
                                    <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                                </a></li>
                            </ul>
                        </li>
                    <?php else: 
                        // User is NOT logged in - show login and register buttons
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>auth/login">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>auth/register">
                                <i class="bi bi-person-plus me-1"></i> Đăng ký
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert" id="success-alert">
            <i class="bi bi-check-circle me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert" id="error-alert">
            <i class="bi bi-exclamation-circle me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) < 5): ?>
        <div class="alert alert-info alert-dismissible fade show m-3" role="alert" id="welcome-alert">
            <i class="bi bi-info-circle me-2"></i>Chào mừng bạn đến với Game Store! Bạn có thể bắt đầu mua sắm ngay.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
