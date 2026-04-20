<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Notes App';
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="app-shell">
    <header class="site-header">
        <div>
            <p class="site-kicker">Учебный проект</p>
            <h1><?= e($pageTitle) ?></h1>
        </div>

        <?php if (isLoggedIn()): ?>
            <nav class="nav">
                <span class="user-badge"><?= e(currentUserName()) ?></span>
                <a href="index.php">Заметки</a>
                <a href="tags.php">Группы</a>
                <a href="logout.php">Выход</a>
            </nav>
        <?php endif; ?>
    </header>

    <main class="content">
