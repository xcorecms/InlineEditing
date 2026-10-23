default: cs phpstan test

cs:
	vendor/bin/phpcs --standard=PSR12 ./src ./tests

phpstan:
	vendor/bin/phpstan

test:
	vendor/bin/tester ./tests

init:
	psql -c 'CREATE DATABASE inline;' -U root postgres
	psql inline < tests/sql/postgres.sql
	mysql -e "CREATE DATABASE inline;" -u root
	mysql -u root inline < tests/sql/mysql.sql
	mysql -e 'SET PASSWORD FOR "root"@"localhost" = PASSWORD("");'
