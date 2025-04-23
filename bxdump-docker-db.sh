#!/bin/bash
export $(cat ./.env | grep -v ^# | xargs) >/dev/null

# Первая команда: экспорт структуры
docker compose exec -itu 0 $DB_HOST mysqldump -h localhost -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" \
  --no-tablespaces --no-data --routines --events > ~/bxdump/10-structure.sql
if [ $? -ne 0 ]; then
  echo "Ошибка при экспорте структуры базы данных"
  exit 1
fi

# Вторая команда: экспорт данных с исключениями
docker compose exec -itu 0 $DB_HOST mysqldump -h localhost -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" \
  --no-tablespaces --no-create-info \
  --ignore-table="$DB_NAME.b_event" \
  --ignore-table="$DB_NAME.b_event_log" \
  --ignore-table="$DB_NAME.b_messageservice_message" \
  --ignore-table="$DB_NAME.b_im_message" \
  --ignore-table="$DB_NAME.b_im_message_param" \
  --ignore-table="$DB_NAME.b_im_message_favorite" \
  --ignore-table="$DB_NAME.b_stat_hit" \
  --ignore-table="$DB_NAME.b_stat_page" \
  --ignore-table="$DB_NAME.b_stat_path" \
  --ignore-table="$DB_NAME.b_stat_guest" \
  --ignore-table="$DB_NAME.b_stat_session" \
  --ignore-table="$DB_NAME.b_stat_path_cache" \
  --ignore-table="$DB_NAME.b_stat_referer_list" \
  --ignore-table="$DB_NAME.b_stat_searcher_hit" \
  --ignore-table="$DB_NAME.b_perf_sql" \
  > ~/bxdump/20-data.sql
if [ $? -ne 0 ]; then
  echo "Ошибка при экспорте данных базы данных"
  exit 1
fi

# Третья команда: архивация
cd ~/bxdump && tar -czf ./bxdump-db.tar.gz ./10-structure.sql ./20-data.sql
if [ $? -ne 0 ]; then
  echo "Ошибка при создании архива"
  exit 1
fi

echo "Бэкап успешно создан: ~/bxdump/bxdump-db.tar.gz"

