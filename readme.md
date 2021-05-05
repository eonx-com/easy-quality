---eonx_docs--- title: Introduction weight: 0 ---eonx_docs---

This package is a way to centralise reusable classes used for coding standards. It contains:

- [Rectors][2]
- [Sniffs][3]

<br>
### Prepare configuration file for ECS (Easy Coding Standard) Sniffs

You can use one of the following names for a configuration file: `ecs.php` or `easy-coding-standard.php`. Create this
file in the root folder of the project.

The basic structure of the configuration file follows:

```php
// ecs.php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    /*
     * List of parameters
     */

    $services = $containerConfigurator->services();
    /*
     * List of services
     */
};
```

### Run ECS check

Go to the root folder of the project and run

```bash
quality/vendor/bin/ecs check
```

Expected output:

```
[OK] No errors found. Great job - your code is shiny in style!
```

### Prepare configuration file for Rector

Create `rector.php` in the root folder of the project.

The basic structure of the configuration file follows:

```php
// rector.php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    /*
     * List of parameters
     */

    $services = $containerConfigurator->services();
    /*
     * List of services
     */
};
```

### Run Rector check

Go to the root folder of the project and run

```bash
quality/vendor/bin/rector process --dry-run
```

Expected output:

```
[OK] Rector is done!
```

[1]: https://getcomposer.org/

[2]: https://github.com/rectorphp/rector

[3]: https://github.com/squizlabs/PHP_CodeSniffer
