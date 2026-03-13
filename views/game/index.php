<?php
$pageTitle = 'Danh sách Game - Game Store';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4 d-flex justify-content-between align-items-center">
        <span>
            Danh Sách Game
            <?php if (!empty($currentCategory)): ?>
                <small class="text-muted">/ <?php echo htmlspecialchars($currentCategory['name']); ?></small>
            <?php endif; ?>
        </span>
    </h2>

    <!-- Bộ lọc -->
    <div class="card mb-4">
        <div class="card-body">
            <form class="row g-3 align-items-end" method="GET" action="<?php echo BASE_URL; ?>game">
                <div class="col-md-4">
                    <label class="form-label">Từ khóa</label>
                    <input type="text" name="search" class="form-control" placeholder="Nhập tên game..."
                        value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Chủ đề</label>
                    <select name="category" class="form-select">
                        <option value="">Tất cả</option>
                        <?php if (!empty($allCategories)): ?>
                            <?php foreach ($allCategories as $cat): ?>
                                <option value="<?php echo (int) $cat['id']; ?>"
                                    <?php echo (!empty($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Loại</label>
                    <select name="type" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="recommended" <?php echo (($_GET['type'] ?? '') === 'recommended') ? 'selected' : ''; ?>>
                            Game đề xuất
                        </option>
                        <option value="sale" <?php echo (($_GET['type'] ?? '') === 'sale') ? 'selected' : ''; ?>>
                            Đang giảm giá
                        </option>
                        <option value="upcoming" <?php echo (($_GET['type'] ?? '') === 'upcoming') ? 'selected' : ''; ?>>
                            Sắp ra mắt
                        </option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel"></i> Lọc
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <?php if (empty($result['data'])): ?>
            <div class="col-12">
                <div class="alert alert-info">Không tìm thấy game nào.</div>
            </div>
        <?php else: ?>
            <?php foreach ($result['data'] as $game): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 game-card">
                        <a href="<?php echo BASE_URL; ?>game/detail?slug=<?php echo $game['slug']; ?>" class="game-image-link">
                            <?php if ($game['discount_percent'] > 0): ?>
                                <span class="badge bg-danger position-absolute top-0 end-0 m-2" style="z-index:10">-<?php echo $game['discount_percent']; ?>%</span>
                            <?php endif; ?>
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
                                            class="text-decoration-line-through text-muted"><?php echo number_format($game['price']); ?>đ</span>
                                        <span class="text-danger fw-bold"><?php echo number_format($game['sale_price']); ?>đ</span>
                                    <?php else: ?>
                                        <span class="fw-bold"><?php echo number_format($game['price']); ?>đ</span>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($game['is_upcoming'])): ?>
                                    <span class="badge bg-secondary">Sắp ra mắt</span>
                                <?php elseif (in_array($game['id'], $ownedGameIds)): ?>
                                    <a class="btn btn-success btn-sm" href="<?php echo BASE_URL; ?>library">
                                        <i class="bi bi-play-circle"></i>
                                    </a>
                                <?php elseif (in_array($game['id'], $cartGameIds)): ?>
                                    <a class="btn btn-info btn-sm" href="<?php echo BASE_URL; ?>cart">
                                        <i class="bi bi-cart-check"></i>
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
            <?php
            $searchParam = !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '';
            $categoryParam = !empty($_GET['category']) ? '&category=' . urlencode($_GET['category']) : '';
            $typeParam = !empty($_GET['type']) ? '&type=' . urlencode($_GET['type']) : '';
            ?>
            <?php if ($result['page'] > 1): ?>
                <li class="page-item">
                    <a class="page-link"
                        href="?page=<?php echo $result['page'] - 1; ?><?php echo $searchParam . $categoryParam . $typeParam; ?>">Trước</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $result['total_pages']; $i++): ?>
                <li class="page-item <?php echo $i == $result['page'] ? 'active' : ''; ?>">
                    <a class="page-link"
                        href="?page=<?php echo $i; ?><?php echo $searchParam . $categoryParam . $typeParam; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($result['page'] < $result['total_pages']): ?>
                <li class="page-item">
                    <a class="page-link"
                        href="?page=<?php echo $result['page'] + 1; ?><?php echo $searchParam . $categoryParam . $typeParam; ?>">Sau</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>