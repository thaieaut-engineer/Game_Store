<?php
$pageTitle = 'Quản lý Game - Admin';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Quản Lý Game</h1>
    <a href="<?php echo BASE_URL; ?>admin/game/create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Thêm Game
    </a>
</div>

<div class="mb-3">
    <form method="GET" class="d-flex">
        <input type="text" name="search" class="form-control me-2" placeholder="Tìm kiếm game..." value="<?php echo $_GET['search'] ?? ''; ?>">
        <button type="submit" class="btn btn-outline-primary">Tìm</button>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tiêu đề</th>
                <th>Giá</th>
                <th>Giá sale</th>
                <th>Giảm giá</th>
                <th>Lượt bán</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($result['data'])): ?>
                <tr>
                    <td colspan="7" class="text-center">Không có dữ liệu</td>
                </tr>
            <?php else: ?>
                <?php foreach ($result['data'] as $game): ?>
                    <tr>
                        <td><?php echo $game['id']; ?></td>
                        <td><?php echo $game['title']; ?></td>
                        <td><?php echo number_format($game['price']); ?>đ</td>
                        <td><?php echo $game['sale_price'] ? number_format($game['sale_price']) . 'đ' : '-'; ?></td>
                        <td>
                            <?php if ($game['discount_percent'] > 0): ?>
                                <span class="badge bg-danger">-<?php echo $game['discount_percent']; ?>%</span>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?php echo $game['total_sales']; ?></td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>admin/game/edit?id=<?php echo $game['id']; ?>" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="<?php echo BASE_URL; ?>admin/game/delete?id=<?php echo $game['id']; ?>" 
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
                    <a class="page-link" href="?page=<?php echo $result['page'] - 1; ?><?php echo !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">Trước</a>
                </li>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $result['total_pages']; $i++): ?>
                <li class="page-item <?php echo $i == $result['page'] ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            
            <?php if ($result['page'] < $result['total_pages']): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $result['page'] + 1; ?><?php echo !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">Sau</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
