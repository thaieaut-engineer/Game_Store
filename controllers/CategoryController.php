<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Category.php';

class CategoryController
{
    private $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    public function index()
    {
        $pageTitle = 'Chủ đề Game - Game Store';
        // Get all categories with game counts
        $categories = $this->categoryModel->getPopular(100); // reuse getPopular for game count, but higher limit
        require_once __DIR__ . '/../views/category/index.php';
    }
}
?>