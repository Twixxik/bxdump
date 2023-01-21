# Bitrix Dump

Простые скрипты для создания дампа базы данных и ядра Битрикс.

## Установка

Клонируем gist в домашнюю директорию пользователя на сервере

```bash
git clone https://gist.github.com/8b57c72b26c5c6e83e66f89a6da62cb1.git ~/bxdump
```

## Дамп базы данных mysql

Переходим в директорию с проектом (там где лежит `/bitrix`) и выполняем скрипт:

```bash
php -f ~/bxdump/bxdump-db.php
```

Скрипт создает дамп структуры всех таблиц и данные всех таблиц, кроме почтовых событий, сообщений мессенджера и монитора производительности.
В результате появится файл `~/bxdump/bxdump-db.tar.gz`

### Восстановление базы из дампа

Скачиваем дамп из директории `~/bxdump/bxdump-db.tar.gz` (подставляем нужный `[servername]` и путь до директории пользователя):

```bash
scp [servername]:/home/bitrix/bxdump/bxdump-db.tar.gz .
```

Распаковываем:

```bash
tar -xzf bxdump-db.tar.gz
```

Применяем файлы по очереди (подставляем параметры подключения к базе вместо переменных окружения если они отсутствуют):

```bash
mysql -h$DB_HOST -P$DB_PORT -u$DB_USER -p$DB_PASSWORD $DB_NAME < 10-structure.sql
mysql -h$DB_HOST -P$DB_PORT -u$DB_USER -p$DB_PASSWORD $DB_NAME < 20-data.sql
```

## Дамп ядра Битрикс

Переходим в директорию с проектом (там где лежит `/bitrix`, НЕ симлинк) и выполняем скрипт:

```bash
bash ~/bxdump/bxdump-core.sh
```

Скрипт создает дамп директории `bitrix`, без директорий с кэшем, бекапов и файлов логов.
В результате появится файл `~/bxdump/bxdump-core.tar.gz`

## Дамп директории загрузок Битрикс

Переходим в директорию с проектом (там где лежит `/upload`, НЕ симлинк) и выполняем скрипт:

```bash
bash ~/bxdump/bxdump-upload.sh
```

Скрипт создает дамп с файлами из директории `upload`, чей размер не превышает 10M.
В результате появится файл `~/bxdump/bxdump-upload.tar.gz`