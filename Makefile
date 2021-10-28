CURRENT_UID =$(shell id -u)

.PHONY: build up down restart php redis supervisor-log queue-log

build:
	docker-compose -f docker-compose-base.yml build --build-arg UID=$(CURRENT_UID)

up:
	docker-compose -f docker-compose-base.yml up -d

down:
	docker-compose -f docker-compose-base.yml down

restart:
	make down && make up

php:
	docker-compose -f docker-compose-base.yml exec php bash

redis:
	docker-compose -f docker-compose-base.yml exec redis redis-cli

supervisor-log:
	docker-compose -f docker-compose-base.yml exec tail /var/log/supervisord.log | ccze -A

queue-log:
	docker-compose -f docker-compose-base.yml exec tail storage/logs/laravel-queue-worker.log | ccze -A
