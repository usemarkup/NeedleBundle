.PHONY: all
default: all;

analysis:
	vendor/bin/phpstan.phar analyse -c phpstan.neon ./src --level 7

unit:
	vendor/bin/phpunit --no-coverage

standards:
	vendor/bin/phpcs -p --colors --standard=ruleset.xml ./src

mess:
	vendor/bin/phpmd ./src text phpmd.xml

all: analysis unit standards mess
