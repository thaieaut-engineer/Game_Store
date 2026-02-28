<?php
$pageTitle = 'Quản lý Tài khoản - Admin';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Quản Lý Tài Khoản</h1>
    <a href="<?php echo BASE_URL; ?>admin/user/create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Thêm Tài Khoản
    </a>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Vai trò</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($result['data'])): ?>
                <tr>
                    <td colspan="6" class="text-center">Không có dữ liệu</td>
                </tr>
            <?php else: ?>
                <?php foreach ($result['data'] as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['name']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td>
                            <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'primary'; ?>">
                                <?php echo $user['role'] === 'admin' ? 'Admin' : 'User'; ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo $user['status'] ? 'success' : 'danger'; ?>">
                                <?php echo $user['status'] ? 'Hoạt động' : 'Khóa'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>admin/user/edit?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="<?php echo BASE_URL; ?>admin/user/delete?id=<?php echo $user['id']; ?>" 
                               class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
                                <i class="bi bi-trash"></i>
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

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
