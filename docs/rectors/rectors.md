---eonx_docs--- title: 'EasyQuality: Rectors' weight: 2001 ---eonx_docs---

##### [\EonX\EasyQuality\Rector\AddCoversAnnotationRector][1]

Adds `@covers` annotation for test classes.

```php
// Before
class SomeServiceTest extends \PHPUnit\Framework\TestCase
{
}
```

```php
// After
/**
 * @covers \SomeService
 */
class SomeServiceTest extends \PHPUnit\Framework\TestCase
{
}
```

**Configuration**

- `replaceArray` - An array of strings that will be cut from the FQCN (Fully Qualified Class Name) when searching for
  the class covered by this test. Default value: `[]`.

##### [\EonX\EasyQuality\Rector\ExplicitBoolCompareRector][2]

Makes bool conditions prettier.

```php
// Before
final class SomeController
{
    public function run($items)
    {
        if (\is_array([]) === true) {
            return 'is array';
        }
    }
}
```

```php
// After
final class SomeController
{
    public function run($items)
    {
        if (\is_array([])) {
            return 'is array';
        }
    }
}
```

##### [\EonX\EasyQuality\Rector\InheritDocRector][3]

Replaces `{@inheritdoc}` annotation with `{@inheritDoc}`.

```php
// Before
/**
 * {@inheritdoc}
 */
public function someMethod(): array
{
}
```

```php
// After
/**
 * {@inheritDoc}
 */
public function someMethod(): array
{
}
```

##### [\EonX\EasyQuality\Rector\PhpDocCommentRector][4]

Applies the company standards to PHPDoc descriptions.

```php
// Before
/**
 * some class
 */
class SomeClass()
{
}
```

```php
// After
/**
 * Some class.
 */
class SomeClass()
{
}
```

##### [\EonX\EasyQuality\Rector\PhpDocReturnForIterableRector][5]

Turns `@return` to `@return iterable<mixed>` in specified classes and methods.

```php
// Before
class SomeEventSubscriber implements EventSubscriberInterface
{
    /**
     * @return mixed[]
     */
    public static function getSubscribedEvents(): iterable
    {
        yield 'event' => 'callback';
    }
}
```

```php
// After
class SomeEventSubscriber implements EventSubscriberInterface
{
    /**
     * @return iterable<mixed>
     */
    public static function getSubscribedEvents(): iterable
    {
        yield 'event' => 'callback';
    }
}
```

##### [\EonX\EasyQuality\Rector\RestoreDefaultNullToNullableTypeParameterRector][6]

Adds default null value to function arguments with PHP 7.1 nullable type.

```php
// Before
class SomeClass
{
    public function __construct(?string $value)
    {
         $this->value = $value;
    }
}
```

```php
// After
class SomeClass
{
    public function __construct(?string $value = null)
    {
         $this->value = $value;
    }
}
```

##### [\EonX\EasyQuality\Rector\ReturnArrayToYieldRector][7]

Turns array return to yield in specified classes and methods.

```php
// Before
class SomeEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return ['event' => 'callback'];
    }
}
```

```php
// After
class SomeEventSubscriber implements EventSubscriberInterface
{
    /**
     * @return iterable<mixed>
     */
    public static function getSubscribedEvents(): iterable
    {
        yield 'event' => 'callback';
    }
}
```

##### [\EonX\EasyQuality\Rector\SingleLineCommentRector][8]

Applies the company standards to single-line comments.

```php
// Before
// some class.
class SomeClass
{
}
```

```php
// After
// Some class
class SomeClass
{
}
```

##### [\EonX\EasyQuality\Rector\StrictInArrayRector][9]

Makes in_array calls strict.

```php
// Before
\in_array($value, $items);
```

```php
// After
\in_array($value, $items, true);
```

##### [\EonX\EasyQuality\Rector\UselessSingleAnnotationRector][10]

Removes PHPDoc completely if it contains only useless single annotation.

```php
// Before
/**
 * {@inheritDoc}
 */
public function someMethod(): array
{
}
```

```php
// After
public function someMethod(): array
{
}
```

[1]: https://github.com/eonx-com/easy-quality/blob/main/src/Rector/AddCoversAnnotationRector.php

[2]: https://github.com/eonx-com/easy-quality/blob/main/src/Rector/ExplicitBoolCompareRector.php

[3]: https://github.com/eonx-com/easy-quality/blob/main/src/Rector/InheritDocRector.php

[4]: https://github.com/eonx-com/easy-quality/blob/main/src/Rector/PhpDocCommentRector.php

[5]: https://github.com/eonx-com/easy-quality/blob/main/src/Rector/PhpDocReturnForIterableRector.php

[6]: https://github.com/eonx-com/easy-quality/blob/main/src/Rector/RestoreDefaultNullToNullableTypeParameterRector.php

[7]: https://github.com/eonx-com/easy-quality/blob/main/src/Rector/ReturnArrayToYieldRector.php

[8]: https://github.com/eonx-com/easy-quality/blob/main/src/Rector/SingleLineCommentRector.php

[9]: https://github.com/eonx-com/easy-quality/blob/main/src/Rector/StrictInArrayRector.php

[10]: https://github.com/eonx-com/easy-quality/blob/main/src/Rector/UselessSingleAnnotationRector.php
