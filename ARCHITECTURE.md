# Kiến Trúc và Cách Code Hoạt Động

Tài liệu này giải thích chi tiết về kiến trúc, luồng xử lý và cách các thành phần trong hệ thống Game Store hoạt động.

## Mục Lục

1. [Kiến Trúc Tổng Quan](#kiến-trúc-tổng-quan)
2. [Luồng Xử Lý Request](#luồng-xử-lý-request)
3. [Hệ Thống Routing](#hệ-thống-routing)
4. [Xác Thực và Phân Quyền](#xác-thực-và-phân-quyền)
5. [Cấu Trúc Database](#cấu-trúc-database)
6. [Các Thành Phần Chính](#các-thành-phần-chính)
7. [Luồng Nghiệp Vụ Chính](#luồng-nghiệp-vụ-chính)
8. [Bảo Mật](#bảo-mật)

---

## Kiến Trúc Tổng Quan

Hệ thống được xây dựng theo mô hình **MVC (Model-View-Controller)** với các thành phần:

### 1. **Model** (`models/`)
- Tương tác trực tiếp với database
- Kế thừa từ `BaseModel` để có các phương thức chung (pagination, connection)
- Mỗi model đại diện cho một bảng trong database
- Sử dụng PDO với prepared statements để tránh SQL injection

### 2. **View** (`views/`)
- Chịu trách nhiệm hiển thị giao diện người dùng
- Sử dụng PHP để render dữ liệu động
- Tách biệt layout (header, footer) và nội dung
- Sử dụng Bootstrap 5 cho responsive design

### 3. **Controller** (`controllers/`)
- Xử lý logic nghiệp vụ
- Nhận request từ router, gọi Model để lấy dữ liệu, truyền cho View để hiển thị
- Xử lý validation, authentication, authorization
- Trả về response (redirect, render view, JSON)

### 4. **Config** (`config/`)
- Cấu hình database, email, constants
- Helper functions (sanitize, validateInput, isLoggedIn, getCurrentUser)
- Autoloading classes

---

## Luồng Xử Lý Request

### Bước 1: Request đến Server
```
User truy cập: http://localhost/Game_Store/game/detail?id=1
```

### Bước 2: Apache Rewrite (.htaccess)
File `.htaccess` chuyển tất cả request không phải file thực sang `index.php`:
```apache
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

### Bước 3: index.php (Front Controller)
1. **Load config**: `require_once config/config.php`
   - Khởi động session
   - Load autoloader
   - Định nghĩa constants (BASE_URL, UPLOAD_PATH, TOKEN_EXPIRY)

2. **Clean expired tokens**: Tự động xóa token hết hạn

3. **Parse route**: Tách route từ URL
   ```php
   $requestUri = $_SERVER['REQUEST_URI'];  // /Game_Store/game/detail?id=1
   $basePath = str_replace('index.php', '', $scriptName);  // /Game_Store/
   $route = str_replace($basePath, '', $requestUri);  // game/detail?id=1
   $route = explode('?', $route)[0];  // game/detail
   ```

4. **Route mapping**: Tìm controller và action tương ứng
   ```php
   $routes = [
       'game/detail' => ['GameController', 'detail'],
       // ...
   ];
   ```

5. **Load và execute controller**:
   ```php
   $controllerInstance = new GameController();
   $controllerInstance->detail();
   ```

### Bước 4: Controller Xử Lý
```php
class GameController {
    public function detail() {
        // 1. Lấy dữ liệu từ Model
        $game = $this->gameModel->findById($id);
        
        // 2. Xử lý logic nghiệp vụ
        $ownedGameIds = $libraryModel->getUserOwnedGameIds($userId);
        
        // 3. Load View và truyền dữ liệu
        require_once 'views/game/detail.php';
    }
}
```

### Bước 5: View Render
View nhận dữ liệu và render HTML:
```php
<h1><?php echo $game['title']; ?></h1>
```

### Bước 6: Response về Browser
HTML được gửi về browser và hiển thị cho user.

---

## Hệ Thống Routing

### Cơ Chế Routing

File `index.php` sử dụng **array-based routing**:

```php
$routes = [
    '' => ['HomeController', 'index'],
    'game' => ['GameController', 'index'],
    'game/detail' => ['GameController', 'detail'],
    'auth/login' => ['AuthController', 'login'],
    // ...
];
```

### Dynamic Routes

Một số route có thể xử lý động:
```php
if (preg_match('/^game\/detail/', $route)) {
    $controller = 'GameController';
    $action = 'detail';
}
```

### Query Parameters

Query string được giữ lại và truyền vào controller:
```
URL: game/detail?id=1
→ $_GET['id'] = 1
```

---

## Xác Thực và Phân Quyền

### Token-Based Authentication

Hệ thống sử dụng **token-based authentication** với các đặc điểm:

#### 1. **Tạo Token khi Đăng Nhập**

```php
// AuthController::login()
$token = bin2hex(random_bytes(32));  // Tạo token ngẫu nhiên 64 ký tự
$expiredAt = date('Y-m-d H:i:s', time() + TOKEN_EXPIRY);  // 30 phút

// Xóa token cũ của user (đảm bảo 1 tài khoản/1 thiết bị)
$tokenModel->deleteByUserId($userId);

// Lưu token mới vào database
$tokenModel->create($userId, $token);

// Lưu vào session và cookie
$_SESSION['auth_token'] = $token;
setcookie('auth_token', $token, ...);
```

#### 2. **Kiểm Tra Đăng Nhập (isLoggedIn)**

Hàm `isLoggedIn()` trong `config/config.php` hoạt động theo thứ tự ưu tiên:

**Bước 1: Kiểm tra Session**
```php
if (!empty($_SESSION['auth_token'])) {
    $user = $tokenModel->validateToken($_SESSION['auth_token']);
    if ($user) {
        // Đồng bộ thông tin user vào session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        // ...
        return true;
    }
    // Token không hợp lệ → xóa session và cookie
    $_SESSION = [];
    clearCookie();
    return false;
}
```

**Bước 2: Fallback sang Cookie**
```php
if (empty($_COOKIE['auth_token'])) {
    return false;
}

$user = $tokenModel->validateToken($_COOKIE['auth_token']);
if ($user === false) {
    // Token invalid/expired
    clearCookie();
    return false;
}

// Token hợp lệ → cập nhật session
$_SESSION['auth_token'] = $_COOKIE['auth_token'];
// ...
return true;
```

#### 3. **Single-Device Login**

Để đảm bảo 1 tài khoản chỉ đăng nhập trên 1 thiết bị:

- **Khi đăng nhập mới**: Xóa tất cả token cũ của user
  ```php
  $this->tokenModel->deleteByUserId($userId);
  ```

- **Khi kiểm tra token**: Validate token trong database
  - Nếu token không tồn tại trong DB → user đã đăng nhập ở nơi khác → logout

#### 4. **Token Expiry**

- Token tự động hết hạn sau **30 phút** (TOKEN_EXPIRY)
- Mỗi request, `index.php` gọi `cleanExpiredTokens()` để xóa token hết hạn

#### 5. **Logout**

```php
// AuthController::logout()
$tokenModel->deleteToken($_SESSION['auth_token']);
$_SESSION = [];
clearCookie();
```

### Phân Quyền

- **isLoggedIn()**: Kiểm tra user đã đăng nhập chưa
- **isAdmin()**: Kiểm tra user có role = 'admin'
- **requireLogin()**: Middleware redirect về login nếu chưa đăng nhập
- **requireAdmin()**: Middleware redirect về home nếu không phải admin

---

## Cấu Trúc Database

### Các Bảng Chính

1. **users**: Thông tin người dùng
2. **user_tokens**: Token đăng nhập (1 token/user, tự hết hạn)
3. **games**: Thông tin game
4. **game_categories**: Danh mục game
5. **game_category_map**: Liên kết game - category (many-to-many)
6. **carts** & **cart_items**: Giỏ hàng
7. **orders** & **order_items**: Đơn hàng
8. **libraries**: Game đã sở hữu (khi admin duyệt đơn)
9. **reviews**: Đánh giá game (1-5 điểm)

### Quan Hệ

- **users** → **user_tokens** (1:N)
- **users** → **orders** (1:N)
- **users** → **libraries** (1:N)
- **games** → **game_category_map** → **game_categories** (N:M)
- **orders** → **order_items** → **games** (1:N)
- **orders** → **libraries** (1:N) - Khi admin duyệt đơn, game được thêm vào library

---

## Các Thành Phần Chính

### 1. BaseModel

Tất cả Model kế thừa từ `BaseModel`:

```php
class BaseModel {
    protected $conn;  // PDO connection
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    protected function paginate($query, $params, $page, $perPage) {
        // Xử lý phân trang tự động
    }
}
```

**Lợi ích**:
- Tái sử dụng connection
- Phương thức `paginate()` dùng chung cho tất cả Model

### 2. Helper Functions (config/config.php)

#### sanitize($data)
- Loại bỏ whitespace, slashes
- Chuyển HTML special chars → entities (chống XSS)

#### validateInput($input)
- Kiểm tra các pattern nguy hiểm (SQL injection, XSS)
- Trả về `false` nếu phát hiện mã độc

#### isLoggedIn()
- Kiểm tra user đã đăng nhập
- Validate token trong database

#### getCurrentUser()
- Trả về thông tin user hiện tại (array)
- Tự động validate token và cập nhật session

#### redirect($url)
- Redirect đến URL với BASE_URL prefix

### 3. Session Management

Session được sử dụng để:
- Lưu thông tin user (user_id, user_name, user_avatar, auth_token)
- Lưu thông báo (success, error)
- Cập nhật ngay lập tức sau khi thay đổi (ví dụ: đổi avatar)

### 4. File Upload

Quy trình upload file (ảnh, video):

1. **Validation**:
   - Kiểm tra file có tồn tại
   - Kiểm tra kích thước (max size)
   - Kiểm tra MIME type
   - Kiểm tra extension

2. **Lưu file**:
   ```php
   $uploadPath = UPLOAD_PATH . 'games/';
   $filename = uniqid() . '_' . $originalName;
   move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath . $filename);
   ```

3. **Lưu vào database**:
   ```php
   $gameModel->updateImage($gameId, $filename);
   ```

---

## Luồng Nghiệp Vụ Chính

### 1. Đăng Ký/Đăng Nhập

#### Đăng Ký
```
1. User điền form → POST /auth/register
2. AuthController::register()
   - Validate input (sanitize, validateInput)
   - Kiểm tra email đã tồn tại chưa
   - Hash password: password_hash($password, PASSWORD_DEFAULT)
   - Tạo user trong database
3. Redirect → /auth/login
```

#### Đăng Nhập
```
1. User điền form → POST /auth/login
2. AuthController::login()
   - Validate input
   - Tìm user theo email
   - Verify password: password_verify($password, $user['password'])
   - Tạo token: bin2hex(random_bytes(32))
   - Xóa token cũ: deleteByUserId($userId)  // Single-device
   - Lưu token vào DB và cookie/session
3. Redirect → Home
```

### 2. Thêm Vào Giỏ Hàng

```
1. User click "Thêm giỏ hàng" → AJAX POST /cart/add
2. CartController::add()
   - Kiểm tra đăng nhập (nếu chưa → return JSON {success: false, redirect})
   - Kiểm tra game có phải "upcoming" không (nếu có → không cho thêm)
   - Lấy hoặc tạo cart cho user
   - Kiểm tra game đã có trong cart chưa
   - Thêm vào cart_items
3. Return JSON {success: true, message}
4. Frontend cập nhật UI (cart count, button state)
```

### 3. Thanh Toán

#### Từ Giỏ Hàng
```
1. User vào /cart → xem giỏ hàng
2. Click "Thanh toán" → /order/checkout
3. OrderController::checkout() (GET)
   - Lấy items từ cart
   - Tính tổng tiền
   - Hiển thị form thanh toán
4. User submit → POST /order/checkout
5. OrderController::checkout() (POST)
   - Validate payment_method
   - Tạo order: $orderModel->create($userId, $total, $paymentMethod)
   - Thêm order_items: foreach items → addItem($orderId, $gameId, $price)
   - Xóa cart: $cartModel->clearCart($cartId)
6. Redirect → /order/history
```

#### Buy Now (Mua Ngay)
```
1. User click "Mua ngay" trên trang detail → /order/checkout?buy_now=123
2. OrderController::checkout() (GET)
   - Kiểm tra đăng nhập (nếu chưa → redirect login)
   - Lấy game theo ID
   - Tính giá (sale_price hoặc price)
   - Hiển thị form thanh toán (chỉ 1 item)
3. User submit → POST /order/checkout
4. OrderController::checkout() (POST)
   - Tạo order với 1 item (buy_now flow)
   - Không cần xóa cart (vì không qua cart)
5. Redirect → /order/history
```

### 4. Admin Duyệt Đơn

```
1. Admin vào /admin/order/detail?id=123
2. AdminOrderController::detail()
   - Lấy order và items
   - Hiển thị form cập nhật status
3. Admin chọn "Đã duyệt" → POST /admin/order/update-status
4. AdminOrderController::updateStatus()
   - Cập nhật order.status = 'approved'
   - Nếu status = 'approved':
     → Library::addFromOrder($orderId)
     → Thêm tất cả games trong order vào libraries table
5. Redirect → /admin/order
```

**Kết quả**:
- Order biến mất khỏi trang "Đơn hàng" của user (vì chỉ hiển thị pending/cancelled)
- Games xuất hiện trong Library của user

### 5. Hiển Thị Game

#### Trang Danh Sách Game
```
1. User vào /game
2. GameController::index()
   - Lấy filter params (search, category, type)
   - Gọi $gameModel->getAll($categoryId, $page, $perPage)
   - Lấy ownedGameIds và cartGameIds (nếu đã login)
3. View hiển thị:
   - Filter form
   - Danh sách game với pagination
   - Button: "Chơi ngay" (nếu owned), "Trong giỏ" (nếu in cart), "Thêm giỏ" (nếu available)
```

#### Trang Chi Tiết Game
```
1. User vào /game/detail?id=123
2. GameController::detail()
   - Lấy game theo ID
   - Kiểm tra user đã sở hữu chưa (Library)
   - Kiểm tra game có trong cart chưa
   - Lấy games cùng category (để hiển thị ở cuối trang)
3. View hiển thị:
   - Thông tin game (video, images, description)
   - Button: "Chơi ngay" (nếu owned), "Mua ngay", "Thêm giỏ", "Login to Buy"
   - Reviews và comments
   - Games cùng category
```

### 6. Excel Export

```
1. Admin click "Xuất Excel" → /admin/export/users
2. AdminExportController::exportUsers()
   - Lấy tất cả users từ database
   - Tạo HTML table với UTF-8 BOM
   - Set headers: Content-Type: application/vnd.ms-excel
   - Output HTML (Excel có thể mở HTML)
3. Browser download file .xls
```

**Lưu ý**: Sử dụng HTML format với UTF-8 BOM để hiển thị tiếng Việt đúng trong Excel.

---

## Bảo Mật

### 1. SQL Injection Prevention

- **Prepared Statements**: Tất cả queries sử dụng PDO prepared statements
  ```php
  $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
  $stmt->bindValue(':email', $email);
  $stmt->execute();
  ```

### 2. XSS Prevention

- **sanitize()**: Chuyển HTML special chars → entities
  ```php
  htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
  ```
- **Output escaping**: Tất cả output trong view đều được escape

### 3. CSRF Protection

- Có thể thêm CSRF token cho các form quan trọng (chưa implement)

### 4. File Upload Security

- **Validation**:
  - Kiểm tra MIME type
  - Kiểm tra extension
  - Kiểm tra kích thước
  - Validate image bằng `getimagesize()`
- **File naming**: Sử dụng `uniqid()` để tránh conflict và path traversal

### 5. Password Security

- **Hashing**: `password_hash()` với bcrypt
- **Verification**: `password_verify()`
- **Reset token**: Token ngẫu nhiên, có expiry time

### 6. Authentication Security

- **Token**: 64 ký tự ngẫu nhiên (bin2hex(random_bytes(32)))
- **Expiry**: 30 phút tự động
- **Single-device**: Xóa token cũ khi đăng nhập mới
- **HttpOnly cookie**: Không cho JavaScript truy cập
- **SameSite**: Lax để chống CSRF

### 7. Input Validation

- **validateInput()**: Kiểm tra các pattern nguy hiểm
- **sanitize()**: Làm sạch dữ liệu
- **Type checking**: Sử dụng `intval()`, `filter_var()` cho các loại dữ liệu cụ thể

---

## Kết Luận

Hệ thống Game Store được xây dựng với:
- **Kiến trúc MVC** rõ ràng, dễ maintain
- **Token-based authentication** an toàn, hỗ trợ single-device login
- **Prepared statements** chống SQL injection
- **Input validation & sanitization** chống XSS
- **File upload validation** an toàn
- **Session management** hiệu quả
- **Routing system** linh hoạt

Code được tổ chức tốt, dễ đọc, dễ mở rộng và bảo trì.
