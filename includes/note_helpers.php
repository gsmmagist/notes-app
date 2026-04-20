<?php

declare(strict_types=1);

function findUserByEmail(PDO $pdo, string $email): array|false
{
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);

    return $stmt->fetch();
}

function createUser(PDO $pdo, string $email, string $password, string $name): int
{
    $stmt = $pdo->prepare(
        'INSERT INTO users (email, password_hash, name) VALUES (:email, :password_hash, :name)'
    );

    $stmt->execute([
        ':email' => $email,
        ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ':name' => $name,
    ]);

    return (int) $pdo->lastInsertId();
}

function getNotesByUser(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare(
        'SELECT n.*, GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ", ") AS tags
         FROM notes n
         LEFT JOIN note_tags nt ON nt.note_id = n.id
         LEFT JOIN tags t ON t.id = nt.tag_id
         WHERE n.user_id = :uid
         GROUP BY n.id
         ORDER BY n.is_pinned DESC, n.updated_at DESC'
    );

    $stmt->execute([':uid' => $userId]);

    return $stmt->fetchAll();
}

function getNoteById(PDO $pdo, int $noteId, int $userId): array|false
{
    $stmt = $pdo->prepare(
        'SELECT n.*, GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ", ") AS tags
         FROM notes n
         LEFT JOIN note_tags nt ON nt.note_id = n.id
         LEFT JOIN tags t ON t.id = nt.tag_id
         WHERE n.id = :id AND n.user_id = :uid
         GROUP BY n.id
         LIMIT 1'
    );

    $stmt->execute([
        ':id' => $noteId,
        ':uid' => $userId,
    ]);

    return $stmt->fetch();
}

function createNote(PDO $pdo, int $userId, string $title, string $body): int
{
    $stmt = $pdo->prepare(
        'INSERT INTO notes (user_id, title, body) VALUES (:uid, :title, :body)'
    );

    $stmt->execute([
        ':uid' => $userId,
        ':title' => $title,
        ':body' => $body,
    ]);

    return (int) $pdo->lastInsertId();
}

function updateNote(PDO $pdo, int $noteId, int $userId, string $title, string $body): void
{
    $stmt = $pdo->prepare(
        'UPDATE notes SET title = :title, body = :body WHERE id = :id AND user_id = :uid'
    );

    $stmt->execute([
        ':title' => $title,
        ':body' => $body,
        ':id' => $noteId,
        ':uid' => $userId,
    ]);
}

function deleteNote(PDO $pdo, int $noteId, int $userId): void
{
    $stmt = $pdo->prepare('DELETE FROM notes WHERE id = :id AND user_id = :uid');
    $stmt->execute([
        ':id' => $noteId,
        ':uid' => $userId,
    ]);

    cleanupUnusedTags($pdo, $userId);
}

function togglePin(PDO $pdo, int $noteId, int $userId): void
{
    $stmt = $pdo->prepare(
        'UPDATE notes SET is_pinned = CASE WHEN is_pinned = 1 THEN 0 ELSE 1 END WHERE id = :id AND user_id = :uid'
    );

    $stmt->execute([
        ':id' => $noteId,
        ':uid' => $userId,
    ]);
}

function normalizeTagName(string $tag): string
{
    $tag = trim($tag);
    $tag = preg_replace('/\s+/u', ' ', $tag) ?? $tag;

    return mb_substr($tag, 0, 50);
}

function parseTagNames(string $rawTags): array
{
    $parts = explode(',', $rawTags);
    $unique = [];

    foreach ($parts as $part) {
        $tag = normalizeTagName($part);

        if ($tag === '') {
            continue;
        }

        $unique[mb_strtolower($tag)] = $tag;
    }

    return array_values($unique);
}

function syncNoteTags(PDO $pdo, int $noteId, int $userId, array $tagNames): void
{
    $deleteLinks = $pdo->prepare('DELETE FROM note_tags WHERE note_id = :note_id');
    $deleteLinks->execute([':note_id' => $noteId]);

    if ($tagNames === []) {
        return;
    }

    $insertTag = $pdo->prepare(
        'INSERT INTO tags (name, user_id)
         VALUES (:name, :uid)
         ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)'
    );

    $insertLink = $pdo->prepare(
        'INSERT INTO note_tags (note_id, tag_id) VALUES (:note_id, :tag_id)'
    );

    foreach ($tagNames as $tagName) {
        $insertTag->execute([
            ':name' => $tagName,
            ':uid' => $userId,
        ]);

        $tagId = (int) $pdo->lastInsertId();

        $insertLink->execute([
            ':note_id' => $noteId,
            ':tag_id' => $tagId,
        ]);
    }
}

function cleanupUnusedTags(PDO $pdo, int $userId): void
{
    $stmt = $pdo->prepare(
        'DELETE t
         FROM tags t
         LEFT JOIN note_tags nt ON nt.tag_id = t.id
         WHERE t.user_id = :uid AND nt.tag_id IS NULL'
    );

    $stmt->execute([':uid' => $userId]);
}

function createNoteWithTags(PDO $pdo, int $userId, string $title, string $body, array $tagNames): int
{
    try {
        $pdo->beginTransaction();

        $noteId = createNote($pdo, $userId, $title, $body);
        syncNoteTags($pdo, $noteId, $userId, $tagNames);
        cleanupUnusedTags($pdo, $userId);

        $pdo->commit();

        return $noteId;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }
}

function updateNoteWithTags(PDO $pdo, int $noteId, int $userId, string $title, string $body, array $tagNames): void
{
    try {
        $pdo->beginTransaction();

        updateNote($pdo, $noteId, $userId, $title, $body);
        syncNoteTags($pdo, $noteId, $userId, $tagNames);
        cleanupUnusedTags($pdo, $userId);

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }
}

function getTagGroupsByUser(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare(
        'SELECT t.id AS tag_id,
                t.name AS tag_name,
                n.id AS note_id,
                n.title,
                n.updated_at,
                n.is_pinned
         FROM tags t
         LEFT JOIN note_tags nt ON nt.tag_id = t.id
         LEFT JOIN notes n ON n.id = nt.note_id
         WHERE t.user_id = :uid
         ORDER BY t.name ASC, n.is_pinned DESC, n.updated_at DESC'
    );

    $stmt->execute([':uid' => $userId]);
    $rows = $stmt->fetchAll();

    $groups = [];

    foreach ($rows as $row) {
        $tagId = (int) $row['tag_id'];

        if (!isset($groups[$tagId])) {
            $groups[$tagId] = [
                'tag_name' => $row['tag_name'],
                'notes' => [],
            ];
        }

        if ($row['note_id'] !== null) {
            $groups[$tagId]['notes'][] = [
                'id' => (int) $row['note_id'],
                'title' => $row['title'],
                'updated_at' => $row['updated_at'],
                'is_pinned' => (int) $row['is_pinned'],
            ];
        }
    }

    return array_values($groups);
}
