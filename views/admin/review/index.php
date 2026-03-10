<?php
$pageTitle = 'Quản lý Bình luận - Admin';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Quản Lý Bình Luận</h1>
</div>

<div class="mb-3">
    <label>Lọc theo điểm đánh giá:</label>
    <a href="?" class="btn btn-sm btn-secondary">Tất cả</a>
    <?php for ($i = 1; $i <= 10; $i++): ?>
        <a href="?rating=<?php echo $i; ?>" class="btn btn-sm btn-outline-primary"><?php echo $i; ?></a>
    <?php endfor; ?>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Người dùng</th>
                <th>Game</th>
                <th>Điểm</th>
                <th>Bình luận</th>
                <th>Ngày</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($result['data'])): ?>
                <tr>
                    <td colspan="7" class="text-center">Không có dữ liệu</td>
                </tr>
            <?php else: ?>
                <?php foreach ($result['data'] as $review): ?>
                    <tr>
                        <td><?php echo $review['id']; ?></td>
                        <td><?php echo $review['user_name']; ?></td>
                        <td><?php echo $review['game_title']; ?></td>
                        <td>
                            <span class="badge bg-warning"><?php echo $review['rating']; ?>/10</span>
                        </td>
                        <td><?php echo substr($review['comment'], 0, 100); ?>...</td>
                        <td><?php echo date('d/m/Y H:i', strtotime($review['created_at'])); ?></td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>admin/review/delete?id=<?php echo $review['id']; ?>"
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
                    <a class="page-link"
                        href="?page=<?php echo $result['page'] - 1; ?><?php echo !empty($_GET['rating']) ? '&rating=' . $_GET['rating'] : ''; ?>">Trước</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $result['total_pages']; $i++): ?>
                <li class="page-item <?php echo $i == $result['page'] ? 'active' : ''; ?>">
                    <a class="page-link"
                        href="?page=<?php echo $i; ?><?php echo !empty($_GET['rating']) ? '&rating=' . $_GET['rating'] : ''; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($result['page'] < $result['total_pages']): ?>
                <li class="page-item">
                    <a class="page-link"
                        href="?page=<?php echo $result['page'] + 1; ?><?php echo !empty($_GET['rating']) ? '&rating=' . $_GET['rating'] : ''; ?>">Sau</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>



<?php require_once __DIR__ . '/../layout/footer.php'; ?>