<?php
$pageTitle = 'Sửa Tài khoản - Admin';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Sửa Tài Khoản</h1>
    <a href="<?php echo BASE_URL; ?>admin/user" class="btn btn-secondary">Quay lại</a>
</div>

<form action="<?php echo BASE_URL; ?>admin/user/edit?id=<?php echo $user['id']; ?>" method="POST">
    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <label for="name" class="form-label">Họ và tên *</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $user['name']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" value="<?php echo $user['email']; ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Vai trò</label>
                <select class="form-select" id="role" name="role">
                    <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="1" <?php echo $user['status'] ? 'selected' : ''; ?>>Hoạt động</option>
                    <option value="0" <?php echo !$user['status'] ? 'selected' : ''; ?>>Khóa</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Cập Nhật</button>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
