<?php

declare(strict_types=1);

namespace App\Helpers;

use Dotenv\Dotenv;

// глобальный обработчик исключений
function global_exception_handler(\Throwable $exception): void
{
	if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'local') {
		echo '<pre>';
		echo 'Uncaught Exception: ' . $exception->getMessage() . PHP_EOL;
		echo 'Stack trace: ' . $exception->getTraceAsString();
		echo '</pre>';
	}

	http_response_code(500);
	require_once __DIR__ . '/../../resources/views/500.view.php';
	exit;
}

// возвращает абсолютный путь к указанному каталогу относительно базового каталога приложения
function base_path($path = ''): string
{
	return realpath(__DIR__ . '/../../' . $path);
}

// возвращает полный URL-путь к ресурсам
function asset(string $path): string
{
	$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

	return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

// загрузка переменных окружения
function load_env_file(string $envPath)
{
	try {
		Dotenv::createImmutable($envPath)->load();
	} catch (\Exception $e) {
		throw new \Exception("Ошибка при загрузке файла .env: " . $e->getMessage());
	}

	// валидация переменных окружения
	if (!$_ENV['DB_HOST'] || !$_ENV['DB_DATABASE'] || !$_ENV['DB_USERNAME']) {
		throw new \Exception("Отсутствует одна или несколько переменных конфигурации базы данных в файле .env.");
	}
}

// получение данных HTTP-запроса
function fetch_data(string $url): string
{
	$ch = curl_init($url);

	if ($ch === false) {
		die('Failed to initialize cURL');
	}

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// отключить проверку SSL-сертификата
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	$response = curl_exec($ch);

	if ($response === false) {
		$error = curl_error($ch);
		$errorCode = curl_errno($ch);
		curl_close($ch);
		die("cURL error ({$errorCode}): {$error}");
	}

	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	if ($httpCode >= 400) {
		die("HTTP error: Received response code $httpCode");
	}

	return $response;
}

function get_pdo_connection(string $host, string $database, string $username, string $password, string $port): \PDO
{
	$dsn = "mysql:host=$host;dbname=$database;port=$port";

	return new \PDO($dsn, $username, $password, [
		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
	]);
}


function random_string(int $length): string
{
	return bin2hex(random_bytes($length / 2));
}

function sanitize_input(string $input): string
{
	return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

function paginate(int $totalItems, int $itemsPerPage, int $currentPage): array
{
	$totalPages = ceil($totalItems / $itemsPerPage);
	$offset = ($currentPage - 1) * $itemsPerPage;

	return ['offset' => $offset, 'totalPages' => $totalPages];
}

function db_query($pdo, $query, $params = [])
{
	try {
		$stmt = $pdo->prepare($query);

		foreach ($params as $key => $value) {
			$stmt->bindValue($key, $value);
		}

		$stmt->execute();

		if (strpos(trim(strtoupper($query)), 'SELECT') === 0) {
			return $stmt->fetchAll();
		}

		return $stmt->rowCount();
	} catch (\PDOException $e) {
		die("Error performing SQL query: " . $e->getMessage());
	}
}
