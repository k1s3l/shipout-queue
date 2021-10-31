CURRENT_UID = $(shell id -u)
DOCKER = docker-compose -f docker-compose-base.yml

.PHONY: build up down restart php redis supervisor-log queue-log

build:
	$(DOCKER) build --build-arg UID=$(CURRENT_UID)

up:
	$(DOCKER) up -d

down:
	$(DOCKER) down

restart:
	make down && make up

php:
	$(DOCKER) exec php bash

redis:
	$(DOCKER) exec redis redis-cli

supervisor-log:
	$(DOCKER) exec php tail /var/log/supervisord.log | ccze -A

queue-log:
	$(DOCKER) exec  php tail storage/logs/laravel-queue-worker.log | ccze -A
