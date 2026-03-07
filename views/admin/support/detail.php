<?php
$pageTitle = 'Chi tiết Hỗ trợ - Admin';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Chi tiết Yêu cầu #
        <?php echo $request['id']; ?>
    </h1>
    <a href="<?php echo BASE_URL; ?>admin/support" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Quay lại danh sách
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Nội dung yêu cầu</h5>
                <span class="text-muted small">
                    <?php echo date('d/m/Y H:i', strtotime($request['created_at'])); ?>
                </span>
            </div>
            <div class="card-body">
                <h5 class="card-title fw-bold text-primary mb-3">
                    <?php echo htmlspecialchars($request['subject']); ?>
                </h5>
                <div class="p-3 bg-light rounded" style="white-space: pre-wrap;">
                    <?php echo htmlspecialchars($request['message']); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Thông tin người gửi</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <strong>Tên:</strong>
                        <?php echo htmlspecialchars($request['name'] ?: $request['user_name']); ?>
                    </li>
                    <li class="mb-2">
                        <strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($request['email']); ?>">
                            <?php echo htmlspecialchars($request['email']); ?>
                        </a>
                    </li>
                    <li class="mb-2">
                        <strong>Loại:</strong>
                        <?php echo $request['user_id'] ? 'Thành viên (ID: ' . $request['user_id'] . ')' : 'Khách'; ?>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Xử lý yêu cầu</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>admin/support/update-status" method="POST">
                    <input type="hidden" name="id" value="<?php echo $request['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="open" <?php echo $request['status'] === 'open' ? 'selected' : ''; ?>>Chưa xử
                                lý (Mở)</option>
                            <option value="in_progress" <?php echo $request['status'] === 'in_progress' ? 'selected' : ''; ?>>Đang xử lý</option>
                            <option value="closed" <?php echo $request['status'] === 'closed' ? 'selected' : ''; ?>>Đã
                                giải quyết (Đóng)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Cập nhật trạng thái</button>
                </form>
                <hr>
                <a href="<?php echo BASE_URL; ?>admin/support/delete?id=<?php echo $request['id']; ?>"
                    class="btn btn-outline-danger btn-sm w-100"
                    onclick="return confirm('Bạn có chắc chắn muốn xóa yêu cầu này?')">
                    <i class="bi bi-trash"></i> Xóa yêu cầu
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>