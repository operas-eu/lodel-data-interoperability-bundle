# Testing

This bundle includes a basic test suite to ensure that core functionality works as expected.

The tests are located in the ```tests/``` directory. To run the tests with PHPUnit, execute the following command:

```bash
$ ./vendor/bin/phpunit
```

If you want to generate a code coverage report, you can use the provided Makefile:

```bash
$ make tests
```

This will execute PHPUnit tests and generate an HTML report of the code coverage, which will be saved in the ```build/coverage-html``` directory. You can access the coverage report by opening ```build/coverage-html/index.html``` in your browser.
