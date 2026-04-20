<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
requireLogin();

$groups = getTagGroupsByUser($pdo, currentUserId());
$pageTitle = 'Группы заметок';
require_once __DIR__ . '/includes/header.php';
?>
<section class="panel">
    <div class="actions-row actions-row-between">
        <div>
            <h2>Группы по тэгам</h2>
            <p class="muted">Каждую группу можно раскрыть и перейти в нужную заметку.</p>
        </div>
        <a class="btn" href="index.php">← К списку заметок</a>
    </div>
</section>

<?php if ($groups === []): ?>
    <section class="panel empty-state">
        <p>Тэгов пока нет.</p>
        <p>Добавьте тэги в форме редактирования заметки, и здесь появятся группы.</p>
    </section>
<?php else: ?>
    <section class="tag-groups">
        <?php foreach ($groups as $group): ?>
            <details class="panel tag-group" open>
                <summary>
                    <span>#<?= e($group['tag_name']) ?></span>
                    <span class="muted">Заметок: <?= count($group['notes']) ?></span>
                </summary>

                <?php if ($group['notes'] === []): ?>
                    <p class="muted">В группе пока нет заметок.</p>
                <?php else: ?>
                    <div class="group-note-list">
                        <?php foreach ($group['notes'] as $note): ?>
                            <a class="group-note-link" href="note.php?id=<?= (int) $note['id'] ?>">
                                <span>
                                    <?= e($note['title'] !== '' ? $note['title'] : 'Без названия') ?>
                                    <?php if ((int) $note['is_pinned'] === 1): ?>
                                        <span class="chip pin-chip">Закреплено</span>
                                    <?php endif; ?>
                                </span>
                                <span class="muted"><?= e($note['updated_at']) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </details>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
