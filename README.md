# Game Store Website

Website bán game được xây dựng bằng PHP, HTML, CSS, JavaScript và Bootstrap với mô hình MVC.

## Tính năng

### Người dùng
- Đăng ký/Đăng nhập
- Quên mật khẩu (gửi email)
- Đổi thông tin tài khoản (ảnh và tên)
- Đổi mật khẩu
- Tìm kiếm game
- Thêm vào giỏ hàng
- Xem giỏ hàng
- Thanh toán
- Xem tình trạng đơn hàng
- Xem lịch sử mua game
- Đánh giá và bình luận (1-10 điểm)

### Admin
- Trang tổng quan với thống kê:
  - Số game, chủ đề, người dùng, đơn hàng
  - Biểu đồ doanh thu (line chart)
  - Biểu đồ chủ đề bán chạy (pie chart)
- Quản lý game (CRUD) với Summernote
- Quản lý chủ đề (CRUD)
- Quản lý tài khoản (CRUD)
- Quản lý đơn hàng (Chờ duyệt, Thành công)
- Quản lý bình luận (lọc theo điểm 1-10, chỉ xóa)

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
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── config/
├── controllers/
├── models/
├── uploads/
├── views/
│   ├── admin/
│   ├── auth/
│   ├── cart/
│   ├── game/
│   ├── home/
│   ├── layout/
│   ├── order/
│   └── profile/
├── .htaccess
├── index.php
└── README.md
```

## Bảo mật

- Token-based authentication (tự động hết hạn sau 30 phút)
- Input validation và sanitization
- SQL injection protection
- XSS protection

## Yêu cầu

- PHP 7.4+
- MySQL 5.7+
- Apache với mod_rewrite
- Bootstrap 5
- jQuery
- Summernote
- Chart.js
