Запуск в Docker-окружении:
* $ git clone https://github.com/AlexanderKurenkov/php-blog-comments.git
* $ cd ./php-blog-comments
* создать файл .env на основе .env.example и указать нужные учетные данные пользователя:
	- $ mv .env.example .env
* $ docker-compose up -d
* базовый URL для доступа к приложению:
	- http://localhost:8000/


Примечание:
* /database/seeder.php:
	- скрипт для получения списка записей и комментариев и их загрузки в БД
* /database/schema.sql:
	- создание схемы базы данных
* /public/index.php:
	- главный файл веб-приложения