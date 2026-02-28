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
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Thống Kê Doanh Thu (30 ngày gần nhất)</h5>
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Chủ Đề Bán Chạy</h5>
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueLabels = <?php echo !empty($revenueStats) ? json_encode(array_map(function($item) { return date('d/m', strtotime($item['date'])); }, $revenueStats)) : '[]'; ?>;
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
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Category Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryLabels = <?php echo !empty($categorySales) ? json_encode(array_column($categorySales, 'name')) : '[]'; ?>;
const categoryData = <?php echo !empty($categorySales) ? json_encode(array_column($categorySales, 'total_sales')) : '[]'; ?>;

new Chart(categoryCtx, {
    type: 'pie',
    data: {
        labels: categoryLabels,
        datasets: [{
            data: categoryData,
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(255, 159, 64, 0.8)',
                'rgba(199, 199, 199, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true
    }
});
</script>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
