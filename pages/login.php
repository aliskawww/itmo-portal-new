<?php
session_start(); 

require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']); 
    $password = $_POST['password']; 
    $email = isset($_POST['email']) ? trim($_POST['email']) : 'notdenified@gmail.com'; 

    $stmt = $conn->prepare("SELECT id, password, email, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username); 
    $stmt->execute(); 
    $stmt->store_result(); 

    // проверка, существует ли пользователь с указанным именем
    if ($stmt->num_rows == 0) {
        echo "Пользователь с таким именем не найден!"; 
    } else {
        // получение данных о пользователе
        $stmt->bind_result($id, $hashed_password, $email, $role);
        $stmt->fetch(); 

        // проверка правильности введенного пароля
        if (password_verify($password, $hashed_password)) {
            // если пароль правильный, сохраняем информацию о пользователе в сессии
            $_SESSION['user_id'] = $id;
            $_SESSION['user_role'] = $role;
            $_SESSION['user_email'] = $email;
            $_SESSION['username'] = $username;

            header("Location: ../index.php"); 
            exit();
        } else {
            echo "Неверный пароль!"; 
        }
    }
}
?>

<form method="POST">
    <input type="text" name="username" placeholder="Логин" required> 
    <input type="password" name="password" placeholder="Пароль" required> 
    <button type="submit">Войти</button> 
</form>