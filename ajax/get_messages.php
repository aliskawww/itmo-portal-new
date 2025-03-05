<?php
session_start();
require '../includes/db.php';

if (isset($_GET['receiver_id'])) {
    $receiver_id = $_GET['receiver_id']; 
    $user_id = $_SESSION['user_id'];   

    // запрос на получение истории сообщений между двумя пользователями
    $stmt = $conn->prepare("SELECT * FROM messages 
                           WHERE (sender_id = ? AND receiver_id = ?) 
                           OR (sender_id = ? AND receiver_id = ?) 
                           ORDER BY timestamp ASC");
    $stmt->bind_param("iiii", $user_id, $receiver_id, $receiver_id, $user_id);
    $stmt->execute();
    
    // получение результата в виде массива
    $result = $stmt->get_result();
    $messages = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // вывод сообщений с форматированием
    foreach ($messages as $msg): ?>
        <p class="message">
            <strong>
                <?php 
                // если отправитель - текущий пользователь
                if ($msg['sender_id'] == $user_id) {
                    echo 'Вы';
                } else {
                    // определение роли собеседника для подписи
                    echo ($_SESSION['user_role'] === 'user') ? 'Консультант' : 'Пользователь';
                } 
                ?>:
            </strong>
            
            <?= htmlspecialchars($msg['message']) ?>
        </p>
    <?php endforeach;
}
?>