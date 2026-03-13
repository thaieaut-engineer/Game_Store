# Game Store Website

Website bán game được xây dựng bằng PHP, HTML, CSS, JavaScript và Bootstrap với mô hình MVC.

## Tính năng

### Người dùng
- Đăng ký/Đăng nhập (token-based, tự động hết hạn sau 30 phút)
- Quên mật khẩu (gửi email)
- Đổi thông tin tài khoản (ảnh và tên) - cập nhật ngay lập tức
- Đổi mật khẩu
- Tìm kiếm game
- Bộ lọc game (theo chủ đề, loại: đề xuất/giảm giá/sắp ra mắt)
- Thêm vào giỏ hàng
- Xem giỏ hàng
- Thanh toán (Mua ngay hoặc từ giỏ hàng)
- Xem tình trạng đơn hàng (chỉ hiển thị đơn chờ duyệt và bị hủy)
- Xem thư viện game đã mua (Library)
- Đánh giá và bình luận game (1-5 điểm)
- Game sắp ra mắt: không thể thêm giỏ hàng hoặc mua

### Admin
- Trang tổng quan với thống kê:
  - Số game, chủ đề, người dùng, đơn hàng
  - Biểu đồ doanh thu (line chart - 30 ngày/tháng/năm)
  - Biểu đồ chủ đề bán chạy (pie chart)
  - AI phân tích doanh thu
- Quản lý game (CRUD) với Summernote:
  - Upload nhiều ảnh cho game
  - Video trailer
  - Mô tả và yêu cầu hệ thống
  - Đánh dấu game sắp ra mắt
- Quản lý chủ đề (CRUD)
- Quản lý tài khoản (CRUD)
- Quản lý đơn hàng:
  - Duyệt đơn hàng (tự động thêm vào Library khi duyệt)
  - Hủy đơn hàng
  - Xem chi tiết đơn hàng
- Quản lý bình luận (lọc theo điểm 1-5, chỉ xóa)
- Quản lý hỗ trợ
- Xuất Excel:
  - Danh sách người dùng
  - Danh sách game
  - Doanh thu (đơn hàng)

## Cài đặt

1. Import database từ file SQL (tạo database `game_store` trước)
2. Cấu hình database trong `config/database.php`
3. Cấu hình BASE_URL trong `config/config.php`
4. Tạo các thư mục upload:
   - `uploads/avatars/`
   - `uploads/games/`
   - `uploads/categories/`
   - `uploads/videos/`
5. Đặt quyền ghi cho các thư mục upload

## Cấu trúc thư mục

