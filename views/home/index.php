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

<!-- Recommended Games -->
<section id="recommend" class="mb-5">
    <div class="container">
        <h2 class="mb-4"><i class="bi bi-star-fill text-warning"></i> Game Đề Xuất</h2>
        <div class="row">
            <?php foreach ($recommendedGames as $game): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 game-card">
                        <a href="<?php echo BASE_URL; ?>game/detail?slug=<?php echo $game['slug']; ?>">
                            <?php
                            require_once __DIR__ . '/../../models/GameImage.php';
                            $imageModel = new GameImage();
                            $images = $imageModel->getByGameId($game['id']);
                            $imageUrl = !empty($images) ? BASE_URL . $images[0]['image_url'] : BASE_URL . 'assets/images/no-image.jpg';
                            ?>
                            <img src="<?php echo $imageUrl; ?>" class="card-img-top" alt="<?php echo $game['title']; ?>">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $game['title']; ?></h5>
                            <p class="card-text text-muted small">
                                <?php echo substr($game['short_description'] ?? '', 0, 100); ?>...
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <?php if ($game['sale_price']): ?>
                                        <span
                                            class="text-decoration-line-through text-muted small"><?php echo number_format($game['price']); ?>đ</span>
                                        <span
                                            class="text-danger fw-bold"><?php echo number_format($game['sale_price']); ?>đ</span>
                                    <?php else: ?>
                                        <span class="fw-bold"><?php echo number_format($game['price']); ?>đ</span>
                                    <?php endif; ?>
                                </div>

                                <?php if (in_array($game['id'], $ownedGameIds)): ?>
                                    <a class="btn btn-success btn-sm" href="<?php echo BASE_URL; ?>library">
                                        <i class="bi bi-play-circle"></i>
                                    </a>
                                <?php elseif (in_array($game['id'], $cartGameIds)): ?>
                                    <a class="btn btn-info btn-sm" href="<?php echo BASE_URL; ?>cart">
                                        <i class="bi bi-cart-check"></i>
                                    </a>
                                <?php elseif (isLoggedIn()): ?>
                                    <button class="btn btn-primary btn-sm add-to-cart"
                                        data-game-id="<?php echo $game['id']; ?>">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                <?php else: ?>
                                    <a class="btn btn-outline-primary btn-sm" href="<?php echo BASE_URL; ?>auth/login">
                                        <i class="bi bi-box-arrow-in-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Upcoming Games -->
<section class="mb-5">
    <div class="container">
        <h2 class="mb-4"><i class="bi bi-calendar-event text-info"></i> Game Sắp Ra Mắt</h2>
        <div class="row">
            <?php foreach ($upcomingGames as $game): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 game-card">
                        <a href="<?php echo BASE_URL; ?>game/detail?slug=<?php echo $game['slug']; ?>">
                            <?php
                            $imageModel = new GameImage();
                            $images = $imageModel->getByGameId($game['id']);
                            $imageUrl = !empty($images) ? BASE_URL . $images[0]['image_url'] : BASE_URL . 'assets/images/no-image.jpg';
                            ?>
                            <img src="<?php echo $imageUrl; ?>" class="card-img-top" alt="<?php echo $game['title']; ?>">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $game['title']; ?></h5>
                            <p class="text-info small">Ra mắt:
                                <?php echo date('d/m/Y', strtotime($game['release_date'])); ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Sale Games -->
<section class="mb-5">
    <div class="container">
        <h2 class="mb-4"><i class="bi bi-tag-fill text-danger"></i> Game Đang Giảm Giá</h2>
        <div class="row">
            <?php foreach ($saleGames as $game): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 game-card">
                        <a href="<?php echo BASE_URL; ?>game/detail?slug=<?php echo $game['slug']; ?>">
                            <?php
                            $imageModel = new GameImage();
                            $images = $imageModel->getByGameId($game['id']);
                            $imageUrl = !empty($images) ? BASE_URL . $images[0]['image_url'] : BASE_URL . 'assets/images/no-image.jpg';
                            ?>
                            <img src="<?php echo $imageUrl; ?>" class="card-img-top" alt="<?php echo $game['title']; ?>">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $game['title']; ?></h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span
                                        class="text-decoration-line-through text-muted small"><?php echo number_format($game['price']); ?>đ</span>
                                    <span
                                        class="text-danger fw-bold"><?php echo number_format($game['sale_price']); ?>đ</span>
                                </div>

                                <?php if (in_array($game['id'], $ownedGameIds)): ?>
                                    <a class="btn btn-success btn-sm" href="<?php echo BASE_URL; ?>library">
                                        <i class="bi bi-play-circle"></i>
                                    </a>
                                <?php elseif (in_array($game['id'], $cartGameIds)): ?>
                                    <a class="btn btn-info btn-sm" href="<?php echo BASE_URL; ?>cart">
                                        <i class="bi bi-cart-check"></i>
                                    </a>
                                <?php elseif (isLoggedIn()): ?>
                                    <button class="btn btn-danger btn-sm add-to-cart" data-game-id="<?php echo $game['id']; ?>">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                <?php else: ?>
                                    <a class="btn btn-outline-danger btn-sm" href="<?php echo BASE_URL; ?>auth/login">
                                        <i class="bi bi-box-arrow-in-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Popular Categories -->
<section id="special" class="mb-5">
    <div class="container">
        <h2 class="mb-4"><i class="bi bi-grid-3x3-gap text-success"></i> Chủ Đề Phổ Biến</h2>
        <div class="row">
            <?php foreach ($popularCategories as $category): ?>
                <div class="col-md-3 mb-4">
                    <div class="card category-card">
                        <a href="<?php echo BASE_URL; ?>game?category=<?php echo $category['id']; ?>">
                            <img src="<?php echo BASE_URL . ($category['image'] ?? 'assets/images/no-image.jpg'); ?>"
                                class="card-img-top" alt="<?php echo $category['name']; ?>">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?php echo $category['name']; ?></h5>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>