tests: phpspec

release:
	./vendor/bin/RMT release

phpspec:
	php ./vendor/bin/phpunit
