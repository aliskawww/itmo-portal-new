<?php
session_start();
require '../includes/db.php';

if (!isset($_GET['profession_id'])) {
    header("Location: ../pages/professii.php");
    exit();
}

$profession_id = $_GET['profession_id'];

// получаем основной список ПВК для профессии
$stmt = $conn->prepare("
    SELECT pvk.id, pvk.name, pvk.description 
    FROM pvk
    JOIN profession_pvk ON pvk.id = profession_pvk.pvk_id
    WHERE profession_pvk.profession_id = ?
");
$stmt->bind_param("i", $profession_id);
$stmt->execute();
$pvk_list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// получаем списки ПВК от экспертов
$stmt = $conn->prepare("
    SELECT epl.id, epl.expert_id, u.username, pvk.name, epl.priority
    FROM expert_pvk_lists epl
    JOIN users u ON epl.expert_id = u.id
    JOIN pvk ON epl.pvk_id = pvk.id
    WHERE epl.profession_id = ?
    ORDER BY epl.expert_id, epl.priority
");
$stmt->bind_param("i", $profession_id);
$stmt->execute();
$expert_pvk_lists = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// проверяем, закреплен ли текущий эксперт за этой профессией
$is_assigned_expert = false;
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'expert') {
    $stmt = $conn->prepare("SELECT id FROM profession_expert WHERE profession_id = ? AND expert_id = ?");
    $stmt->bind_param("ii", $profession_id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();
    $is_assigned_expert = $stmt->num_rows > 0;
    $stmt->close();
}

// проверяем, есть ли у эксперта список ПВК
$has_expert_list = false;
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'expert') {
    $stmt = $conn->prepare("SELECT id FROM expert_pvk_lists WHERE expert_id = ? AND profession_id = ?");
    $stmt->bind_param("ii", $_SESSION['user_id'], $profession_id);
    $stmt->execute();
    $stmt->store_result();
    $has_expert_list = $stmt->num_rows > 0;
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ПВК для профессии</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style1.css">
</head>
<body>
    <!-- навигационная панель -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <a class="navbar-brand" href="../index.php">ITMO Portal</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'expert')): ?>
                    <li class="nav-item">
                        <a href="chat.php" class="btn btn-outline-dark mr-2">Чат</a>
                    </li>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a href="admin_page.php" class="btn btn-outline-dark mr-2">Админ панель</a>
                    </li>
                <?php endif; ?>
                <?php if (!isset($_SESSION['username'])): ?>
                    <li class="nav-item">
                        <a href="register.html" class="btn btn-outline-dark mr-2">Зарегистрироваться</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="btn btn-outline-dark mr-2" href="../index.php">На главную</a>
                </li>
                <li class="nav-item">
                    <?php if (isset($_SESSION['username'])): ?>
                        <span class="navbar-text mr-2"><?= htmlspecialchars($_SESSION['username']) ?></span>
                        <a href="logout.php" class="btn btn-outline-dark">Выйти</a>
                    <?php else: ?>
                        <a href="registr.html" class="btn btn-outline-dark">Вход на портал</a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </nav>

    <!-- основной контент -->
    <div class="container mt-5 pt-5">
        <h1 class="text-center mb-4">ПВК для профессии</h1>
        <a href="professii.php" class="btn btn-secondary mb-4">Назад</a>

        <!-- основной список ПВК -->
        <?php if (empty($expert_pvk_lists)): ?>
            <h2>Основной список ПВК</h2>
            <ul class="list-group mb-4">
                <?php foreach ($pvk_list as $pvk): ?>
                    <li class="list-group-item">
                        <strong><?= htmlspecialchars($pvk['name']) ?></strong>
                        <p><?= htmlspecialchars($pvk['description']) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <!-- списки ПВК от экспертов -->
        <h2>Списки ПВК от экспертов</h2>
        <?php if (!empty($expert_pvk_lists)): ?>
            <?php
            $current_expert = null;
            foreach ($expert_pvk_lists as $list):
                if ($current_expert !== $list['expert_id']):
                    $current_expert = $list['expert_id'];
            ?>
                    <h3 class="mt-4">Эксперт: <?= htmlspecialchars($list['username']) ?></h3>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <form action="../ajax/deletepvklist.php" method="POST" class="mb-3">
                            <input type="hidden" name="profession_id" value="<?= $profession_id ?>">
                            <input type="hidden" name="expert_id" value="<?= $list['expert_id'] ?>">
                            <input type="hidden" name="delete_all" value="1">
                            <button type="submit" class="btn btn-danger">Удалить весь список эксперта</button>
                        </form>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'expert' && $_SESSION['user_id'] == $list['expert_id']): ?>
                        <form action="../ajax/deletepvklist.php" method="POST" class="mb-3">
                            <input type="hidden" name="profession_id" value="<?= $profession_id ?>">
                            <input type="hidden" name="delete_all" value="1">
                            <button type="submit" class="btn btn-danger">Удалить весь список</button>
                        </form>
                    <?php endif; ?>
                    <ul class="list-group mb-4">
            <?php endif; ?>
                        <li class="list-group-item">
                            <strong><?= htmlspecialchars($list['name']) ?></strong> (Приоритет: <?= $list['priority'] ?>)
                            <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_id'] == $list['expert_id'])): ?>
                                <form action="../ajax/deletepvklist.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="list_id" value="<?= $list['id'] ?>">
                                    <input type="hidden" name="profession_id" value="<?= $profession_id ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                                </form>
                            <?php endif; ?>
                        </li>
            <?php if ($current_expert !== $list['expert_id']): ?>
                    </ul>
            <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">Нет списков ПВК от экспертов.</p>
        <?php endif; ?>

        <!-- режим конструктора для экспертов -->
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'expert' && $is_assigned_expert && !$has_expert_list): ?>
            <h2 class="mt-4">Конструктор списка ПВК</h2>
            <form action="../ajax/savepvklist.php" method="POST">
                <input type="hidden" name="profession_id" value="<?= $profession_id ?>">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ПВК</th>
                            <th>Приоритет</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pvk_list as $pvk): ?>
                            <tr>
                                <td><?= htmlspecialchars($pvk['name']) ?></td>
                                <td>
                                    <input type="number" name="priority[<?= $pvk['id'] ?>]" min="1" max="<?= count($pvk_list) ?>" class="form-control" placeholder="Приоритет">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" class="btn btn-primary">Сохранить список</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- футер -->
    <footer class="footer bg-light text-dark mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-4">
                    <h5>ITMO Portal</h5>
                    <p>Ваш портал в мир IT-профессий.</p>
                </div>
                <div class="col-md-4">
                    <h5>Контакты</h5>
                    <p>Email: info@itmoportal.ru</p>
                    <p>Телефон: +7 (999) 123-45-67</p>
                </div>
                <div class="col-md-4">
                    <h5>Социальные сети</h5>
                    <a href="#" class="text-dark">Facebook</a><br>
                    <a href="#" class="text-dark">Twitter</a><br>
                    <a href="#" class="text-dark">Instagram</a>
                </div>
            </div>
        </div>
        <div class="text-center py-3" style="background-color: rgba(0, 0, 0, 0.05);">
            © 2024 ITMO Portal. Все права защищены.
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // проверка уникальности приоритетов
        document.querySelector('form').addEventListener('submit', function (e) {
            const priorities = [];
            document.querySelectorAll('input[name^="priority"]').forEach(input => {
                if (input.value) {
                    if (priorities.includes(input.value)) {
                        alert('Приоритеты не должны повторяться!');
                        e.preventDefault();
                        return;
                    }
                    priorities.push(input.value);
                }
            });
        });
    </script>
</body>
</html>
