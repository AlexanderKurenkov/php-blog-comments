<?php

declare(strict_types=1);

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use function App\Helpers\load_env_file;
use function App\Helpers\fetch_data;
use function App\Helpers\get_pdo_connection;

const POSTS_URL = 'https://jsonplaceholder.typicode.com/posts';
const COMMENTS_URL = 'https://jsonplaceholder.typicode.com/comments';

// выполняет подготовленный оператор
function execute_batch($pdo, $query, $dataSet): int
{
	// число добавленных записей
	$count = 0;

	$stmt = $pdo->prepare($query);

	foreach ($dataSet as $data) {
		try {
			$stmt->execute($data);
			++$count;
		} catch (PDOException $e) {
			echo "Ошибка при выполнении запроса: " . $e->getMessage() . PHP_EOL;
		}
	}

	return $count;
}

function main()
{
	// инициализирует переменные окружения
	try {
		load_env_file(__DIR__ . '/../');
	} catch (Exception $e) {
		throw $e;
	}

	// HTTP-запрос для получения данных
	$posts = json_decode(fetch_data(POSTS_URL));
	$comments = json_decode(fetch_data(COMMENTS_URL));

	try {
		$pdo = get_pdo_connection($_ENV['DB_HOST'], $_ENV['DB_DATABASE'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_PORT'] ?? '3306');

		// вставляет записи постов в базу данных
		$postQuery = "INSERT INTO posts (id, title, body, user_id) VALUES (:id, :title, :body, :user_id)";
		$postData = array_map(fn($post) => [
			':id' => $post->id,
			':title' => $post->title,
			':body' => $post->body,
			':user_id' => $post->userId
		], $posts);
		$numPosts = execute_batch($pdo, $postQuery, $postData);

		// вставляет записи комментариев в базу данных
		$commentQuery = "INSERT INTO comments (id, name, email, body, post_id) VALUES (:id, :name, :email, :body, :post_id)";
		$commentData = array_map(fn($comment) => [
			':id' => $comment->id,
			':name' => $comment->name,
			':email' => $comment->email,
			':body' => $comment->body,
			':post_id' => $comment->postId
		], $comments);

		$numComments = execute_batch($pdo, $commentQuery, $commentData);

		echo PHP_EOL . "----------------------------------------------------------------------" . PHP_EOL;
		echo "Добавлено в базу данных $numPosts записей и $numComments комментариев";
		echo PHP_EOL . "----------------------------------------------------------------------" . PHP_EOL;
	} catch (PDOException $e) {
		echo "Ошибка при выполнении запроса: " . $e->getMessage() . PHP_EOL;
	}
}

main();
