quality:
	vendor/bin/php-cs-fixer fix
	vendor/bin/phpstan analyse
.PHONY: quality

security:
	symfony security:check --no-interaction
.PHONY: security