```
Game_Store/
├── assets/                          # Tài nguyên tĩnh
│   ├── css/
│   │   ├── auth.css                 # CSS cho trang đăng nhập/đăng ký
│   │   ├── chatbot.css              # CSS cho chatbot
│   │   ├── home.css                 # CSS cho trang chủ
│   │   └── style.css                # CSS chung
│   ├── images/                      # Hình ảnh tĩnh
│   ├── js/
│   │   ├── chatbot.js               # JavaScript cho chatbot
│   │   ├── home.js                  # JavaScript cho trang chủ
│   │   └── main.js                  # JavaScript chung
│   └── videos/                      # Video banner
│
├── config/                          # Cấu hình
│   ├── config.php                   # Cấu hình chung (BASE_URL, helper functions)
│   ├── database.php                  # Cấu hình kết nối database
│   └── Mail.php                     # Cấu hình gửi email (PHPMailer)
│
├── controllers/                     # Controllers (MVC)
│   ├── AboutController.php          # Trang About Us
│   ├── AdminCategoryController.php  # Quản lý chủ đề (Admin)
│   ├── AdminController.php          # Dashboard Admin
│   ├── AdminExportController.php    # Xuất Excel (Admin)
│   ├── AdminGameController.php      # Quản lý game (Admin)
│   ├── AdminOrderController.php     # Quản lý đơn hàng (Admin)
│   ├── AdminReviewController.php    # Quản lý đánh giá (Admin)
│   ├── AdminSupportController.php   # Quản lý hỗ trợ (Admin)
│   ├── AdminUserController.php      # Quản lý người dùng (Admin)
│   ├── AuthController.php           # Đăng nhập/Đăng ký/Quên mật khẩu
│   ├── CartController.php           # Giỏ hàng
│   ├── CategoryController.php       # Danh sách chủ đề
│   ├── GameController.php           # Danh sách/Chi tiết game
│   ├── HomeController.php           # Trang chủ
│   ├── LibraryController.php        # Thư viện game đã mua
│   ├── OrderController.php          # Đơn hàng/Thanh toán
│   ├── ProfileController.php        # Trang cá nhân
│   ├── ReviewController.php         # Đánh giá game
│   ├── SupportController.php        # Hỗ trợ
│   └── TutorialController.php       # Hướng dẫn
│
├── middleware/                      # Middleware
│   └── AuthMiddleware.php          # Kiểm tra đăng nhập/quyền
│
├── models/                          # Models (MVC)
│   ├── AiLog.php                    # Log AI
│   ├── AiService.php                # Dịch vụ AI
│   ├── BaseModel.php                # Model cơ sở (pagination, DB connection)
│   ├── Cart.php                     # Giỏ hàng
│   ├── Category.php                 # Chủ đề game
│   ├── Game.php                     # Game
│   ├── GameCategoryMap.php          # Liên kết game-chủ đề
│   ├── GameImage.php                # Hình ảnh game
│   ├── Library.php                  # Thư viện game đã mua
│   ├── Order.php                    # Đơn hàng
│   ├── PasswordReset.php            # Đặt lại mật khẩu
│   ├── Review.php                   # Đánh giá
│   ├── Support.php                  # Hỗ trợ
│   ├── User.php                     # Người dùng
│   └── UserToken.php                # Token đăng nhập
│
├── views/                           # Views (MVC)
│   ├── about/                       # Trang About Us
│   ├── admin/                       # Views Admin
│   │   ├── category/                # Quản lý chủ đề
│   │   ├── game/                    # Quản lý game
│   │   ├── layout/                  # Layout Admin
│   │   ├── order/                   # Quản lý đơn hàng
│   │   ├── review/                  # Quản lý đánh giá
│   │   ├── support/                 # Quản lý hỗ trợ
│   │   ├── user/                    # Quản lý người dùng
│   │   └── dashboard.php            # Dashboard Admin
│   ├── auth/                        # Views đăng nhập/đăng ký
│   │   ├── forgot-password.php
│   │   ├── login.php
│   │   ├── register.php
│   │   └── reset-password.php
│   ├── cart/                        # Trang giỏ hàng
│   ├── category/                    # Danh sách chủ đề
│   ├── game/                        # Danh sách/Chi tiết game
│   ├── home/                        # Trang chủ
│   ├── layout/                      # Layout chung
│   │   ├── chatbot.php             # Chatbot widget
│   │   ├── footer.php               # Footer
│   │   └── header.php               # Header/Navigation
│   ├── library/                     # Thư viện game
│   ├── order/                       # Thanh toán/Lịch sử đơn hàng
│   ├── profile/                     # Trang cá nhân
│   ├── support/                     # Trang hỗ trợ
│   └── tutorial/                    # Trang hướng dẫn
│
├── uploads/                         # Thư mục upload
│   ├── avatars/                     # Avatar người dùng
│   ├── categories/                  # Hình ảnh chủ đề
│   ├── games/                       # Hình ảnh game
│   └── videos/                      # Video game
│
├── vendor/                          # Thư viện Composer
│   ├── autoload.php
│   ├── composer/
│   └── phpmailer/
│
├── .htaccess                        # URL Rewriting (Apache)
├── index.php                        # Router chính
├── database.sql                     # Script tạo database
├── composer.json                    # Dependencies Composer
├── composer.lock                    # Lock file Composer
├── CHANGELOG.md                     # Lịch sử thay đổi
└── README.md                        # Tài liệu dự án
```

## Bảo mật

- Token-based authentication:
  - Tự động hết hạn sau 30 phút
  - Một tài khoản chỉ đăng nhập được trên 1 thiết bị (token cũ bị xóa khi login mới)
  - Tự động validate token mỗi request
- Input validation và sanitization
- SQL injection protection (PDO prepared statements)
- XSS protection (htmlspecialchars)
- Password hashing (bcrypt)
- File upload validation (kiểm tra type, size)

## Yêu cầu

- PHP 7.4+
- MySQL 5.7+
- Apache với mod_rewrite
- Bootstrap 5
- jQuery
- Summernote
- Chart.js
