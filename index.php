<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITMO Portal</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style1.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <a class="navbar-brand" href="index.php">ITMO Portal</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'expert')): ?>
                    <li class="nav-item">
                        <a href="pages/chat.php" class="btn btn-outline-dark mr-2">Чат</a>
                    </li>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a href="pages/proflist.php" class="btn btn-outline-dark mr-2">Управление профессиями</a>
                    </li>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a href="pages/admin_page.php" class="btn btn-outline-dark mr-2">Админ панель</a>
                    </li>
                <?php endif; ?>
                <?php if (!isset($_SESSION['username'])): ?>
                    <li class="nav-item">
                        <a href="pages/register.html" class="btn btn-outline-dark mr-2">Зарегистрироваться</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <?php if (isset($_SESSION['username'])): ?>
                        <span class="navbar-text mr-2"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <a href="pages/logout.php" class="btn btn-outline-dark">Выйти</a>
                    <?php else: ?>
                        <a href="pages/registr.html" class="btn btn-outline-dark">Войти</a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5 pt-5 text-center">
        <h1 class="mb-4">Добро пожаловать на ITMO Portal!</h1>
        <p>Здесь вы можете узнать о различных IT-профессиях, зарегистрироваться и получить консультацию.</p>
        <a href="pages/professii.php" class="btn btn-outline-dark mt-3">Описание профессий в ИТ</a>
        <div class="mt-4">
            <img src="assets/img/1.png" alt="Logo" class="img-fluid">
        </div>
    </div>

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
</body>
</html>