tests: phpspec behat

release:
	./vendor/bin/RMT release

phpspec:
	php ./vendor/bin/phpunit

behat:
	php ./vendor/bin/behat --colors -vvv

docker_start:
	docker run -d -p 5432:5432 -v $(shell pwd):/tmp/rulerz --name pg-rulerz postgres:9.4

docker_stop:
	docker rm -f pg-rulerz

database:
	./examples/scripts/create_database.sh