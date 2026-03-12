<?php
$pageTitle = 'Trang chủ - Game Store';
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Banner Video -->
<section class="banner-video mb-5">
    <div class="container-fluid p-0">
        <div class="banner-wrapper">
            <video autoplay muted loop playsinline>
                <source src="<?php echo BASE_URL; ?>assets/videos/banner.mp4" type="video/mp4">
            </video>
        </div>
    </div>
</section>

<!-- Recommended Games Carousel -->
<section id="recommend" class="mb-5">
    <div class="container-fluid px-md-5">
        <div class="section-header">
            <h2 class="section-title">
                <a href="<?php echo BASE_URL; ?>game?type=recommended">
                    <i class="bi bi-star-fill text-warning"></i> Game Đề Xuất
                </a>
            </h2>
            <div class="carousel-controls">
                <button class="carousel-btn prev-btn"><i class="bi bi-chevron-left"></i></button>
                <button class="carousel-btn next-btn"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
        
        <div class="carousel-container">
            <div class="carousel-track">
                <?php 
                require_once __DIR__ . '/../../models/GameImage.php';
                $imageModel = new GameImage();
                foreach ($recommendedGames as $game): 
                    $images = $imageModel->getByGameId($game['id']);
                    $imageUrl = !empty($images) ? BASE_URL . $images[0]['image_url'] : BASE_URL . 'assets/images/no-image.jpg';
                ?>
                    <div class="carousel-item-custom">
                        <div class="card game-card shadow-sm border-0">
                            <a href="<?php echo BASE_URL; ?>game/detail?slug=<?php echo $game['slug']; ?>" class="game-image-link">
                                <img src="<?php echo $imageUrl; ?>" class="card-img-top" alt="<?php echo $game['title']; ?>">
                            </a>
                            <div class="card-body">
                                <h6 class="card-title"><?php echo $game['title']; ?></h6>
                                <div class="price-container d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php if ($game['sale_price']): ?>
                                            <span class="text-decoration-line-through text-muted small"><?php echo number_format($game['price']); ?>đ</span>
                                            <span class="text-danger fw-bold d-block"><?php echo number_format($game['sale_price']); ?>đ</span>
                                        <?php else: ?>
                                            <span class="fw-bold d-block"><?php echo number_format($game['price']); ?>đ</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (in_array($game['id'], $ownedGameIds)): ?>
                                        <a class="btn btn-success btn-sm" href="<?php echo BASE_URL; ?>library">
                                            <i class="bi bi-play-fill"></i>
                                        </a>
                                    <?php elseif (in_array($game['id'], $cartGameIds)): ?>
                                        <a class="btn btn-info btn-sm text-white" href="<?php echo BASE_URL; ?>cart">
                                            <i class="bi bi-cart-check-fill"></i>
                                        </a>
                                    <?php elseif (isLoggedIn()): ?>
                                        <button class="btn btn-primary btn-sm add-to-cart" data-game-id="<?php echo $game['id']; ?>">
                                            <i class="bi bi-cart-plus-fill"></i>
                                        </button>
                                    <?php else: ?>
                                        <a class="btn btn-outline-primary btn-sm" href="<?php echo BASE_URL; ?>auth/login">
                                            <i class="bi bi-cart-plus"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Upcoming Games Sliding Banner -->
<section id="upcoming" class="mb-5">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <a href="<?php echo BASE_URL; ?>game?type=upcoming">
                    <i class="bi bi-calendar-event text-info"></i> Game Sắp Ra Mắt
                </a>
            </h2>
            <div class="carousel-controls">
                <button class="carousel-btn prev-btn"><i class="bi bi-chevron-left"></i></button>
                <button class="carousel-btn next-btn"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>

        <div class="carousel-container banner-carousel-container">
            <div class="carousel-track banner-carousel-track">
                <?php 
                foreach ($upcomingGames as $game): 
                    $images = $imageModel->getByGameId($game['id']);
                    $imageUrl = !empty($images) ? BASE_URL . $images[0]['image_url'] : BASE_URL . 'assets/images/no-image.jpg';
                ?>
                    <a href="<?php echo BASE_URL; ?>game/detail?slug=<?php echo $game['slug']; ?>" class="banner-slide">
                        <img src="<?php echo $imageUrl; ?>" class="w-100 h-100 object-fit-cover" alt="<?php echo $game['title']; ?>">
                        <div class="upcoming-banner-content">
                            <span class="upcoming-tag">Sắp ra mắt</span>
                            <h2 class="display-5 fw-bold mb-2"><?php echo $game['title']; ?></h2>
                            <p class="lead mb-3"><?php echo htmlspecialchars(substr($game['short_description'] ?? '', 0, 150)); ?>...</p>
                            <div class="fs-5">
                                <i class="bi bi-calendar-check me-2"></i> Phát hành: <?php echo date('d/m/Y', strtotime($game['release_date'])); ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Sale Games Carousel -->
