<?php
$pageTitle = 'Danh sách Game - Game Store';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4">
        Danh Sách Game
        <?php if (!empty($currentCategory)): ?>
            <small class="text-muted">/ <?php echo htmlspecialchars($currentCategory['name']); ?></small>
        <?php endif; ?>
    </h2>

    <div class="row">
        <?php if (empty($result['data'])): ?>
            <div class="col-12">
                <div class="alert alert-info">Không tìm thấy game nào.</div>
            </div>
        <?php else: ?>
            <?php foreach ($result['data'] as $game): ?>
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
                                <?php echo substr($game['short_description'] ?? '', 0, 100); ?>...</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <?php if ($game['sale_price']): ?>
                                        <span
                                            class="text-decoration-line-through text-muted"><?php echo number_format($game['price']); ?>đ</span>
                                        <span class="text-danger fw-bold"><?php echo number_format($game['sale_price']); ?>đ</span>
                                    <?php else: ?>
                                        <span class="fw-bold"><?php echo number_format($game['price']); ?>đ</span>
                                    <?php endif; ?>
                                </div>
                                <?php if (in_array($game['id'], $ownedGameIds)): ?>
                                    <a class="btn btn-success btn-sm" href="<?php echo BASE_URL; ?>library">
                                        <i class="bi bi-play-circle"></i>
                                    </a>
                                <?php elseif (isLoggedIn()): ?>
                                    <button class="btn btn-primary btn-sm add-to-cart" data-game-id="<?php echo $game['id']; ?>">
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
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($result['total_pages'] > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if ($result['page'] > 1): ?>
                    <li class="page-item">
                        <a class="page-link"
                            href="?page=<?php echo $result['page'] - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($_GET['category']) ? '&category=' . urlencode($_GET['category']) : ''; ?>">Trước</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $result['total_pages']; $i++): ?>
                    <li class="page-item <?php echo $i == $result['page'] ? 'active' : ''; ?>">
                        <a class="page-link"
                            href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($_GET['category']) ? '&category=' . urlencode($_GET['category']) : ''; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($result['page'] < $result['total_pages']): ?>
                    <li class="page-item">
                        <a class="page-link"
                            href="?page=<?php echo $result['page'] + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($_GET['category']) ? '&category=' . urlencode($_GET['category']) : ''; ?>">Sau</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>