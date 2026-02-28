<?php
$pageTitle = 'Library của tôi - Game Store';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4"><i class="bi bi-collection-play"></i> Library của tôi</h2>

    <?php if (empty($result['data'])): ?>
        <div class="alert alert-info">
            Bạn chưa sở hữu game nào. Hãy mua game để xuất hiện trong Library.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($result['data'] as $item): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 game-card">
                        <a href="<?php echo BASE_URL; ?>game/detail?slug=<?php echo $item['slug']; ?>">
                            <img src="<?php echo BASE_URL . ($item['image'] ?? 'assets/images/no-image.jpg'); ?>"
                                 class="card-img-top"
                                 alt="<?php echo htmlspecialchars($item['title']); ?>">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="<?php echo BASE_URL; ?>game/detail?slug=<?php echo $item['slug']; ?>"
                                   class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </a>
                            </h5>
                            <p class="text-muted small mb-2">
                                Thêm vào lúc: <?php echo date('d/m/Y H:i', strtotime($item['added_at'])); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <?php if ($item['sale_price']): ?>
                                        <span class="text-decoration-line-through text-muted small">
                                            <?php echo number_format($item['price']); ?>đ
                                        </span>
                                        <span class="text-success fw-bold">
                                            <?php echo number_format($item['sale_price']); ?>đ
                                        </span>
                                    <?php else: ?>
                                        <span class="fw-bold">
                                            <?php echo number_format($item['price']); ?>đ
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <a href="<?php echo BASE_URL; ?>game/detail?slug=<?php echo $item['slug']; ?>"
                                   class="btn btn-outline-primary btn-sm">
                                    Chơi / Xem
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($result['total_pages'] > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($result['page'] > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $result['page'] - 1; ?>">Trước</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $result['total_pages']; $i++): ?>
                        <li class="page-item <?php echo $i == $result['page'] ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($result['page'] < $result['total_pages']): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $result['page'] + 1; ?>">Sau</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

