<?php
$pageTitle = 'Chi tiết Đơn hàng - Admin';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Chi Tiết Đơn Hàng #<?php echo $order['id']; ?></h1>
    <a href="<?php echo BASE_URL; ?>admin/order" class="btn btn-secondary">Quay lại</a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
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
                                <td><?php echo $item['title']; ?></td>
                                <td><?php echo number_format($item['price']); ?>đ</td>
                                <td><?php echo number_format($item['price']); ?>đ</td>
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
                <h5 class="card-title">Thông Tin Khách Hàng</h5>
                <p><strong>Tên:</strong> <?php echo $order['user_name']; ?></p>
                <p><strong>Email:</strong> <?php echo $order['email']; ?></p>
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
                <p><strong>Tổng cộng:</strong> <?php echo number_format($order['total_amount']); ?>đ</p>

                <?php if ($order['status'] === 'pending'): ?>
                    <form action="<?php echo BASE_URL; ?>admin/order/update-status" method="POST" class="mt-3">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <select name="status" class="form-select mb-2">
                            <option value="approved">Đã duyệt</option>
                            <option value="cancelled">Hủy đơn</option>
                        </select>
                        <button type="submit" class="btn btn-primary w-100">Cập Nhật</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>