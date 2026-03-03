<?php
$pageTitle = 'Chủ đề Game - Game Store';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4">Chủ đề Game</h2>

    <div class="row">
        <?php if (empty($categories)): ?>
            <div class="col-12">
                <div class="alert alert-info">Chưa có chủ đề nào được tạo.</div>
            </div>
        <?php else: ?>
            <?php foreach ($categories as $category): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 game-card">
                        <a href="<?php echo BASE_URL; ?>game?category=<?php echo $category['id']; ?>">
                            <?php
                            $imageUrl = !empty($category['image']) ? BASE_URL . $category['image'] : BASE_URL . 'assets/images/no-image.jpg';
                            ?>
                            <div class="category-image-container bg-dark d-flex align-items-center justify-content-center"
                                style="height: 200px; overflow: hidden;">
                                <img src="<?php echo $imageUrl; ?>" class="card-img-top w-100 h-100" style="object-fit: cover;"
                                    alt="<?php echo htmlspecialchars($category['name']); ?>"
                                    onerror="this.src='<?php echo BASE_URL; ?>assets/images/no-image.jpg'">
                            </div>
                        </a>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($category['name']); ?></h5>
                            <p class="card-text text-muted small">
                                Khám phá các tựa game thuộc chủ đề <?php echo htmlspecialchars($category['name']); ?>.
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary"><?php echo $category['game_count']; ?> Games</span>
                                <a href="<?php echo BASE_URL; ?>game?category=<?php echo $category['id']; ?>"
                                    class="btn btn-primary btn-sm">
                                    Xem ngay
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>