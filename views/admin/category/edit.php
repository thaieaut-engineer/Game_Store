<?php
$pageTitle = 'Sửa Chủ đề - Admin';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Sửa Chủ Đề</h1>
    <a href="<?php echo BASE_URL; ?>admin/category" class="btn btn-secondary">Quay lại</a>
</div>

<form action="<?php echo BASE_URL; ?>admin/category/edit?id=<?php echo $category['id']; ?>" method="POST" enctype="multipart/form-data">
    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <label for="name" class="form-label">Tên chủ đề *</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $category['name']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Ảnh hiện tại</label>
                <?php if ($category['image']): ?>
                    <div class="mb-2">
                        <img src="<?php echo BASE_URL . $category['image']; ?>" width="200" alt="Category">
                    </div>
                <?php endif; ?>
                <label for="image" class="form-label">Thay đổi ảnh</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Cập Nhật</button>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
