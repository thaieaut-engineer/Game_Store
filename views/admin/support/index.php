<?php
$pageTitle = 'Quản lý Hỗ trợ - Admin';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Quản lý Yêu cầu Hỗ trợ</h1>
</div>

<div class="table-responsive bg-white p-3 rounded shadow-sm">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Người gửi</th>
                <th>Email</th>
                <th>Chủ đề</th>
                <th>Trạng thái</th>
                <th>Ngày gửi</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $req): ?>
                <tr>
                    <td>#
                        <?php echo $req['id']; ?>
                    </td>
                    <td>
                        <?php if ($req['user_name']): ?>
                            <span class="fw-bold">
                                <?php echo htmlspecialchars($req['user_name']); ?>
                            </span>
                            <br><small class="text-muted">ID:
                                <?php echo $req['user_id']; ?>
                            </small>
                        <?php else: ?>
                            <span class="fw-bold">
                                <?php echo htmlspecialchars($req['name']); ?>
                            </span>
                            <br><small class="text-muted">Khách</small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($req['email']); ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($req['subject']); ?>
                    </td>
                    <td>
                        <?php if ($req['status'] === 'open'): ?>
                            <span class="badge bg-danger">Chưa xử lý</span>
                        <?php elseif ($req['status'] === 'in_progress'): ?>
                            <span class="badge bg-warning text-dark">Đang xử lý</span>
                        <?php else: ?>
                            <span class="badge bg-success">Đã đóng</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo date('d/m/Y H:i', strtotime($req['created_at'])); ?>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="<?php echo BASE_URL; ?>admin/support/detail?id=<?php echo $req['id']; ?>"
                                class="btn btn-outline-primary" title="Xem chi tiết">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="<?php echo BASE_URL; ?>admin/support/delete?id=<?php echo $req['id']; ?>"
                                class="btn btn-outline-danger"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa yêu cầu này?')" title="Xóa">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo ($i === $currentPage) ? 'active' : ''; ?>">
                        <a class="page-link" href="<?php echo BASE_URL; ?>admin/support?page=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>