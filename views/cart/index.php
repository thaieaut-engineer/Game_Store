<?php
$pageTitle = 'Giỏ hàng - Game Store';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4">Giỏ Hàng</h2>

    <?php if (empty($items)): ?>
        <div class="alert alert-info">
            <h4>Giỏ hàng trống</h4>
            <p>Hãy thêm game vào giỏ hàng để tiếp tục mua sắm.</p>
            <a href="<?php echo BASE_URL; ?>game" class="btn btn-primary">Xem danh sách game</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Game</th>
                            <th>Giá</th>
                            <th>Tổng</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo BASE_URL . ($item['image'] ?? 'assets/images/no-image.jpg'); ?>"
                                            class="me-3" width="80" alt="Game">
                                        <div>
                                            <a href="<?php echo BASE_URL; ?>game/detail?slug=<?php echo $item['slug']; ?>">
                                                <?php echo $item['title']; ?>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $price = $item['sale_price'] ?? $item['price'];
                                    echo number_format($price); ?>đ
                                </td>
                                <td><?php echo number_format($price * $item['quantity']); ?>đ</td>
                                <td>
                                    <button class="btn btn-danger btn-sm remove-item" data-item-id="<?php echo $item['id']; ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tổng Cộng</h5>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tạm tính:</span>
                            <span id="cart-total"><?php echo number_format($total); ?>đ</span>
                        </div>
                        <a href="<?php echo BASE_URL; ?>order/checkout" class="btn btn-primary w-100">Thanh Toán</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>