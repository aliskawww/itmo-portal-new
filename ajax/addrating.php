<?php
session_start();

// проверка авторизации и роли пользователя:
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user' && $_SESSION['user_role'] !== 'expert') {
    header("Location: ../pages/registr.html"); 
    exit(); 
}

require '../includes/db.php';

// обработка данных формы только для POST-запросов
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $expert_id = $_SESSION['user_id'];   
    $profession_id = $_POST['profession_id']; 
    $rating = $_POST['rating'];           

    // запись оценки в базу данных 
    $stmt = $conn->prepare("INSERT INTO ratings (expert_id, profession_id, rating) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $expert_id, $profession_id, $rating); 
    $stmt->execute(); 
    $stmt->close();   

    header("Location: ../pages/professii.php");
    exit(); 
}
?>