---eonx_docs--- title: 'EasyQuality: Sniffs' weight: 1001 ---eonx_docs---

#### Arrays

##### [\EonX\EasyQuality\Sniffs\Arrays\AlphabeticallySortedArrayKeysSniff][1]

Arrays must be sorted by keys alphabetically.

```php
// Incorrect
$array = ['z' => 1, 'g' => 2, 'a' => 3];
```

```php
// Correct
$array = ['a' => 3, 'g' => 2, 'z' => 1];
```

**Parameters**

- `skipPatterns` - A list of patterns to be checked to skip the array. Specify a token type (e.g. `T_FUNCTION`
  or `T_CLASS`) as a key, and an array of regex patterns as a value to skip an array in the corresponding tokens (
  functions, classes). Default value: `[]`. For example, you can skip all the arrays inside of functions which names
  start with `someFunction`
  or classes which names start with `SomeClass`.

```
[
    T_FUNCTION => ['/^someFunction.*/'],
    T_CLASS => ['/^SomeClass.*/'],
]
```

##### [\EonX\EasyQuality\Sniffs\Attributes\DoctrineColumnTypeSniff][2]

Check the doctrine column type and replace it if required

```php
// Incorrect
class MyClass
{
    #[ORM\Column(type: "string", length: 255)]
    protected string $property1;

    #[ORM\Column(type: "date", length: 255)]
    private Date $property2;

    #[ORM\Column(type: "datetime", length: 255)]
    private DateTime $property3;
}
```

```php
// Correct
class MyClass
{
    #[ORM\Column(type: Types::STRING, length: 255)]
    protected string $property1;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, length: 255)]
    private Date $property2;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, length: 255)]
    private DateTime $property3;
}
```

**Parameters**

- `replacePairs` - array with doctrine column type replace pairs

example:

```
'replacePairs' => [
    'string' => 'Types::STRING',
    'date' => Types::DATE_IMMUTABLE,
    'datetime' => Types::DATETIME_IMMUTABLE,
]
```

##### [\EonX\EasyQuality\Sniffs\Arrays\SortedApiResourceOperationKeysSniff][3]

Api operations must be sorted

###### Sort order:

First, default operations in the following order:

```
get
post
put
patch
delete
```

Second, custom generic operations in the following order:

```
activate
deactivate
```

Third, all the rest operations in the alphabetical order

```php
// Incorrect
#[ApiResource(
    collectionOperations: [
        'activate' => 'activate',
        'put' => 'put',
        'delete' => 'delete',
        'patch' => 'patch',
        'post' => 'post',
        'get' => [
            'security' => "is_granted(...)",
        ],
        'deactivate' => 'deactivate',
        'custom' => 'custom',
        'another-custom' => 'another-custom',
    ]
)
```

```php
// Correct
#[ApiResource(
    collectionOperations: [
        'get' => [
            'security' => "is_granted(...)",
        ],
        'post' => 'post',
        'put' => 'put',
        'patch' => 'patch',
        'delete' => 'delete',
        'activate' => 'activate',
        'deactivate' => 'deactivate',
        'another-custom' => 'another-custom',
        'custom' => 'custom',
    ]
)
```

##### [\EonX\EasyQuality\Sniffs\Classes\AvoidPrivatePropertiesSniff][4]

Class properties must be protected or public.

```php
// Incorrect
class MyClass
{
    private $myProperty1;
    // Or
    $myProperty2;
}
```

```php
// Correct
class MyClass
{
    public $myProperty1;
    // Or
    protected $myProperty2;
}
```

##### [\EonX\EasyQuality\Sniffs\Classes\AvoidPublicPropertiesSniff][5]

Class properties must be protected or private.

```php
// Incorrect
class MyClass
{
    public $myProperty1;
    // Or
    $myProperty2;
}
```

```php
// Correct
class MyClass
{
    private $myProperty1;
    // Or
    protected $myProperty2;
}
```

##### [\EonX\EasyQuality\Sniffs\Classes\MakeClassAbstractSniff][6]

Class must be abstract.

```php
// Incorrect
class MyClass
{
}

final class AnotherClass
{
}
```

```php
// Correct
abstract class MyClass
{
}

abstract class AnotherClass
{
}
```

##### [\EonX\EasyQuality\Sniffs\Classes\PropertyTypeSniff][7]

Check the property type and replace it if required

```php
// Incorrect
class MyClass
{
    public DateTime $myProperty1;
    
    public Carbon $myProperty2;
}
```

```php
// Correct
class MyClass
{
    public DateTimeImmutable $myProperty1;
    
    public CarbonImmutable $myProperty2;
}
```

**Parameters**

- `replacePairs` - array with property type replace pairs

example:

```
'replacePairs' => [
    'DateTime' => 'DateTimeImmutable',
    'Carbon' => 'CarbonImmutable',
]
```

