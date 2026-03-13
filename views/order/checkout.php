<?php
$pageTitle = 'Thanh toán - Game Store';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4">Thanh Toán</h2>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Đơn Hàng</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Game</th>
                                <th>Giá</th>
                                <th>Tổng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?php echo $item['title']; ?></td>
                                    <td>
                                        <?php
                                        $price = $item['sale_price'] ?? $item['price'];
                                        echo number_format($price); ?>đ
                                    </td>
                                    <td><?php echo number_format($price); ?>đ</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <form action="<?php echo BASE_URL; ?>order/checkout" method="POST">
                <?php if (isset($buyNowId) && $buyNowId > 0): ?>
                    <input type="hidden" name="buy_now" value="<?php echo $buyNowId; ?>">
                <?php endif; ?>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Phương Thức Thanh Toán</h5>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="bank"
                                    value="Bank Transfer" required>
                                <label class="form-check-label" for="bank">Chuyển khoản ngân hàng</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="momo"
                                    value="MoMo" required>
                                <label class="form-check-label" for="momo">Ví MoMo</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Xác Nhận Đặt Hàng</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tổng Cộng</h5>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Tạm tính:</span>
                        <span><?php echo number_format($total); ?>đ</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Tổng cộng:</strong>
                        <strong><?php echo number_format($total); ?>đ</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>