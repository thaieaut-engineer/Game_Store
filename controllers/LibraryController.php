<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Library.php';

class LibraryController {
    private $libraryModel;

    public function __construct() {
        if (!isLoggedIn()) {
            redirect('auth/login');
        }
        $this->libraryModel = new Library();
    }

    public function index() {
        $page = $_GET['page'] ?? 1;
        $user = getCurrentUser();
        $result = $this->libraryModel->getByUser($user['id'], $page, 12);

        require_once __DIR__ . '/../views/library/index.php';
    }
}
?>

