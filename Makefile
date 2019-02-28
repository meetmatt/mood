.PHONY: env
env:
	if [[ ! -f .env ]]; then cp .env.dist .env; fi

.PHONY: composer-install
composer-install:
	docker run --rm -i -v "${PWD}":/app -w /app --user $(id -u):$(id -g) composer:1.8.3 install --ignore-platform-reqs --no-scripts

.PHONY: dev
dev:
	docker-compose build && docker-compose up -d

.PHONY: prod
prod:
	docker-compose -f docker-compose.prod.yaml build && docker-compose -f docker-compose.prod.yaml up -d

.PHONY: migration
migration:
	docker-compose exec mysql sh -c 'cat /docker-entrypoint-initdb.d/*.sql | mysql mood'

.PHONY: list-teams
list-teams:
	docker-compose exec mysql sh -c 'echo "SELECT * FROM team" | mysql mood'

