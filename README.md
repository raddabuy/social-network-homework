В результате выполнения ДЗ создан базовый скелет социальной сети, который будет развиваться в дальнейших ДЗ.
Проект выполнен на читом php без использовани фреймворков, использована БД postgres.

Инструкция дляразвертывания проекта:
1. В корне проекта выполнить команду docker compose build (или docker-compose в зависимости от установленной версии сompose).
2. Запустить контейнеры docker compose up -d
3. Выполнить sql файл. Для этого нужно:
     3.1. docker compose cp ./sql/create_users_table.sql postgres:/docker-entrypoint-initdb.d/create_users_table.sql (копируем файл в контейнер)
     3.2. docker compose exec -u root postgres psql admin root -f docker-entrypoint-initdb.d/create_test.sql (выполняем sql команду)
   В итоге создается таблица users
5. Установить необходимые пакеты. Для этого нужно:
    4.1. Зайти в php контейнер командой docker compose exec php bash
    4.2. В bash выполнить сcomposer install

Всё, проект готов к работе.
К домашнему заданию приложена коллекция из Postman. В ней содержится 3 запроса:
/login
/user/register
/user/get/{id}
