<?php
session_start();
require '../includes/db.php';

// Проверка административных привилегий
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Доступ запрещен."); // немедленный выход с ошибкой
}

$profession_id = $_POST['profession_id']; 
$expert_id = $_POST['expert_id'];         

// удаление связи между экспертом и профессией
$stmt = $conn->prepare("DELETE FROM profession_expert WHERE profession_id = ? AND expert_id = ?");
$stmt->bind_param("ii", $profession_id, $expert_id); 
$stmt->execute(); // выполнение запроса
$stmt->close();  

echo "Закрепление удалено.";

header("Location: ../pages/admin_page.php");
exit(); 
?>