<section id="sale" class="mb-5">
    <div class="container-fluid px-md-5">
        <div class="section-header">
            <h2 class="section-title">
                <a href="<?php echo BASE_URL; ?>game?type=sale">
                    <i class="bi bi-tag-fill text-danger"></i> Game Đang Giảm Giá
                </a>
            </h2>
            <div class="carousel-controls">
                <button class="carousel-btn prev-btn"><i class="bi bi-chevron-left"></i></button>
                <button class="carousel-btn next-btn"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
        
        <div class="carousel-container">
            <div class="carousel-track">
                <?php 
                foreach ($saleGames as $game): 
                    $images = $imageModel->getByGameId($game['id']);
                    $imageUrl = !empty($images) ? BASE_URL . $images[0]['image_url'] : BASE_URL . 'assets/images/no-image.jpg';
                ?>
                    <div class="carousel-item-custom">
                        <div class="card game-card shadow-sm border-0">
                            <a href="<?php echo BASE_URL; ?>game/detail?slug=<?php echo $game['slug']; ?>" class="game-image-link">
                                <img src="<?php echo $imageUrl; ?>" class="card-img-top" alt="<?php echo $game['title']; ?>">
                            </a>
                            <div class="card-body">
                                <h6 class="card-title"><?php echo $game['title']; ?></h6>
                                <div class="price-container d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php if ($game['sale_price']): ?>
                                            <span class="text-decoration-line-through text-muted small"><?php echo number_format($game['price']); ?>đ</span>
                                            <span class="text-danger fw-bold d-block"><?php echo number_format($game['sale_price']); ?>đ</span>
                                        <?php else: ?>
                                            <span class="fw-bold d-block"><?php echo number_format($game['price']); ?>đ</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (in_array($game['id'], $ownedGameIds)): ?>
                                        <a class="btn btn-success btn-sm" href="<?php echo BASE_URL; ?>library">
                                            <i class="bi bi-play-fill"></i>
                                        </a>
                                    <?php elseif (in_array($game['id'], $cartGameIds)): ?>
                                        <a class="btn btn-info btn-sm text-white" href="<?php echo BASE_URL; ?>cart">
                                            <i class="bi bi-cart-check-fill"></i>
                                        </a>
                                    <?php elseif (isLoggedIn()): ?>
                                        <button class="btn btn-primary btn-sm add-to-cart" data-game-id="<?php echo $game['id']; ?>">
                                            <i class="bi bi-cart-plus-fill"></i>
                                        </button>
                                    <?php else: ?>
                                        <a class="btn btn-outline-primary btn-sm" href="<?php echo BASE_URL; ?>auth/login">
                                            <i class="bi bi-cart-plus"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Popular Categories -->
<section id="special" class="mb-5">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <a href="<?php echo BASE_URL; ?>category">
                    <i class="bi bi-grid-3x3-gap text-success"></i> Chủ Đề Phổ Biến
                </a>
            </h2>
            <div class="carousel-controls">
                <button class="carousel-btn prev-btn"><i class="bi bi-chevron-left"></i></button>
                <button class="carousel-btn next-btn"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
        
        <div class="carousel-container">
            <div class="carousel-track">
                <?php foreach ($popularCategories as $category): ?>
                    <div class="carousel-item-custom">
                        <a href="<?php echo BASE_URL; ?>game?category=<?php echo $category['id']; ?>" class="text-decoration-none">
                            <div class="category-card-custom">
                                <img src="<?php echo BASE_URL . ($category['image'] ?? 'assets/images/no-image.jpg'); ?>" alt="<?php echo $category['name']; ?>">
                                <div class="category-overlay">
                                    <span><?php echo $category['name']; ?></span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>