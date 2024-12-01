<?php

use function App\Helpers\base_path;

require_once base_path('resources/views/header.view.php');

?>

<form class="d-flex mb-4" action="/search" method="POST">
	<input class="form-control me-2" type="text" name="query" placeholder="Текст для поиска..." value="" required>
	<button class="btn btn-primary" type="submit">Найти</button>
</form>

<?php
require_once base_path('resources/views/footer.view.php');
?>