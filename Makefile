start:
	cp .env.example .env
	docker-compose build laravel
	docker-compose run --rm laravel sh -c 'composer install; php artisan key:generate'

up:
	docker-compose up -d laravel
	@if [ -z $(whereis google-chrome) ]; then\
		google-chrome http://localhost:8000/health-check/liveness;\
		google-chrome http://localhost:8000/health-check/readiness;\
	fi

down:
	docker-compose down

test:
	docker-compose run --rm laravel sh -c 'LOG_LEVEL=error composer test'
	@if [ -z $(whereis google-chrome) ]; then\
		google-chrome coverage/index.html;\
	fi
	$(info ===========)
	$(info >>> see coverage in Api and Service folders)
	$(info ===========)
