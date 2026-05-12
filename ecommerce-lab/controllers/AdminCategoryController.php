<?php
// controllers/AdminCategoryController.php

require_once '../config/database.php';
require_once '../models/Category.php';
require_once '../config/helpers.php';

function admin_categories_action() {
    global $pdo;
    require_admin();

    $error = '';
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'create') {
                $name = trim($_POST['name']);
                $parent_id = $_POST['parent_id'];
                
                if (empty($name)) {
                    $error = "Category name is required.";
                } else {
                    if (create_category($pdo, $name, $parent_id)) {
                        $success = "Category created successfully.";
                    } else {
                        $error = "Failed to create category.";
                    }
                }
            } elseif ($_POST['action'] === 'delete') {
                $id = $_POST['id'];
                if (delete_category($pdo, $id)) {
                    $success = "Category deleted successfully.";
                } else {
                    $error = "Cannot delete category: It has child categories or associated products.";
                }
            }
        }
    }

    $categories = get_all_categories($pdo);
    
    $content_view = '../views/admin/categories.php';
    require '../views/layout.php';
}
?>
