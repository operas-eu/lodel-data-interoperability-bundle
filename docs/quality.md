# Quality Checks

This document explains the process and tools used to check the code quality of the project.

## YAML Lint
[YAML Lint](https://symfony.com/doc/current/components/yaml.html) is used to validate the syntax of YAML configuration files. It ensures that all `.yaml` files are correctly formatted and free of syntax errors.

## PHP-CS-Fixer
[PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) is used to automatically fix coding standard violations according to the project's configuration.

## PHPStan
[PHPStan](https://phpstan.org/) performs static analysis of the code, identifying potential errors, bugs, and issues related to types and code quality.

## Running Quality Checks

To run the quality checks, execute the following command:

```bash
$ make quality
```
