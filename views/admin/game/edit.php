<?php
$pageTitle = 'Sửa Game - Admin';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Sửa Game</h1>
    <a href="<?php echo BASE_URL; ?>admin/game" class="btn btn-secondary">Quay lại</a>
</div>

<form action="<?php echo BASE_URL; ?>admin/game/edit?id=<?php echo $game['id']; ?>" method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề *</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo $game['title']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="short_description" class="form-label">Mô tả ngắn</label>
                        <textarea class="form-control" id="short_description" name="short_description" rows="3"><?php echo $game['short_description']; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" rows="10"><?php echo $game['description']; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="system_requirements" class="form-label">Cấu hình đề xuất</label>
                        <textarea class="form-control" id="system_requirements" name="system_requirements" rows="10"><?php echo $game['system_requirements']; ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="price" class="form-label">Giá *</label>
                        <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?php echo $game['price']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="sale_price" class="form-label">Giá sale</label>
                        <input type="number" class="form-control" id="sale_price" name="sale_price" step="0.01" value="<?php echo $game['sale_price']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="stock" class="form-label">Tồn kho</label>
                        <input type="number" class="form-control" id="stock" name="stock" value="<?php echo $game['stock']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="release_date" class="form-label">Ngày phát hành</label>
                        <input type="date" class="form-control" id="release_date" name="release_date" value="<?php echo $game['release_date']; ?>">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_upcoming" name="is_upcoming" value="1" <?php echo $game['is_upcoming'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_upcoming">Game sắp ra mắt</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="video_url" class="form-label">Video URL</label>
                        <input type="text" class="form-control" id="video_url" name="video_url" value="<?php echo $game['video_url']; ?>" placeholder="uploads/videos/...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ảnh hiện tại</label>
                        <div class="row">
                            <?php foreach ($images as $image): ?>
                                <div class="col-6 mb-2">
                                    <img src="<?php echo BASE_URL . $image['image_url']; ?>" class="img-fluid" alt="Game Image">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="images" class="form-label">Thêm ảnh mới</label>
                        <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chủ đề</label>
                        <?php 
                        $selectedCategories = array_column($gameCategories, 'id');
                        foreach ($categories as $category): 
                        ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="<?php echo $category['id']; ?>" 
                                       id="cat_<?php echo $category['id']; ?>" <?php echo in_array($category['id'], $selectedCategories) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="cat_<?php echo $category['id']; ?>">
                                    <?php echo $category['name']; ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary">Cập Nhật</button>
</form>

<script>
$(document).ready(function() {
    $('#description').summernote({
        height: 300
    });
    $('#system_requirements').summernote({
        height: 300
    });
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
