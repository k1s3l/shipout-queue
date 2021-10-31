CURRENT_UID = $(shell id -u)
DOCKER = docker-compose -f docker-compose-base.yml
LOG = $(DOCKER) exec -T php ccze -A

.PHONY: build up down restart php redis supervisor-log queue-log queue-log-monitor

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
	$(DOCKER) exec php tail /var/log/supervisord.log | $(LOG)

queue-log:
	$(DOCKER) exec -T php tail storage/logs/laravel-queue-worker.log | $(LOG)
	#$(DOCKER) exec -T php "tail storage/logs/laravel-queue-worker.log | ccze -A"

queue-log-monitor:
	watch -n2 -c -d --no-title make queue-log