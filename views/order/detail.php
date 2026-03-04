<?php
$pageTitle = 'Chi tiết đơn hàng - Game Store';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4">Chi Tiết Đơn Hàng #<?php echo $order['id']; ?></h2>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Thông Tin Đơn Hàng</h5>
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
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>game/detail?slug=<?php echo $item['slug']; ?>">
                                            <?php echo $item['title']; ?>
                                        </a>
                                    </td>
                                    <td><?php echo number_format($item['price']); ?>đ</td>
                                    <td><?php echo number_format($item['price'] * $item['quantity']); ?>đ</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Thông Tin Thanh Toán</h5>
                    <hr>
                    <p><strong>Phương thức:</strong> <?php echo $order['payment_method']; ?></p>
                    <p><strong>Trạng thái:</strong>
                        <?php
                        $statusLabels = [
                            'pending' => ['label' => 'Chờ duyệt', 'class' => 'warning'],
                            'approved' => ['label' => 'Đã duyệt', 'class' => 'info'],
                            'completed' => ['label' => 'Hoàn thành', 'class' => 'success'],
                            'cancelled' => ['label' => 'Đã hủy', 'class' => 'danger']
                        ];
                        $status = $statusLabels[$order['status']] ?? ['label' => $order['status'], 'class' => 'secondary'];
                        ?>
                        <span class="badge bg-<?php echo $status['class']; ?>"><?php echo $status['label']; ?></span>
                    </p>
                    <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Tổng cộng:</strong>
                        <strong><?php echo number_format($order['total_amount']); ?>đ</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>