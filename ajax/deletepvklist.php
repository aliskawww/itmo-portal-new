<?php
session_start();

// проверка, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    die("Доступ запрещен.");
}

require '../includes/db.php';

// получение данных из POST-запроса
$list_id = $_POST['list_id'] ?? null;
$profession_id = $_POST['profession_id'];
$delete_all = $_POST['delete_all'] ?? 0;
$expert_id = $_POST['expert_id'] ?? null;

// удаляем весь список эксперта (для администратора)
if ($delete_all && $expert_id && $_SESSION['user_role'] === 'admin') {
    $stmt = $conn->prepare("DELETE FROM expert_pvk_lists WHERE expert_id = ? AND profession_id = ?");
    $stmt->bind_param("ii", $expert_id, $profession_id);
    $stmt->execute();
    $stmt->close();
}
// удаляем весь список эксперта (для самого эксперта)
elseif ($delete_all && $_SESSION['user_role'] === 'expert') {
    $stmt = $conn->prepare("DELETE FROM expert_pvk_lists WHERE expert_id = ? AND profession_id = ?");
    $stmt->bind_param("ii", $_SESSION['user_id'], $profession_id);
    $stmt->execute();
    $stmt->close();
}
// удаляем конкретный пункт списка
elseif ($list_id) {
    // проверяем, что список принадлежит эксперту или удаляется администратором
    $stmt = $conn->prepare("SELECT expert_id FROM expert_pvk_lists WHERE id = ?");
    $stmt->bind_param("i", $list_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // проверка прав доступа
    if ($_SESSION['user_role'] === 'expert' && $result['expert_id'] !== $_SESSION['user_id']) {
        die("Вы не можете удалить этот список ПВК.");
    }

    // удаляем список
    $stmt = $conn->prepare("DELETE FROM expert_pvk_lists WHERE id = ?");
    $stmt->bind_param("i", $list_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: ../pages/pvk.php?profession_id=" . $profession_id);
exit();
