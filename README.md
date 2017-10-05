Запустить приложение:
========================

1. git clone .....

2. Настроить host

3. Настроить parameters.yml

4. выполнить команду composer install

5. выполнить команду php bin/console doctrine:migrations:execute 20171004085521

6. Добавить новый класс: route '{YOUR ServerName}/app_dev.php/class/new'

Возврат списка классов по api 
========================
$c = curl_init('http://{YOUR ServerName}/app_dev.php/class/api/list/all');

Возврат 1 класса по api 
========================
$c = curl_init('http://{YOUR ServerName}/app_dev.php/class/api/list/{id}');

CRUD 
========================
Список всех классов: {YOUR ServerName}/app_dev.php/class
Добавить новый класс: {YOUR ServerName}/app_dev.php/class/new
Показать класс: {YOUR ServerName}/app_dev.php/class/{id}
Редактировать класс {YOUR ServerName}/app_dev.php/class/{id}/edit

Изменение состояния класса (используется The Workflow Component)
========================
Редактировать класс {YOUR ServerName}/app_dev.php/class/{id}/edit
