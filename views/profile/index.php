<?php
$pageTitle = 'Tài khoản - Game Store';
require_once __DIR__ . '/../layout/header.php';
$user = getCurrentUser();
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Thông Tin Tài Khoản</h2>
        <div>
            <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'primary'; ?>">
                <?php echo $user['role'] === 'admin' ? 'Admin' : 'Người dùng'; ?>
            </span>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Cập Nhật Thông Tin</h5>
                    <form action="<?php echo BASE_URL; ?>profile/update" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Ảnh đại diện</label>
                            <div class="mb-2">
                                <?php 
                                $avatarFile = $user['avatar'] ?? 'default-avatar.png';
                                $avatarPath = BASE_URL . 'uploads/avatars/' . $avatarFile;
                                if (!file_exists(__DIR__ . '/../../uploads/avatars/' . $avatarFile)) {
                                    // Try default image
                                    if (file_exists(__DIR__ . '/../../assets/images/default-avatar.png')) {
                                        $avatarPath = BASE_URL . 'assets/images/default-avatar.png';
                                    } else {
                                        // Use a placeholder or default icon
                                        $avatarPath = 'https://via.placeholder.com/100?text=Avatar';
                                    }
                                }
                                ?>
                                <img src="<?php echo $avatarPath; ?>" 
                                     class="rounded-circle border" width="100" height="100" alt="Avatar" 
                                     style="object-fit: cover;" id="avatar-preview">
                            </div>
                            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*" onchange="previewAvatar(this)">
                            <small class="text-muted">Chọn ảnh JPG, PNG hoặc GIF (tối đa 2MB)</small>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?php echo $user['email']; ?>" disabled>
                        </div>
                        <button type="submit" class="btn btn-primary">Cập Nhật</button>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Đổi Mật Khẩu</h5>
                    <form action="<?php echo BASE_URL; ?>profile/change-password" method="POST">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                        </div>
                        <button type="submit" class="btn btn-primary">Đổi Mật Khẩu</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Thông Tin Tài Khoản</h5>
                    <hr>
                    <p><strong>Thành viên từ:</strong><br>
                    <small class="text-muted">
                        <?php 
                        if (isset($user['created_at']) && !empty($user['created_at']) && $user['created_at'] != '0000-00-00 00:00:00') {
                            echo date('d/m/Y', strtotime($user['created_at']));
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </small></p>
                    <p><strong>Trạng thái:</strong><br>
                    <span class="badge bg-<?php echo $user['status'] ? 'success' : 'danger'; ?>">
                        <?php echo $user['status'] ? 'Hoạt động' : 'Đã khóa'; ?>
                    </span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
