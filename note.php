<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
requireLogin();

$userId = currentUserId();
$noteId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$isEdit = $noteId > 0;
$errors = [];

$note = [
    'id' => 0,
    'title' => '',
    'body' => '',
    'tags' => '',
];

if ($isEdit) {
    $existingNote = getNoteById($pdo, $noteId, $userId);

    if (!$existingNote) {
        http_response_code(404);
        exit('Заметка не найдена.');
    }

    $note = $existingNote;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();

    $postedNoteId = (int) ($_POST['note_id'] ?? 0);
    $title = trim((string) ($_POST['title'] ?? ''));
    $body = trim((string) ($_POST['body'] ?? ''));
    $tagsRaw = trim((string) ($_POST['tags'] ?? ''));

    if (mb_strlen($title) > 255) {
        $errors[] = 'Заголовок не должен превышать 255 символов.';
    }

    if ($errors === []) {
        $tagNames = parseTagNames($tagsRaw);

        if ($postedNoteId > 0) {
            $editableNote = getNoteById($pdo, $postedNoteId, $userId);

            if (!$editableNote) {
                http_response_code(404);
                exit('Заметка не найдена.');
            }

            updateNoteWithTags($pdo, $postedNoteId, $userId, $title, $body, $tagNames);
            $savedNoteId = $postedNoteId;
        } else {
            $savedNoteId = createNoteWithTags($pdo, $userId, $title, $body, $tagNames);
        }

        set_flash('success', 'Заметка сохранена.');
        redirect('note.php?id=' . $savedNoteId);
    }

    $note = [
        'id' => $postedNoteId,
        'title' => $title,
        'body' => $body,
        'tags' => $tagsRaw,
    ];
}

$successMessage = get_flash('success');
$pageTitle = $note['id'] ? 'Редактирование заметки' : 'Новая заметка';
require_once __DIR__ . '/includes/header.php';
?>
<section class="panel note-editor-panel">
    <div class="actions-row actions-row-between">
        <div>
            <h2><?= $note['id'] ? 'Редактирование заметки' : 'Создание заметки' ?></h2>
            <p class="muted">Тэги вводятся через запятую.</p>
        </div>
        <a class="btn" href="index.php">← К списку</a>
    </div>

    <?php if ($successMessage): ?>
        <div class="message success"><?= e($successMessage) ?></div>
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

    <form method="post" class="form-grid note-form">
        <input type="hidden" name="note_id" value="<?= (int) $note['id'] ?>">
        <?= csrf_input() ?>

        <label>
            <span>Заголовок</span>
            <input type="text" name="title" maxlength="255" value="<?= e($note['title']) ?>" placeholder="Например: Идеи для проекта">
        </label>

        <label>
            <span>Тэги</span>
            <input type="text" name="tags" value="<?= e($note['tags'] ?? '') ?>" placeholder="работа, личное, идеи">
        </label>

        <label>
            <span>Текст заметки</span>
            <textarea name="body" rows="14" placeholder="Введите текст заметки..."><?= e($note['body']) ?></textarea>
        </label>

        <div class="actions-row">
            <button class="btn primary" type="submit">Сохранить</button>
            <a class="btn" href="index.php">Вернуться к списку</a>
        </div>
    </form>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
