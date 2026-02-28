<?php
$pageTitle = 'Quản lý Chủ đề - Admin';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Quản Lý Chủ Đề</h1>
    <a href="<?php echo BASE_URL; ?>admin/category/create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Thêm Chủ Đề
    </a>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Ảnh</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($result['data'])): ?>
                <tr>
                    <td colspan="4" class="text-center">Không có dữ liệu</td>
                </tr>
            <?php else: ?>
                <?php foreach ($result['data'] as $category): ?>
                    <tr>
                        <td><?php echo $category['id']; ?></td>
                        <td><?php echo $category['name']; ?></td>
                        <td>
                            <?php if ($category['image']): ?>
                                <img src="<?php echo BASE_URL . $category['image']; ?>" width="100" alt="Category">
                            <?php else: ?>
                                <span class="text-muted">Không có ảnh</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>admin/category/edit?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="<?php echo BASE_URL; ?>admin/category/delete?id=<?php echo $category['id']; ?>" 
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
