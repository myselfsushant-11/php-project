<?php
require_once __DIR__ . '/../includes/auth.php';

$q = trim($_GET['q'] ?? '');
if ($q === '') {
    json_out(['success' => true, 'movies' => []]);
}

$stmt = $pdo->prepare(
    "SELECT id, title, genre, language, poster, rating
     FROM movies
     WHERE title LIKE ? OR genre LIKE ? OR language LIKE ?
     ORDER BY status = 'now_showing' DESC, title ASC
     LIMIT 8"
);
$like = '%' . $q . '%';
$stmt->execute([$like, $like, $like]);
$movies = $stmt->fetchAll();

json_out(['success' => true, 'movies' => $movies]);
