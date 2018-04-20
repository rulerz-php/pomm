tests: phpspec

release:
	./vendor/bin/RMT release

phpspec:
	php ./vendor/bin/phpspec run --ansi  -vvv
