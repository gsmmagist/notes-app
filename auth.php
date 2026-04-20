<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
requireGuest();

$mode = ($_GET['mode'] ?? 'login') === 'register' ? 'register' : 'login';
$errors = [];
$old = [
    'name' => '',
    'email' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();

    $action = $_POST['action'] ?? '';

    if ($action === 'register') {
        $mode = 'register';
        $old['name'] = trim((string) ($_POST['name'] ?? ''));
        $old['email'] = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

        if ($old['name'] === '') {
            $errors[] = 'Введите имя.';
        }

        if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Введите корректный e-mail.';
        }

        if (mb_strlen($password) < 6) {
            $errors[] = 'Пароль должен содержать минимум 6 символов.';
        }

        if ($password !== $passwordConfirm) {
            $errors[] = 'Пароли не совпадают.';
        }

        if (findUserByEmail($pdo, $old['email'])) {
            $errors[] = 'Пользователь с таким e-mail уже существует.';
        }

        if ($errors === []) {
            createUser($pdo, $old['email'], $password, $old['name']);
            set_flash('success', 'Регистрация выполнена. Теперь войдите в аккаунт.');
            redirect('auth.php?mode=login');
        }
    }

    if ($action === 'login') {
        $mode = 'login';
        $old['email'] = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Введите корректный e-mail.';
        }

        if ($password === '') {
            $errors[] = 'Введите пароль.';
        }

        if ($errors === []) {
            $user = findUserByEmail($pdo, $old['email']);

            if (!$user || !password_verify($password, $user['password_hash'])) {
                $errors[] = 'Неверный e-mail или пароль.';
            } else {
                loginUser($user);
                redirect('index.php');
            }
        }
    }
}

$successMessage = get_flash('success');
$pageTitle = $mode === 'register' ? 'Регистрация' : 'Авторизация';
require_once __DIR__ . '/includes/header.php';
?>
<section class="auth-wrap">
    <div class="panel auth-panel">
        <div class="switcher">
            <a class="<?= $mode === 'login' ? 'active' : '' ?>" href="auth.php?mode=login">Вход</a>
            <a class="<?= $mode === 'register' ? 'active' : '' ?>" href="auth.php?mode=register">Регистрация</a>
        </div>

        <?php if ($successMessage): ?>
            <div class="message success"><?= e($successMessage) ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['logout'])): ?>
            <div class="message success">Вы вышли из аккаунта.</div>
        <?php endif; ?>

        <?php if ($errors !== []): ?>
            <div class="message error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($mode === 'register'): ?>
            <form method="post" class="form-grid">
                <input type="hidden" name="action" value="register">
                <?= csrf_input() ?>

                <label>
                    <span>Имя</span>
                    <input type="text" name="name" maxlength="100" value="<?= e($old['name']) ?>" required>
                </label>

                <label>
                    <span>E-mail</span>
                    <input type="email" name="email" maxlength="255" value="<?= e($old['email']) ?>" required>
                </label>

                <label>
                    <span>Пароль</span>
                    <input type="password" name="password" minlength="6" required>
                </label>

                <label>
                    <span>Повторите пароль</span>
                    <input type="password" name="password_confirm" minlength="6" required>
                </label>

                <button class="btn primary" type="submit">Зарегистрироваться</button>
            </form>
        <?php else: ?>
            <form method="post" class="form-grid">
                <input type="hidden" name="action" value="login">
                <?= csrf_input() ?>

                <label>
                    <span>E-mail</span>
                    <input type="email" name="email" maxlength="255" value="<?= e($old['email']) ?>" required>
                </label>

                <label>
                    <span>Пароль</span>
                    <input type="password" name="password" required>
                </label>

                <button class="btn primary" type="submit">Войти</button>
            </form>
        <?php endif; ?>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
