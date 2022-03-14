CURRENT_UID = $(shell id -u)
DOCKER-COMPOSE = docker-compose -f docker-compose-base.yml
LOG = $(DOCKER-COMPOSE) exec -T php ccze -A

.PHONY: build up down restart php redis supervisor-log queue-log queue-log-monitor

build:
	$(DOCKER-COMPOSE) build --build-arg UID=$(CURRENT_UID)

up:
	$(DOCKER-COMPOSE) up -d

down:
	$(DOCKER-COMPOSE) down

restart:
	$(MAKE) down && $(MAKE) up

php:
	$(DOCKER-COMPOSE) exec php bash

redis:
	$(DOCKER-COMPOSE) exec redis redis-cli

supervisor-log:
	$(DOCKER-COMPOSE) exec php tail /var/log/supervisord.log | $(LOG)

queue-log:
	$(DOCKER-COMPOSE) exec -T php tail storage/logs/laravel-queue-worker.log | $(LOG)
	#$(DOCKER-COMPOSE) exec -T php "tail storage/logs/laravel-queue-worker.log | ccze -A"

queue-log-monitor:
	watch -n2 -c -d --no-title make queue-log