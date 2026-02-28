<?php
$pageTitle = 'Quản lý Đơn hàng - Admin';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Quản Lý Đơn Hàng</h1>
</div>

<div class="mb-3">
    <a href="?status=pending" class="btn btn-warning">Chờ duyệt</a>
    <a href="?status=approved" class="btn btn-info">Đã duyệt</a>
    <a href="?" class="btn btn-secondary">Tất cả</a>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Người dùng</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngày đặt</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($result['data'])): ?>
                <tr>
                    <td colspan="6" class="text-center">Không có dữ liệu</td>
                </tr>
            <?php else: ?>
                <?php foreach ($result['data'] as $order): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo $order['user_name']; ?></td>
                        <td><?php echo number_format($order['total_amount']); ?>đ</td>
                        <td>
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
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>admin/order/detail?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php if ($result['total_pages'] > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($result['page'] > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $result['page'] - 1; ?><?php echo !empty($_GET['status']) ? '&status=' . $_GET['status'] : ''; ?>">Trước</a>
                </li>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $result['total_pages']; $i++): ?>
                <li class="page-item <?php echo $i == $result['page'] ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($_GET['status']) ? '&status=' . $_GET['status'] : ''; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            
            <?php if ($result['page'] < $result['total_pages']): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $result['page'] + 1; ?><?php echo !empty($_GET['status']) ? '&status=' . $_GET['status'] : ''; ?>">Sau</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
