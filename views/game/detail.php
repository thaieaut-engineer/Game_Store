<?php
$pageTitle = $game['title'] . ' - Game Store';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-8">
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

                            // Update active state
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
                                if (mainVideo) {
                                    mainVideo.pause();
                                    mainVideo.classList.add('d-none');
                                }
                                if (mainImage) {
                                    mainImage.classList.remove('d-none');
                                    mainImage.src = src;
                                }
                            }
                        });
                    });
                });
            </script>

            <!-- Description -->
            <div class="mb-4">
                <h3>Mô Tả</h3>
                <div><?php echo $game['description']; ?></div>
            </div>

            <!-- System Requirements -->
            <?php if ($game['system_requirements']): ?>
                <div class="mb-4">
                    <h3>Cấu Hình Đề Xuất</h3>
                    <div><?php echo $game['system_requirements']; ?></div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-body">
                    <h2 class="card-title"><?php echo $game['title']; ?></h2>

                    <!-- Price -->
                    <div class="mb-3">
                        <?php if ($game['sale_price']): ?>
                            <span
                                class="text-decoration-line-through text-muted"><?php echo number_format($game['price']); ?>đ</span>
                            <h3 class="text-danger"><?php echo number_format($game['sale_price']); ?>đ</h3>
                        <?php else: ?>
                            <h3><?php echo number_format($game['price']); ?>đ</h3>
                        <?php endif; ?>
                    </div>

                    <!-- Rating -->
                    <?php if ($ratingStats && $ratingStats['total_reviews'] > 0): ?>
                        <div class="mb-3">
                            <div class="d-flex align-items-center">
                                <span
                                    class="fs-4 fw-bold me-2"><?php echo number_format($ratingStats['avg_rating'], 1); ?></span>
                                <div class="text-warning">
                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                        <i
                                            class="bi bi-star<?php echo $i <= round($ratingStats['avg_rating']) ? '-fill' : ''; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="ms-2 text-muted">(<?php echo $ratingStats['total_reviews']; ?> đánh giá)</span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Add to Cart / Play -->
                    <?php if (isLoggedIn()): ?>
                        <?php if (!empty($hasInLibrary)): ?>
                            <a class="btn btn-success btn-lg w-100 mb-3" href="<?php echo BASE_URL; ?>library">
                                <i class="bi bi-play-circle"></i> Chơi ngay
                            </a>
                            <p class="text-center text-muted small mb-0">
                                Bạn đã sở hữu game này trong Library.
                            </p>
                        <?php else: ?>
                            <button class="btn btn-primary btn-lg w-100 mb-3 add-to-cart"
                                data-game-id="<?php echo $game['id']; ?>">
                                <i class="bi bi-cart-plus"></i> Thêm Vào Giỏ Hàng
                            </button>
                        <?php endif; ?>
                    <?php else: ?>
                        <a class="btn btn-outline-primary btn-lg w-100 mb-3" href="<?php echo BASE_URL; ?>auth/login">
                            <i class="bi bi-box-arrow-in-right"></i> Đăng nhập để mua
                        </a>
                    <?php endif; ?>

                    <!-- Categories -->
                    <?php if (!empty($categories)): ?>
                        <div class="mb-3">
                            <strong>Chủ đề:</strong>
                            <?php foreach ($categories as $cat): ?>
                                <a class="badge bg-secondary text-decoration-none"
                                    href="<?php echo BASE_URL; ?>game?category=<?php echo (int) $cat['id']; ?>">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews -->
    <div class="row mt-5">
        <div class="col-12">
            <h3>Đánh Giá và Bình Luận</h3>

            <?php if (isLoggedIn()): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5>Viết đánh giá</h5>
                        <form action="<?php echo BASE_URL; ?>review/create" method="POST">
                            <input type="hidden" name="game_id" value="<?php echo $game['id']; ?>">
                            <input type="hidden" name="game_slug" value="<?php echo $game['slug']; ?>">

                            <div class="mb-3">
                                <label>Điểm đánh giá (1-10):</label>
                                <select name="rating" class="form-select" required>
                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Bình luận:</label>
                                <textarea name="comment" class="form-control" rows="3" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Reviews List -->
            <?php if (!empty($reviews['data'])): ?>
                <?php foreach ($reviews['data'] as $review): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <img src="<?php echo BASE_URL . 'uploads/avatars/' . ($review['avatar'] ?? 'default.png'); ?>"
                                    class="rounded-circle me-2" width="40" height="40" alt="Avatar">
                                <div>
                                    <strong><?php echo $review['user_name']; ?></strong>
                                    <div class="text-warning">
                                        <?php for ($i = 1; $i <= 10; $i++): ?>
                                            <i class="bi bi-star<?php echo $i <= $review['rating'] ? '-fill' : ''; ?>"></i>
                                        <?php endfor; ?>
                                        <span class="text-dark ms-1"><?php echo $review['rating']; ?>/10</span>
                                    </div>
                                </div>
                                <small
                                    class="text-muted ms-auto"><?php echo date('d/m/Y H:i', strtotime($review['created_at'])); ?></small>
                            </div>
                            <p><?php echo $review['comment']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">Chưa có đánh giá nào.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Related Games by Categories -->
    <?php if (!empty($relatedGames)): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">
                    <i class="bi bi-controller"></i> Game Cùng Chủ Đề
                    <?php if (!empty($categories)): ?>
                        <small class="text-muted">
                            (<?php echo implode(', ', array_column($categories, 'name')); ?>)
                        </small>
                    <?php endif; ?>
                </h3>
                <div class="row">
                    <?php foreach ($relatedGames as $relatedGame): ?>
                        <div class="col-md-3 mb-4">
                            <div class="card h-100 game-card">
                                <a href="<?php echo BASE_URL; ?>game/detail?slug=<?php echo $relatedGame['slug']; ?>">
                                    <?php
                                    require_once __DIR__ . '/../../models/GameImage.php';
                                    $imageModel = new GameImage();
                                    $relatedImages = $imageModel->getByGameId($relatedGame['id']);
                                    $relatedImageUrl = !empty($relatedImages) ? BASE_URL . $relatedImages[0]['image_url'] : BASE_URL . 'assets/images/no-image.jpg';
                                    ?>
                                    <img src="<?php echo $relatedImageUrl; ?>" class="card-img-top"
                                        alt="<?php echo $relatedGame['title']; ?>">
                                </a>
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="<?php echo BASE_URL; ?>game/detail?slug=<?php echo $relatedGame['slug']; ?>"
                                            class="text-decoration-none text-dark">
                                            <?php echo $relatedGame['title']; ?>
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted small">
                                        <?php echo substr($relatedGame['short_description'] ?? '', 0, 80); ?>...</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <?php if ($relatedGame['sale_price']): ?>
                                                <span
                                                    class="text-decoration-line-through text-muted small"><?php echo number_format($relatedGame['price']); ?>đ</span>
                                                <span
                                                    class="text-danger fw-bold"><?php echo number_format($relatedGame['sale_price']); ?>đ</span>
                                            <?php else: ?>
                                                <span class="fw-bold"><?php echo number_format($relatedGame['price']); ?>đ</span>
                                            <?php endif; ?>
                                        </div>
                                        <button class="btn btn-primary btn-sm add-to-cart"
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