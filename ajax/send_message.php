<?php
session_start();
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_id = $_SESSION['user_id'];      
    $receiver_id = $_POST['receiver_id'];   
    $message = trim($_POST['message']);     

    // проверка существования получателя в системе
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param("i", $receiver_id);  
    $stmt->execute();
    $stmt->store_result();

    // если получатель существует - сохраняем сообщение
    if ($stmt->num_rows > 0) {
        // вставка нового сообщения с использованием подготовленного запроса
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $sender_id, $receiver_id, $message); 
        $stmt->execute();
        $stmt->close();
    }

    header("Location: ../pages/chat.php" . ($_SESSION['user_role'] === 'consultant' ? "?receiver_id=$receiver_id" : ""));
    exit();
}
?>