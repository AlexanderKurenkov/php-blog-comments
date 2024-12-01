<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
use function App\Helpers\base_path;
use function App\Helpers\load_env_file;
use function App\Helpers\sanitize_input;

// глобальный обработчик исключений
set_exception_handler('App\Helpers\global_exception_handler');

// инициализирует переменные окружения
try {
	load_env_file(__DIR__ . '/../');
} catch (Exception $e) {
	throw $e;
}

// настройка диспетчера маршрутизации
$dispatcher = simpleDispatcher(function (RouteCollector $r) {
	// определение маршрутов
	$r->addRoute('GET', '/', function () {
		require base_path('resources/views/home.view.php');
	});

	$r->addRoute('POST', '/search', function ($vars) {
		$searchTerm = sanitize_input($_POST['query']);

		// передача поискового запроса контроллеру и извлечение данных
		$data = require base_path('app/controllers/search_controller.php');

		// извлечение данных и отображение представления
		extract($data);
		require base_path('resources/views/search.view.php');
	});

	$r->addRoute('GET', '/404', function () {
		http_response_code(404);
		require base_path('resources/views/404.view.php');
	});

	$r->addRoute('GET', '/500', function () {
		http_response_code(500);
		require base_path('/resources/views/500.view.php');
	});
});

// получение HTTP-метода и URI запроса
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];


if (false !== $pos = strpos($uri, '?')) {
	$uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// диспетчеризация URI
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
	case FastRoute\Dispatcher::NOT_FOUND:
		http_response_code(404);
		require base_path('resources/views/404.view.php');
		break;
	case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
		http_response_code(405);
		require base_path('resources/views/404.view.php');
		break;
	case FastRoute\Dispatcher::FOUND:
		$handler = $routeInfo[1];
		$handler($routeInfo[2]);
		break;
}
