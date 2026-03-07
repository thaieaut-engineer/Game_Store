<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Support.php';

class SupportController
{
    private $supportModel;

    public function __construct()
    {
        $this->supportModel = new Support();
    }

    public function index()
    {
        require_once __DIR__ . '/../views/support/index.php';
    }

    public function submit()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $subject = $_POST['subject'] ?? 'Yêu cầu hỗ trợ mới';
            $message = $_POST['message'] ?? '';
            $user_id = $_SESSION['user_id'] ?? null;

            if (empty($name) || empty($email) || empty($message)) {
                $_SESSION['error'] = "Vui lòng điền đầy đủ các trường bắt buộc.";
                header('Location: ' . BASE_URL . '/support');
                exit();
            }

            $data = [
                'user_id' => $user_id,
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message
            ];

            if ($this->supportModel->create($data)) {
                $_SESSION['success'] = "Gửi yêu cầu thành công! Chúng tôi sẽ phản hồi sớm nhất có thể.";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại sau.";
            }

            header('Location: ' . BASE_URL . '/support');
            exit();
        }
    }
}
?>