##### [\EonX\EasyQuality\Sniffs\Classes\RequirePublicConstructorSniff][8]

Class constructor must be public.

```php
// Incorrect
class MyClass
{
    protected function __construct()
    {
    }
}
```

```php
// Correct
class MyClass
{
    public function __construct()
    {
    }
}
```

##### [\EonX\EasyQuality\Sniffs\Classes\RequireStrictDeclarationSniff][9]

Strict type declaration is required.

```php
// Incorrect
<?php
// Any php content
```

```php
// Correct
<?php
declare(strict_types=1);

// Any php content
```

##### [\EonX\EasyQuality\Sniffs\Classes\StrictDeclarationFormatSniff][10]

Strict type declaration must be on a new line with no leading whitespace.

```php
// Incorrect
<?php

declare(strict_types=1);
// Any php content
```

```php
// Incorrect
<?php declare(strict_types=1);
// Any php content
```

```php
// Correct
<?php
declare(strict_types=1);

// Any php content
```

#### Commenting

##### [\EonX\EasyQuality\Sniffs\Commenting\AnnotationSortingSniff][11]

Checks that annotations are sorted alphabetically.

```php
// Incorrect
class MyClass
{
    /**
     * @return void
     *
     * @param mixed $withSomething
     */
    public function doSomething($withSomething): void
    {

    }
}
```

```php
// Correct
class MyClass
{
    /**
     * @param mixed $withSomething
     *
     * @return void
     */
    public function doSomething($withSomething): void
    {

    }
}
```

**Parameters**

- `alwaysTopAnnotations` - A list of annotations that should always come first in the list, without regard to sorting. Default value: `[]`.

##### [\EonX\EasyQuality\Sniffs\Commenting\FunctionCommentSniff][12]

Checks that function comment blocks follow EonX standards.

```php
// Incorrect
class MyClass
{
    /**
     * @return void
     *
     * @param mixed $withSomething
     */

    public function doSomethingA($withSomething): void
    {

    }

    /*
     * @return void
     *
     * @param string $withSomething
     */
    public function doSomethingB(string $withSomething): void
    {

    }

    public function doSomethingC(int $withSomething): void
    {

    }

    /**
     * Do something.
     *
     * @return void
     */
    public function doSomethingD(bool $withSomething): void
    {

    }
}
```

```php
// Incorrect
class MyClass
{
    /**
     * Do something.
     *
     * @param mixed $withSomething
     *
     * @return void
     */
    public function doSomethingA($withSomething): void
    {

    }

    /**
     * Do something.
     *
     * @param string $withSomething
     *
     * @return void
     */
    public function doSomethingB(string $withSomething): void
    {

    }

    /**
     * Do something.
     *
     * @param int $withSomething
     *
     * @return void
     */
    public function doSomethingC(int $withSomething): void
    {

    }

    /**
     * Do something.
     *
     * @param bool $withSomething
     *
     * @return void
     */
    public function doSomethingD(bool $withSomething): void
    {

    }
}
```

#### Control Structures

##### [\EonX\EasyQuality\Sniffs\ControlStructures\ArrangeActAssertSniff][13]

Checks that a test method conforms to Arrange, Act and Assert (AAA) pattern. The allowed number of empty lines is between [1, 2].

```php
// Incorrect
final class TestClass
{
    public function testSomethingA()
    {
        $expectedResult = 4;
        $array = [
            'key' => 'value',
        ];
        $actualResult = 2 + 2;
        self::assertSame($expectedResult, $actualResult);
        self::assertSame(['key' => 'value'], $array);
    }

    public function testSomethingB()
    {
        $expectedResult = 4;
        $actualResult = 2 + 2;
        self::assertSame($expectedResult, $actualResult);
    }
}
```

```php
// Correct
final class TestClass
{
    public function testSomethingA()
    {
        $expectedResult = 4;
        $array = [
            'key' => 'value',
        ];

        $actualResult = 2 + 2;

        self::assertSame($expectedResult, $actualResult);
        self::assertSame(['key' => 'value'], $array);
    }

    public function testSomethingB()
    {
        $actualResult = 2 + 2;

        self::assertSame(4, $actualResult);
    }

    public function testSomethingC()
    {
        self::assertSame(4, 2 + 2);
    }

    // Allow empty line in closure
    public function testSomethingD()
    {
        $value1 = 2;
        $value2 = 2;
        $expectedClosure = static function () use ($value1, $value2): int {
            $result = $value1 + $value2;

            return $result + 0;
        };

        $actualResult = 2 + 2;

        self::assertSame($expectedClosure(), $actualResult);
    }

    public function noTestMethod()
    {
        $expectedResult = 4;
        $actualResult = 2 + 2;
        self::assertSame($expectedResult, $actualResult);
    }
}
```

