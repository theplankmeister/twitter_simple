SHELL := /bin/sh
CONTAINER_NAME_PREFIX := $(shell cat .env | grep CONTAINER_NAME_PREFIX | cut -f2 -d'=')
.PHONY: help
help:
	@echo "$$(grep -hE '^\S+:.*##' $(MAKEFILE_LIST) | sed -e 's/:.*##\s*/:/' | column -c2 -t -s :)"

docker-start: ## start containers
docker-start:
	@docker-compose up --remove-orphans -d

docker-stop: ## stop containers
docker-stop:
	@docker-compose stop

docker-remove: ## remove stopped containers
docker-remove:
	@docker-compose rm -f -s

docker-restart: ## stop and start containers
docker-restart: docker-stop docker-start

docker-rebuild: ## rebuild containers without cache
docker-rebuild:
	@docker-compose build --no-cache

docker-clean: ## Remove all containers, rebuild them, and start them.
docker-clean: docker-remove docker-rebuild docker-start

docker-shell: ## Open a shell session on the PHP container
docker-shell:
	@docker exec -it $(CONTAINER_NAME_PREFIX)_php /bin/sh
