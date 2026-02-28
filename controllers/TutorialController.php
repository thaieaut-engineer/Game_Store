<?php
require_once __DIR__ . '/../config/config.php';

class TutorialController
{
    public function index()
    {
        require_once __DIR__ . '/../views/tutorial/index.php';
    }
}
?>