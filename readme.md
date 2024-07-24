---eonx_docs--- title: Introduction weight: 0 ---eonx_docs---

This package is a way to centralise reusable classes used for coding standards and quality tools. It contains:

- [Rectors][2]
- [Sniffs][3]

<br>

### Install (separately from the project's composer.json file)

1. Create a `quality` directory in your project root.
2. Go to the `quality` directory and run `composer require eonx-com/easy-quality`.
3. Set env variables to speed up local execution:
    - `EONX_EASY_QUALITY_JOB_SIZE` - number of files to process in parallel (default: 2)
    - `EONX_EASY_QUALITY_MAX_NUMBER_OF_PROCESS` - maximum number of parallel processes (default: 4)
    - `EONX_EASY_QUALITY_TIMEOUT_SECONDS` - timeout in seconds for each process (default: 120)
4. Add `quality/vendor` to Git ignore (either in a `.gitignore` file inside the `quality` directory or in you project's
   root `.gitignore` file).
5. Update your project's `composer.json` file by adding a post-install script (this will automate an installation
   of `eonx-com/easy-quality` on all the local machines):

    ```json
    {
        "post-install-cmd": [
            "cd quality && composer install --no-dev"
        ]
    }
    ```

6. Update your project's `composer.json` file by adding the following scripts. Here we
   use `veewee/composer-run-parallel` (install it as a **dev** dependency) for the `check-all` script to run multiple
   commands in parallel. Feel free to modiy these
   commands as you wish.

    ```json
    {
        "scripts": {
            "check-all": "@parallel check-security check-ecs check-rector check-phpmd-app check-phpmd-tests check-phpstan",
            "check-ecs": "php -d memory_limit=1024M quality/vendor/bin/ecs check --clear-cache",
            "check-phpmd-app": "quality/vendor/bin/phpmd src ansi phpmd.app.xml",
            "check-phpmd-tests": "quality/vendor/bin/phpmd tests ansi phpmd.tests.xml",
            "check-phpstan": "quality/vendor/bin/phpstan analyse --ansi --memory-limit=1000M",
            "check-rector": "quality/vendor/bin/rector process --dry-run"
        }
    }
    ```

7. Make sure you have config files for ECS, Rector, PHP Mess Detector, and PHPStan in the project source code root.
8. Run `composer check-all` from the project source code root to make sure everything is working and fix the found
   issues.
9. If you want to use the quality tools in CI, here is an example of a GitHub action configuration:

    ```yaml
        coding-standards:
            needs: composer
            runs-on: ubuntu-latest
            timeout-minutes: 60
            strategy:
                fail-fast: false
                matrix:
                    php: ['8.2']
                    actions:
                        - {name: phpstan, run: composer check-phpstan}
                        - {name: phpmd-app, run: composer check-phpmd-app}
                        - {name: phpmd-tests, run: composer check-phpmd-tests}
                        - {name: rector, run: composer check-rector}
                        - {name: security, run: composer check-security}
                        - {name: yaml-linter, run: './bin/console lint:yaml config src translations --parse-tags'}
                        - {name: ecs, run: composer check-ecs}
            env:
                EONX_EASY_QUALITY_JOB_SIZE: 20
                EONX_EASY_QUALITY_MAX_NUMBER_OF_PROCESS: 32
                EONX_EASY_QUALITY_TIMEOUT_SECONDS: 120
    
            name: ${{ matrix.actions.name}} (${{ matrix.php }})
    
            steps:
                -
                    uses: actions/checkout@v4
    
                -
                    uses: shivammathur/setup-php@v2
                    with:
                        php-version: ${{ matrix.php }}
                        coverage: none
    
                -
                    name: Get the cached Composer dependencies
                    uses: actions/cache@v4
                    with:
                        path: src/vendor
                        key: composer-${{ hashFiles('src/composer.lock') }}
    
                -
                    name: Get the cached quality tools installation
                    id: cache-quality-tools
                    uses: actions/cache@v4
                    with:
                        path: quality/vendor
                        key: quality-tools-composer-${{ hashFiles('quality/composer.lock') }}
    
                -
                    name: Install quality tools
                    if: steps.cache-quality-tools.outputs.cache-hit == false
                    run: composer --working-dir=../quality install --prefer-dist --no-scripts --no-progress --no-interaction --no-dev
    
                -
                    name: Check ${{ matrix.actions.name }}
                    run: ${{ matrix.actions.run }}
                    shell: bash
    ```

### Prepare configuration file for ECS (Easy Coding Standard) Sniffs

Create a configuration file for ECS in the `quality` folder of the project.

For example see [quality/ecs.php](quality/ecs.php).

### Run ECS check

Go to the root folder of the project and run

```shell
composer check-ecs
```

or

```shell
quality/vendor/bin/ecs check
```

Expected output:

```
[OK] No errors found. Great job - your code is shiny in style!
```

### Prepare configuration file for Rector

Create a configuration file for Rector in the `quality` folder of the project.

For example see [quality/rector.php](quality/rector.php).

### Run Rector check

Go to the root folder of the project and run

```shell
composer check-rector
```

or

```shell
quality/vendor/bin/rector process --dry-run
```

Expected output:

```
[OK] Rector is done!
```

[1]: https://getcomposer.org/

[2]: https://github.com/rectorphp/rector

[3]: https://github.com/squizlabs/PHP_CodeSniffer
