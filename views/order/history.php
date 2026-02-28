<?php
$pageTitle = 'đơn hàng - Game Store';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4">Đơn Hàng</h2>
    
    <?php if (empty($result['data'])): ?>
        <div class="alert alert-info">Bạn chưa có đơn hàng nào.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result['data'] as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
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
                            <td>
                                <a href="<?php echo BASE_URL; ?>order/detail?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">Xem chi tiết</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
