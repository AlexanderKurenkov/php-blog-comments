<?php

use function App\Helpers\base_path;
use function App\Helpers\sanitize_input;
use function App\Helpers\paginate;

require_once base_path('resources/views/header.view.php');

?>

<form class="d-flex mb-4" action="/search" method="POST">
	<input class="form-control me-2" type="text" name="query" placeholder="Текст для поиска..." value="<?= sanitize_input($searchTerm) ?>" required>
	<button class="btn btn-primary" type="submit">Найти</button>
</form>

<?php if (!empty($errors)): ?>
	<?php foreach ($errors as $error): ?>
		<div class="alert alert-danger"><?= sanitize_input($error) ?></div>
	<?php endforeach; ?>
<?php elseif ($query): ?>
	<?php if ($totalComments == 0): ?>
		<div class="alert alert-warning">Совпадений не найдено.</div>
	<?php else: ?>
		<h4>Всего результатов: <?= sanitize_input($totalComments) ?></h4>
		<div class="list-group">
			<?php foreach ($comments as $comment): ?>
				<div class="list-group-item">
					<h5>Заголовок записи: <?= sanitize_input($comment['post_title']) ?></h5>
					<p>Комментарий: <?= sanitize_input($comment['body']) ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
<?php endif; ?>

<?php
require_once base_path('resources/views/footer.view.php');
?>