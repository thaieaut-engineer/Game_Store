<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Game.php';
require_once __DIR__ . '/../models/Order.php';

class AdminExportController
{
    private $userModel;
    private $gameModel;
    private $orderModel;

    public function __construct()
    {
        if (!isAdmin()) {
            redirect('');
        }
        $this->userModel  = new User();
        $this->gameModel  = new Game();
        $this->orderModel = new Order();
    }

    /** Xuất danh sách tài khoản */
    public function exportUsers()
    {
        $users = $this->userModel->getAllForExport();

        $filename = 'danh_sach_tai_khoan_' . date('Ymd_His') . '.xls';
        $this->sendExcelHeaders($filename);

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"
               xmlns:x="urn:schemas-microsoft-com:office:excel"
               xmlns="http://www.w3.org/TR/REC-html40">
        <head><meta charset="UTF-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets>
        <x:ExcelWorksheet><x:Name>Tai Khoan</x:Name><x:WorksheetOptions><x:DisplayGridlines/>
        </x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->
        </head><body>';

        echo '<table border="1">';
        echo '<tr style="background:#4472C4;color:white;font-weight:bold;">
            <th>ID</th>
            <th>Tên</th>
            <th>Email</th>
            <th>Vai trò</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
        </tr>';

        foreach ($users as $u) {
            $role   = $u['role'] === 'admin' ? 'Admin' : 'User';
            $status = $u['status'] ? 'Hoạt động' : 'Khóa';
            $date   = date('d/m/Y H:i', strtotime($u['created_at']));
            echo '<tr>
                <td>' . $u['id'] . '</td>
                <td>' . htmlspecialchars($u['name']) . '</td>
                <td>' . htmlspecialchars($u['email']) . '</td>
                <td>' . $role . '</td>
                <td>' . $status . '</td>
                <td>' . $date . '</td>
            </tr>';
        }

        echo '</table></body></html>';
        exit;
    }

    /** Xuất danh sách game */
    public function exportGames()
    {
        $games = $this->gameModel->getAllForExport();

        $filename = 'danh_sach_game_' . date('Ymd_His') . '.xls';
        $this->sendExcelHeaders($filename);

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"
               xmlns:x="urn:schemas-microsoft-com:office:excel"
               xmlns="http://www.w3.org/TR/REC-html40">
        <head><meta charset="UTF-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets>
        <x:ExcelWorksheet><x:Name>Game</x:Name><x:WorksheetOptions><x:DisplayGridlines/>
        </x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->
        </head><body>';

        echo '<table border="1">';
        echo '<tr style="background:#4472C4;color:white;font-weight:bold;">
            <th>ID</th>
            <th>Tên Game</th>
            <th>Giá Gốc (đ)</th>
            <th>Giá Sale (đ)</th>
            <th>% Giảm Giá</th>
            <th>Lượt Mua</th>
            <th>Sắp Ra Mắt</th>
            <th>Ngày Tạo</th>
        </tr>';

        foreach ($games as $g) {
            $price    = number_format($g['price'], 0, ',', '.');
            $sale     = $g['sale_price'] ? number_format($g['sale_price'], 0, ',', '.') : '-';
            $discount = $g['discount_percent'] > 0 ? $g['discount_percent'] . '%' : '-';
            $upcoming = $g['is_upcoming'] ? 'Có' : 'Không';
            $date     = date('d/m/Y', strtotime($g['created_at']));
            echo '<tr>
                <td>' . $g['id'] . '</td>
                <td>' . htmlspecialchars($g['title']) . '</td>
                <td>' . $price . '</td>
                <td>' . $sale . '</td>
                <td>' . $discount . '</td>
                <td>' . ($g['total_sales'] ?? 0) . '</td>
                <td>' . $upcoming . '</td>
                <td>' . $date . '</td>
            </tr>';
        }

        echo '</table></body></html>';
        exit;
    }

    /** Xuất doanh thu */
    public function exportRevenue()
    {
        $orders = $this->orderModel->getAllForExport();

        $filename = 'doanh_thu_' . date('Ymd_His') . '.xls';
        $this->sendExcelHeaders($filename);

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"
               xmlns:x="urn:schemas-microsoft-com:office:excel"
               xmlns="http://www.w3.org/TR/REC-html40">
        <head><meta charset="UTF-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets>
        <x:ExcelWorksheet><x:Name>Doanh Thu</x:Name><x:WorksheetOptions><x:DisplayGridlines/>
        </x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->
        </head><body>';

        echo '<table border="1">';
        echo '<tr style="background:#4472C4;color:white;font-weight:bold;">
            <th>ID Đơn</th>
            <th>Người Mua</th>
            <th>Email</th>
            <th>Tổng Tiền (đ)</th>
            <th>Thanh Toán</th>
            <th>Trạng Thái</th>
            <th>Ngày Đặt</th>
        </tr>';

        $statusMap = [
            'pending'   => 'Chờ xử lý',
            'approved'  => 'Đã duyệt',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
        ];
        $totalRevenue = 0;

        foreach ($orders as $o) {
            $amount  = number_format($o['total_amount'], 0, ',', '.');
            $status  = $statusMap[$o['status']] ?? $o['status'];
            $date    = date('d/m/Y H:i', strtotime($o['created_at']));
            if (in_array($o['status'], ['approved', 'completed'])) {
                $totalRevenue += $o['total_amount'];
            }
            echo '<tr>
                <td>' . $o['id'] . '</td>
                <td>' . htmlspecialchars($o['user_name']) . '</td>
                <td>' . htmlspecialchars($o['email']) . '</td>
                <td>' . $amount . '</td>
                <td>' . htmlspecialchars($o['payment_method']) . '</td>
                <td>' . $status . '</td>
                <td>' . $date . '</td>
            </tr>';
        }

        // Tổng doanh thu
        echo '<tr style="font-weight:bold;background:#E2EFDA;">
            <td colspan="3">TỔNG DOANH THU (đơn đã duyệt/hoàn thành)</td>
            <td>' . number_format($totalRevenue, 0, ',', '.') . '</td>
            <td colspan="3"></td>
        </tr>';

        echo '</table></body></html>';
        exit;
    }

    private function sendExcelHeaders($filename)
    {
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM để Excel đọc tiếng Việt đúng
    }
}
?>
