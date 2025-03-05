<?php
session_start();

require '../includes/db.php'; 

// проверка прав администратора
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Ошибка: доступ запрещен."); 
}

// проверка, был ли отправлен POST-запрос
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    
    // запрос для безопасного удаления
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id); 
    
    // Выполнение запроса и обработка результата
    if ($stmt->execute()) {
        header("Location: ../pages/admin_page.php");
    } else {
        echo "Ошибка при обновлении роли: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo "Некорректный запрос.";
}
?>
