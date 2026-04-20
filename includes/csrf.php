<?php

declare(strict_types=1);

/**
 * Генерирует CSRF-токен один раз на сессию и хранит его в $_SESSION.
 * Мы не передаём токен через URL, чтобы он не попадал в историю браузера и логи.
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Возвращает готовое hidden-поле для вставки в HTML-форму.
 * Так токен автоматически уходит вместе с POST-запросом.
 */
function csrf_input(): string
{
    $token = htmlspecialchars(csrf_token(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Проверяет, совпадает ли токен из формы с токеном в сессии.
 * Используем hash_equals(), чтобы сравнение было безопасным и не зависело от времени выполнения.
 */
function is_csrf_token_valid(?string $token): bool
{
    if (!is_string($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Прерывает обработку POST-запроса, если токен отсутствует или подменён.
 * Такой метод удобно вызывать в начале любого обработчика форм.
 */
function require_valid_csrf(): void
{
    if (!is_csrf_token_valid($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit('Ошибка 403: неверный CSRF-токен. Обновите страницу и попробуйте снова.');
    }
}