**Parameters**

- `testMethodPrefix` - If a method name starts with this prefix, checks will be applied to it. Default value: `test`.
- `testNamespace` - If a class namespace starts with this prefix, the class will be parsed. Default value: `App\Tests`.

```php
// Correct
namespace App\NoTestNamespace;

final class TestClass
{
    public function testSomething()
    {
        $expectedResult = 4;

        $actualResult = 2 + 2;

        self::assertSame($expectedResult, $actualResult);

        echo $actualResult;
    }
}
```

##### [\EonX\EasyQuality\Sniffs\ControlStructures\NoNotOperatorSniff][14]

A strict comparison operator must be used instead of a NOT operator.

```php
// Incorrect
$a = (bool)\random_int(0, 1);
if (!$a) {
    // Do something.
}
````

```php
// Correct
$a = (bool)\random_int(0, 1);
if ($a === false) {
    // Do something.
}
````

##### [\EonX\EasyQuality\Sniffs\ControlStructures\UseYieldInsteadOfReturnSniff][15]

Checks that `yield` is used instead of `return` in specified classes and methods.

**Parameters**

- `applyTo` - An array of regular expressions to match the class namespace and method name. Example:

```
[
    [
        'namespace' => '/^App\\\Tests/',
        'patterns' => ['/provide[A-Z]*/'],
    ]
]
```

#### Exceptions

##### [\EonX\EasyQuality\Sniffs\Exceptions\ThrowExceptionMessageSniff][16]

Exception message must be either a variable or a translation message, starting with a valid prefix.

```php
// Incorrect
throw new \Exception('Incorrect message');
````

```php
// Correct
throw new NotFoundHttpException();
// Or
$exception = new Exception('Some exception message');
throw $exception;
// Or
throw new InvalidArgumentException('exceptions.some_message');
// Or
$message = 'Some exception message';
throw new RuntimeException($message);
````

**Parameters**

- `validPrefixes` - An array of prefixes that are valid for starting the message text. Default value: `['exceptions.']`.

#### Functions

##### [\EonX\EasyQuality\Sniffs\Functions\DisallowNonNullDefaultValueSniff][17]

Function and closure parameters can only have a default value of `null`.

```php
// Incorrect
function someFunction(int $param1, ?stdClass $class, string $const3 = TestClass::TEST, array $param4 = []) {
    // No body needed
}
````

```php
// Correct
function someFunction(int $param1, ?stdClass $class = null, ?string $const3 = null, ?array $param4 = null) {
    // No body needed
}
````

#### Methods

##### [\EonX\EasyQuality\Sniffs\Methods\TestMethodNameSniff][18]

Checks that a method name matches/does not match a specific regex.

**Parameters**

- `allowed` - An array of regular expressions to match method names. Default value:

```
[
    [
        'namespace' => '/^App\\\Tests\\\Unit/',
        'patterns' => ['/test[A-Z]/'],
    ],
]
```

- `forbidden` - An array of regular expressions that method names should not match. Default value:

```
[
    [
        'namespace' => '/^App\\\Tests\\\Unit/',
        'patterns' => ['/(Succeed|Return|Throw)[^s]/'],
    ],
]
```

#### Namespaces

##### [\EonX\EasyQuality\Sniffs\Namespaces\Psr4Sniff][19]

Checks that a namespace name matches PSR-4 project structure.

**Parameters**

- `composerJsonPath` - A relative path to the project file `composer.json`. Default value: `composer.json`.

[1]: https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Arrays/AlphabeticallySortedArrayKeysSniff.php

[2]: https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Attributes/DoctrineColumnTypeSniff.php

[3]: https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Attributes/SortedApiResourceOperationKeysSniff.php

[4]: https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Classes/AvoidPrivatePropertiesSniff.php

[5]: https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Classes/AvoidPublicPropertiesSniff.php

[6]: https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Classes/MakeClassAbstractSniff.php

[7]: https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Classes/PropertyTypeSniff.php

[8]: https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Classes/RequirePublicConstructorSniff.php

[9]: https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Classes/RequireStrictDeclarationSniff.php

[10]: https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Classes/StrictDeclarationFormatSniff.php

[11]: https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Commenting/AnnotationSortingSniff.php

[12]: https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Commenting/FunctionCommentSniff.php

[13]: https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/ControlStructures/ArrangeActAssertSniff.php

[14]: https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/ControlStructures/NoNotOperatorSniff.php

[15]: https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/ControlStructures/UseYieldInsteadOfReturnSniff.php

[16]: https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Exceptions/ThrowExceptionMessageSniff.php

[17]: https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Functions/DisallowNonNullDefaultValueSniff.php

[18]: https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Methods/TestMethodNameSniff.php

[19]: https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Namespaces/Psr4Sniff.php
