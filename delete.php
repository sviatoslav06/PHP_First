<?php
global $pdo;
include($_SERVER["DOCUMENT_ROOT"] . "/config/connection_database.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'];

    // Perform deletion (add appropriate error handling)
    $deleteSql = "DELETE FROM categories WHERE id = :id";
    $stmtDelete = $pdo->prepare($deleteSql);
    $stmtDelete->bindParam(':id', $category_id);

    if ($stmtDelete->execute()) {
        // Redirect to index.php or another appropriate page
        header("Location: /index.php");
        exit;
    } else {
        echo "Error deleting category.";
    }
}
?>