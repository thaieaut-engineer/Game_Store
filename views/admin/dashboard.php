<?php
$pageTitle = 'Tổng quan - Admin';
require_once __DIR__ . '/layout/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tổng Quan</h1>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Tổng số Game</h5>
                <h2><?php echo $gameCount; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Tổng số Chủ đề</h5>
                <h2><?php echo $categoryCount; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Tổng số Người dùng</h5>
                <h2><?php echo $userCount; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Tổng số Đơn hàng</h5>
                <h2><?php echo $orderCount; ?></h2>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Thống Kê Doanh Thu</h5>
                <ul class="nav nav-pills card-header-pills" id="revenueTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="days-tab" data-bs-toggle="tab" data-bs-target="#days-chart"
                            type="button" role="tab">30 ngày gần nhất</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="months-tab" data-bs-toggle="tab" data-bs-target="#months-chart"
                            type="button" role="tab">Theo tháng (Năm hiện tại)</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="years-tab" data-bs-toggle="tab" data-bs-target="#years-chart"
                            type="button" role="tab">Theo năm</button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="revenueTabContent">
                    <div class="tab-pane fade show active" id="days-chart" role="tabpanel">
                        <div style="position: relative; height:300px; width:100%">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="months-chart" role="tabpanel">
                        <div style="position: relative; height:300px; width:100%">
                            <canvas id="revenueMonthChart"></canvas>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="years-chart" role="tabpanel">
                        <div style="position: relative; height:300px; width:100%">
                            <canvas id="revenueYearChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Chủ Đề Bán Chạy</h5>
                <div style="height: 300px; display: flex; justify-content: center; align-items: center;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <!-- AI Analysis -->
        <div class="card border-info h-100">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <span><i class="bi bi-robot"></i> AI Phân Tích Doanh Thu</span>
                <button type="button" class="btn btn-light btn-sm fw-bold text-info" id="btn-analyze-revenue">
                    🪄 Phân tích
                </button>
            </div>
            <div class="card-body" id="ai-revenue-result" style="max-height: 300px; overflow-y: auto;">
                <p class="text-muted mb-0">Nhấn nút "Phân tích" để AI đánh giá tình hình doanh thu dựa trên dữ liệu hiện
                    tại...</p>
            </div>
        </div>
    </div>
</div>

<!-- Code commented out to skip AI block replacement -->

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Revenue Chart 30 days
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueLabels = <?php echo !empty($revenueStats) ? json_encode(array_map(function ($item) {
            return date('d/m', strtotime($item['date']));
        }, $revenueStats)) : '[]'; ?>;
        const revenueData = <?php echo !empty($revenueStats) ? json_encode(array_column($revenueStats, 'revenue')) : '[]'; ?>;

        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: revenueData,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Month Revenue Chart
        const monthCtx = document.getElementById('revenueMonthChart').getContext('2d');
        const monthLabels = <?php echo !empty($revenueByMonth) ? json_encode(array_map(function ($item) {
            return 'Tháng ' . $item['month'];
        }, $revenueByMonth)) : '[]'; ?>;
        const monthData = <?php echo !empty($revenueByMonth) ? json_encode(array_column($revenueByMonth, 'revenue')) : '[]'; ?>;

        new Chart(monthCtx, {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: monthData,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Year Revenue Chart
        const yearCtx = document.getElementById('revenueYearChart').getContext('2d');
        const yearLabels = <?php echo !empty($revenueByYear) ? json_encode(array_map(function ($item) {
            return 'Năm ' . $item['year'];
        }, $revenueByYear)) : '[]'; ?>;
        const yearData = <?php echo !empty($revenueByYear) ? json_encode(array_column($revenueByYear, 'revenue')) : '[]'; ?>;

        new Chart(yearCtx, {
            type: 'bar',
            data: {
                labels: yearLabels,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: yearData,
                    backgroundColor: 'rgba(153, 102, 255, 0.6)',
                    borderColor: 'rgb(153, 102, 255)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Fix for hidden canvas size issue in Bootstrap tabs
        $('a[data-bs-toggle="tab"], button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            const target = $(e.target).attr("data-bs-target");
            if (target === '#months-chart') {
                document.getElementById('revenueMonthChart').style.display = 'block';
                document.getElementById('revenueMonthChart').style.width = '100%';
            } else if (target === '#years-chart') {
                document.getElementById('revenueYearChart').style.display = 'block';
                document.getElementById('revenueYearChart').style.width = '100%';
            }
        });

        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryLabels = <?php echo !empty($categorySales) ? json_encode(array_column($categorySales, 'name')) : '[]'; ?>;
        const categoryData = <?php echo !empty($categorySales) ? json_encode(array_column($categorySales, 'total_sales')) : '[]'; ?>;

        // Tạo màu ngẫu nhiên cho đủ số lượng chủ đề
        const bgColors = [];
        const borderColors = [];
        for (let i = 0; i < categoryLabels.length; i++) {
            const r = Math.floor(Math.random() * 200 + 55);
            const g = Math.floor(Math.random() * 200 + 55);
            const b = Math.floor(Math.random() * 200 + 55);
            bgColors.push(`rgba(${r}, ${g}, ${b}, 0.7)`);
            borderColors.push(`rgb(${r}, ${g}, ${b})`);
        }

        new Chart(categoryCtx, {
            type: 'pie',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryData,
                    backgroundColor: bgColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });

        // AI Analysis
        $('#btn-analyze-revenue').click(function () {
            const btn = $(this);
            const resultDiv = $('#ai-revenue-result');
            const originalText = btn.html();

            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang phân tích...');
            btn.prop('disabled', true);
            resultDiv.html('<div class="text-center py-4"><div class="spinner-border text-info" role="status"></div><p class="mt-2 mb-0">AI đang đọc hiểu dữ liệu và viết báo cáo...</p></div>');

            $.ajax({
                url: '<?php echo BASE_URL; ?>admin/dashboard/ai-report',
                type: 'POST',
                success: function (response) {
                    if (response.success && response.data) {
                        resultDiv.html('<div class="ai-report-content">' + response.data + '</div>');
                    } else {
                        resultDiv.html('<div class="alert alert-danger">Lỗi: ' + (response.message || 'Không thể tạo báo cáo') + '</div>');
                    }
                },
                error: function () {
                    resultDiv.html('<div class="alert alert-danger">Đã xảy ra lỗi khi kết nối tới server.</div>');
                },
                complete: function () {
                    btn.html(originalText);
                    btn.prop('disabled', false);
                }
            });
        });
    });
</script>

<?php require_once __DIR__ . '/layout/footer.php'; ?>