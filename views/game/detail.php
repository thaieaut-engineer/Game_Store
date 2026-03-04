<?php
$pageTitle = $game['title'] . ' - Game Store';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container my-5">
    <!-- Game Header: Title & Rating -->
    <div class="row mb-4">
        <div class="col-12 text-center text-md-start">
            <h1 class="display-4 fw-bold"><?php echo $game['title']; ?></h1>
            <?php if ($ratingStats && $ratingStats['total_reviews'] > 0): ?>
                <div class="d-flex align-items-center justify-content-center justify-content-md-start mt-2">
                    <div class="text-warning me-2">
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                            <i class="bi bi-star<?php echo $i <= round($ratingStats['avg_rating']) ? '-fill' : ''; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="fw-bold fs-5"><?php echo number_format($ratingStats['avg_rating'], 1); ?>/10</span>
                    <span class="ms-2 text-muted">(<?php echo $ratingStats['total_reviews']; ?> đánh giá)</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Media Gallery -->
            <div class="media-gallery mb-4">
                <!-- Main Preview -->
                <div class="gallery-main ratio ratio-16x9 mb-3 shadow rounded overflow-hidden bg-dark">
                    <?php if ($game['video_url']): ?>
                        <video id="gallery-video" controls class="w-100 h-100">
                            <source src="<?php echo BASE_URL . $game['video_url']; ?>" type="video/mp4">
                        </video>
                        <img id="gallery-image" src="" class="w-100 h-100 d-none" style="object-fit: contain;"
                            alt="Main View">
                    <?php elseif (!empty($images)): ?>
                        <img id="gallery-image" src="<?php echo BASE_URL . $images[0]['image_url']; ?>" class="w-100 h-100"
                            style="object-fit: contain;" alt="Main View">
                        <video id="gallery-video" controls class="w-100 h-100 d-none">
                            <source src="" type="video/mp4">
                        </video>
                    <?php else: ?>
                        <img src="<?php echo BASE_URL; ?>assets/images/no-image.jpg" class="w-100 h-100"
                            style="object-fit: contain;" alt="No Image">
                    <?php endif; ?>
                </div>

                <!-- Thumbnails -->
                <div class="thumbnail-list d-flex gap-2 overflow-auto pb-2">
                    <?php if ($game['video_url']): ?>
                        <div class="thumb-item active" data-type="video"
                            data-src="<?php echo BASE_URL . $game['video_url']; ?>">
                            <div class="ratio ratio-16x9 position-relative">
                                <video class="w-100 h-100 object-fit-cover">
                                    <source src="<?php echo BASE_URL . $game['video_url']; ?>" type="video/mp4">
                                </video>
                                <div class="position-absolute top-50 left-50 translate-middle text-white">
                                    <i class="bi bi-play-circle-fill fs-2"></i>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($images)): ?>
                        <?php foreach ($images as $index => $image): ?>
                            <div class="thumb-item <?php echo (!$game['video_url'] && $index === 0) ? 'active' : ''; ?>"
                                data-type="image" data-src="<?php echo BASE_URL . $image['image_url']; ?>">
                                <div class="ratio ratio-16x9">
                                    <img src="<?php echo BASE_URL . $image['image_url']; ?>"
                                        class="w-100 h-100 object-fit-cover" alt="Thumbnail">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Action Area: Price & Purchase -->
            <div class="action-area p-4 bg-light rounded shadow-sm mb-4 border">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="price-display">
                            <?php if ($game['sale_price']): ?>
                                <span
                                    class="text-decoration-line-through text-muted small"><?php echo number_format($game['price']); ?>đ</span>
                                <h2 class="text-danger mb-0 fw-bold"><?php echo number_format($game['sale_price']); ?>đ</h2>
                            <?php else: ?>
                                <h2 class="mb-0 fw-bold"><?php echo number_format($game['price']); ?>đ</h2>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <?php if (isLoggedIn()): ?>
                                <?php if (!empty($hasInLibrary)): ?>
                                    <a class="btn btn-success btn-lg px-4" href="<?php echo BASE_URL; ?>library">
                                        <i class="bi bi-play-circle"></i> Chơi ngay
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-primary btn-lg px-4 add-to-cart"
                                        data-game-id="<?php echo $game['id']; ?>">
                                        <i class="bi bi-cart-plus"></i> Thêm Giỏ Hàng
                                    </button>
                                    <a class="btn btn-danger btn-lg px-4"
                                        href="<?php echo BASE_URL; ?>order/checkout?buy_now=<?php echo $game['id']; ?>">
                                        <i class="bi bi-credit-card"></i> Mua Ngay
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <a class="btn btn-danger btn-lg px-4"
                                    href="<?php echo BASE_URL; ?>order/checkout?buy_now=<?php echo $game['id']; ?>">
                                    <i class="bi bi-credit-card"></i> Mua Ngay
                                </a>
                                <a class="btn btn-outline-primary btn-lg px-4"
                                    href="<?php echo BASE_URL; ?>auth/login?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">
                                    Đăng nhập để mua
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if (!empty($categories)): ?>
                    <div class="mt-3 pt-3 border-top">
                        <span class="text-muted me-2">Chủ đề:</span>
                        <?php foreach ($categories as $cat): ?>
                            <a class="badge bg-secondary text-decoration-none me-1"
                                href="<?php echo BASE_URL; ?>game?category=<?php echo (int) $cat['id']; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <style>
                .thumbnail-list::-webkit-scrollbar {
                    height: 5px;
                }

                .thumbnail-list::-webkit-scrollbar-track {
                    background: #f1f1f1;
                }

                .thumbnail-list::-webkit-scrollbar-thumb {
                    background: #888;
                    border-radius: 10px;
                }

                .thumb-item {
                    min-width: 154px;
                    cursor: pointer;
                    border: 3px solid transparent;
                    border-radius: 6px;
                    overflow: hidden;
                    transition: all 0.2s ease;
                    opacity: 0.7;
                    background: #000;
                }

                .thumb-item:hover {
                    opacity: 1;
                    transform: scale(1.05);
                }

                .thumb-item.active {
                    border-color: #0d6efd;
                    opacity: 1;
                    box-shadow: 0 0 10px rgba(13, 110, 253, 0.5);
                }

                .object-fit-cover {
                    object-fit: cover;
                }

                .game-description img {
                    max-width: 100%;
                    height: auto;
                }

                .game-card .ratio img {
                    height: 100% !important;
                }
            </style>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const mainImage = document.getElementById('gallery-image');
                    const mainVideo = document.getElementById('gallery-video');
                    const thumbItems = document.querySelectorAll('.thumb-item');
                    thumbItems.forEach(item => {
                        item.addEventListener('click', function () {
                            const type = this.getAttribute('data-type');
                            const src = this.getAttribute('data-src');
                            thumbItems.forEach(t => t.classList.remove('active'));
                            this.classList.add('active');
                            if (type === 'video') {
                                if (mainImage) mainImage.classList.add('d-none');
                                if (mainVideo) {
                                    mainVideo.classList.remove('d-none');
                                    mainVideo.querySelector('source').src = src;
                                    mainVideo.load();
                                    mainVideo.play();
                                }
                            } else {
                                if (mainVideo) { mainVideo.pause(); mainVideo.classList.add('d-none'); }
                                if (mainImage) { mainImage.classList.remove('d-none'); mainImage.src = src; }
                            }
                        });
                    });
                });
            </script>

            <!-- Description -->
            <div class="mb-5">
                <h3 class="border-bottom pb-2 mb-3">Mô Tả</h3>
                <div class="game-description fs-5"><?php echo $game['description']; ?></div>
            </div>

            <!-- System Requirements -->
            <?php if ($game['system_requirements']): ?>
                <div class="mb-5">
                    <h3 class="border-bottom pb-2 mb-3">Cấu Hình Đề Xuất</h3>
                    <div class="bg-light p-3 rounded border"><?php echo $game['system_requirements']; ?></div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <!-- Sidebar info area -->
            <div class="card mb-4 border-0 bg-transparent shadow-none">
                <div class="card-body p-0">
                    <h5 class="border-bottom pb-2 mb-3">Thông Tin Chi Tiết</h5>
                    <ul class="list-unstyled">
                        <li class="mb-3 d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted">Ngày phát hành:</span>
                            <span><?php echo date('d/m/Y', strtotime($game['created_at'])); ?></span>
                        </li>
                        <li class="mb-3 d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted">Lượt mua:</span>
                            <span><?php echo number_format($game['sales_count'] ?? 0); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews -->
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="border-bottom pb-2 mb-4">Đánh Giá và Bình Luận</h3>

            <?php if (isLoggedIn()): ?>
                <div class="card mb-4 border-0 bg-light shadow-sm">
                    <div class="card-body">
                        <h5>Viết đánh giá của bạn</h5>
                        <form action="<?php echo BASE_URL; ?>review/create" method="POST">
                            <input type="hidden" name="game_id" value="<?php echo $game['id']; ?>">
                            <input type="hidden" name="game_slug" value="<?php echo $game['slug']; ?>">

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Điểm đánh giá (1-10):</label>
                                    <select name="rating" class="form-select" required>
                                        <?php for ($i = 10; $i >= 1; $i--): ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?> / 10</option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Bình luận của bạn:</label>
                                    <textarea name="comment" class="form-control" rows="4"
                                        placeholder="Chia sẻ cảm nghĩ của bạn về game..." required></textarea>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary px-4">Gửi đánh giá</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Reviews List -->
            <div class="reviews-list">
                <?php if (!empty($reviews['data'])): ?>
                    <?php foreach ($reviews['data'] as $review): ?>
                        <div class="card mb-3 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="<?php echo BASE_URL . 'uploads/avatars/' . ($review['avatar'] ?? 'default.png'); ?>"
                                        class="rounded-circle me-3 border" width="48" height="48" alt="Avatar">
                                    <div>
                                        <h6 class="mb-0 fw-bold"><?php echo $review['user_name']; ?></h6>
                                        <div class="text-warning small">
                                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                                <i class="bi bi-star<?php echo $i <= $review['rating'] ? '-fill' : ''; ?>"></i>
                                            <?php endfor; ?>
                                            <span class="text-dark ms-2 fw-bold"><?php echo $review['rating']; ?>/10</span>
                                        </div>
                                    </div>
                                    <small
                                        class="text-muted ms-auto"><?php echo date('d/m/Y H:i', strtotime($review['created_at'])); ?></small>
                                </div>
                                <div class="review-content p-2 bg-light rounded italic">
                                    "<?php echo nl2br(htmlspecialchars($review['comment'])); ?>"
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-4 bg-light rounded border border-dashed">
                        <i class="bi bi-chat-dots fs-1 text-muted"></i>
                        <p class="mt-2 text-muted">Chưa có đánh giá nào. Hãy là người đầu tiên!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Related Games by Categories -->
    <?php if (!empty($relatedGames)): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="border-bottom pb-2 mb-4">
                    <i class="bi bi-controller"></i> Game Cùng Chủ Đề
                </h3>
                <div class="row">
                    <?php foreach ($relatedGames as $relatedGame): ?>
                        <div class="col-md-3 mb-4">
                            <div class="card h-100 game-card border-0 shadow-sm overflow-hidden">
                                <a href="<?php echo BASE_URL; ?>game/detail?slug=<?php echo $relatedGame['slug']; ?>">
                                    <?php
                                    require_once __DIR__ . '/../../models/GameImage.php';
                                    $imageModel = new GameImage();
                                    $relatedImages = $imageModel->getByGameId($relatedGame['id']);
                                    $relatedImageUrl = !empty($relatedImages) ? BASE_URL . $relatedImages[0]['image_url'] : BASE_URL . 'assets/images/no-image.jpg';
                                    ?>
                                    <div class="ratio ratio-16x9">
                                        <img src="<?php echo $relatedImageUrl; ?>" class="card-img-top object-fit-cover h-100"
                                            alt="<?php echo $relatedGame['title']; ?>">
                                    </div>
                                </a>
                                <div class="card-body">
                                    <h5 class="card-title text-truncate">
                                        <a href="<?php echo BASE_URL; ?>game/detail?slug=<?php echo $relatedGame['slug']; ?>"
                                            class="text-decoration-none text-dark fw-bold">
                                            <?php echo $relatedGame['title']; ?>
                                        </a>
                                    </h5>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>
                                            <?php if ($relatedGame['sale_price']): ?>
                                                <span
                                                    class="text-danger fw-bold fs-5"><?php echo number_format($relatedGame['sale_price']); ?>đ</span>
                                            <?php else: ?>
                                                <span
                                                    class="fw-bold fs-5"><?php echo number_format($relatedGame['price']); ?>đ</span>
                                            <?php endif; ?>
                                        </div>
                                        <button class="btn btn-outline-primary btn-sm add-to-cart"
                                            data-game-id="<?php echo $relatedGame['id']; ?>">
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>