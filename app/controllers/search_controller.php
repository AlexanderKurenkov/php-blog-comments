<?php

use function App\Helpers\db_query;
use function App\Helpers\get_pdo_connection;

$errors = [];
$comments = [];
$totalComments = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// валидация поискового запроса
	if (mb_strlen($searchTerm) < 3) {
		$errors[] = 'Поисковый запрос должен содержать не менее 3-х символов.';
	} else {
		// запрос к базе данных для поиска комментариев на основе поискового запроса
		$query = "SELECT comments.*, posts.title AS post_title
          FROM comments
          INNER JOIN posts ON comments.post_id = posts.id
          WHERE comments.body LIKE :term";

		$params = [':term' => '%' . $searchTerm . '%'];
		$pdo = get_pdo_connection($_ENV['DB_HOST'], $_ENV['DB_DATABASE'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_PORT'] ?? '3306');
		$comments = db_query($pdo, $query, $params);
	}
}

return [
	'comments' => $comments,
	'totalComments' => count($comments),
	'searchTerm' => $searchTerm,
	'errors' => $errors
];
