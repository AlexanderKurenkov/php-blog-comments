<?php

use function App\Helpers\base_path;

require_once base_path('resources/views/header.view.php');
?>

<div class="container vh-100 d-flex flex-column justify-content-center align-items-center text-center">
	<h1 class="display-3">404</h1>
	<h2 class="mb-4">Страница не найдена</h2>
	<a href="/" class="text-decoration-none mt-3">На главную</a>
</div>

<?php
require_once base_path('resources/views/footer.view.php');
?>