<?php
session_start();
require '../includes/db.php'; 

// проверка административных привилегий
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Ошибка: доступ запрещен."); // завершение при отсутствии прав
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']); 
    $new_role = trim($_POST['role']);     
    
    // проверка допустимости роли 
    if (!in_array($new_role, ['user', 'expert', 'admin', 'consultant'])) {
        die("Ошибка: недопустимая роль."); 
    }
    
    // обновление роли пользователя с использованием подготовленного запроса
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $new_role, $user_id); 
    
    // выполнение запроса и обработка результата
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