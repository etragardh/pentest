## Minify

This is a modified copy of [Minify](https://github.com/matthiasmullie/minify) that adds a few changes to CSS minification for our needs.

The author is unresponsive on the original repo, so we've copied it here.

### Testing

Please include tests for every change or addition to the code.
To run the complete test suite:

```sh
vendor/bin/phpunit
```

When submitting a new pull request, please make sure that that the test suite
passes (Travis CI will run it & report back on your pull request.)

To run the tests on Windows, run `tests/convert_symlinks_to_windows_style.sh`
from the command line in order to convert Linux-style test symlinks to
Windows-style.

### Coding standards

All code must follow [PSR-2](http://www.php-fig.org/psr/psr-2/). Just make sure
to run php-cs-fixer before submitting the code, it'll take care of the
formatting for you:

```sh
vendor/bin/php-cs-fixer fix src
vendor/bin/php-cs-fixer fix tests
```

Document the code thoroughly!

## License

Note that minify is MIT-licensed, which basically allows anyone to do
anything they like with it, without restriction.
