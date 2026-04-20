<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();

    $action = $_POST['action'] ?? '';
    $noteId = (int) ($_POST['note_id'] ?? 0);
    $userId = currentUserId();

    if ($noteId > 0) {
        if ($action === 'delete_note') {
            deleteNote($pdo, $noteId, $userId);
            set_flash('success', 'Заметка удалена.');
        }

        if ($action === 'toggle_pin') {
            togglePin($pdo, $noteId, $userId);
            set_flash('success', 'Статус закрепления изменён.');
        }
    }

    redirect('index.php');
}

$notes = getNotesByUser($pdo, currentUserId());
$successMessage = get_flash('success');
$pageTitle = 'Мои заметки';
require_once __DIR__ . '/includes/header.php';
?>
<section class="toolbar panel">
    <div>
        <h2>Список заметок</h2>
        <p class="muted">Закреплённые заметки всегда идут сверху.</p>
    </div>
    <div class="actions-row">
        <a class="btn primary" href="note.php">+ Новая заметка</a>
        <a class="btn" href="tags.php">Группы по тэгам</a>
    </div>
</section>

<?php if ($successMessage): ?>
    <div class="message success"><?= e($successMessage) ?></div>
<?php endif; ?>

<section class="panel note-list-panel">
    <?php if ($notes === []): ?>
        <div class="empty-state">
            <p>Заметок пока нет.</p>
            <p>Самое время создать первую — чистый лист терпеливее любого преподавателя.</p>
        </div>
    <?php else: ?>
        <div class="note-list-scroll">
            <?php foreach ($notes as $note): ?>
                <article class="note-item">
                    <div class="note-main">
                        <div class="note-meta-line">
                            <?php if ((int) $note['is_pinned'] === 1): ?>
                                <span class="chip pin-chip">Закреплено</span>
                            <?php endif; ?>
                            <?php if (!empty($note['tags'])): ?>
                                <span class="chip"><?= e($note['tags']) ?></span>
                            <?php endif; ?>
                        </div>

                        <a class="note-title-link" href="note.php?id=<?= (int) $note['id'] ?>">
                            <?= e($note['title'] !== '' ? $note['title'] : 'Без названия') ?>
                        </a>
                        <p class="muted">Обновлено: <?= e($note['updated_at']) ?></p>
                    </div>

                    <div class="note-actions">
                        <form method="post">
                            <?= csrf_input() ?>
                            <input type="hidden" name="action" value="toggle_pin">
                            <input type="hidden" name="note_id" value="<?= (int) $note['id'] ?>">
                            <button class="btn" type="submit">
                                <?= (int) $note['is_pinned'] === 1 ? 'Открепить' : 'Закрепить' ?>
                            </button>
                        </form>

                        <form method="post" onsubmit="return confirm('Удалить заметку?');">
                            <?= csrf_input() ?>
                            <input type="hidden" name="action" value="delete_note">
                            <input type="hidden" name="note_id" value="<?= (int) $note['id'] ?>">
                            <button class="btn danger" type="submit">Удалить</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
