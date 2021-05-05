---eonx_docs---
title: 'EasyStandard: Sniffs'
weight: 1001
---eonx_docs---

#### Arrays

##### [\EonX\EasyStandard\Sniffs\Arrays\AlphabeticallySortedArrayKeysSniff][1]

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

- `skipPatterns` - A list of patterns to be checked to skip the array.
  Specify a token type (e.g. `T_FUNCTION` or `T_CLASS`) as a key, and an array of regex patterns as a value
  to skip an array in the corresponding tokens (functions, classes). Default value: `[]`.
  For example, you can skip all the arrays inside of functions which names start with `someFunction`
  or classes which names start with `SomeClass`.
```
[
    T_FUNCTION => ['/^someFunction.*/'],
    T_CLASS => ['/^SomeClass.*/'],
]
```

##### [\EonX\EasyStandard\Sniffs\Classes\AvoidPrivatePropertiesSniff][2]

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

##### [\EonX\EasyStandard\Sniffs\Classes\AvoidPublicPropertiesSniff][3]

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

##### [\EonX\EasyStandard\Sniffs\Classes\RequirePublicConstructorSniff][4]

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

##### [\EonX\EasyStandard\Sniffs\Classes\RequireStrictDeclarationSniff][5]

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

##### [\EonX\EasyStandard\Sniffs\Classes\StrictDeclarationFormatSniff][6]

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

##### [\EonX\EasyStandard\Sniffs\Commenting\AnnotationSortingSniff][7]

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

- `alwaysTopAnnotations` - A list of annotations that should always come first in the list, without regard to sorting.
  Default value: `[]`.

##### [\EonX\EasyStandard\Sniffs\Commenting\FunctionCommentSniff][8]

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

##### [\EonX\EasyStandard\Sniffs\ControlStructures\ArrangeActAssertSniff][9]

Checks that a test method conforms to Arrange, Act and Assert (AAA) pattern. The allowed number of empty lines is
between [1, 2].

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

##### [\EonX\EasyStandard\Sniffs\ControlStructures\NoNotOperatorSniff][10]

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

#### Exceptions

##### [\EonX\EasyStandard\Sniffs\Exceptions\ThrowExceptionMessageSniff][11]

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

##### [\EonX\EasyStandard\Sniffs\Functions\DisallowNonNullDefaultValueSniff][12]

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

##### [\EonX\EasyStandard\Sniffs\Methods\TestMethodNameSniff][13]

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

##### [\EonX\EasyStandard\Sniffs\Namespaces\Psr4Sniff][14]

Checks that a namespace name matches PSR-4 project structure.

**Parameters**

- `composerJsonPath` - A relative path to the project file `composer.json`. Default value: `composer.json`.

[1]: https://github.com/eonx-com/easy-monorepo/blob/master/packages/EasyStandard/src/Sniffs/Arrays/AlphabeticallySortedArrayKeysSniff.php
[2]: https://github.com/eonx-com/easy-monorepo/blob/master/packages/EasyStandard/src/Sniffs/Classes/AvoidPrivatePropertiesSniff.php
[3]: https://github.com/eonx-com/easy-monorepo/blob/master/packages/EasyStandard/src/Sniffs/Classes/AvoidPublicPropertiesSniff.php
[4]: https://github.com/eonx-com/easy-monorepo/blob/master/packages/EasyStandard/src/Sniffs/Classes/RequirePublicConstructorSniff.php
[5]: https://github.com/eonx-com/easy-monorepo/blob/master/packages/EasyStandard/src/Sniffs/Classes/RequireStrictDeclarationSniff.php
[6]: https://github.com/eonx-com/easy-monorepo/blob/master/packages/EasyStandard/src/Sniffs/Classes/StrictDeclarationFormatSniff.php
[7]: https://github.com/eonx-com/easy-monorepo/blob/master/packages/EasyStandard/src/Sniffs/Commenting/AnnotationSortingSniff.php
[8]: https://github.com/eonx-com/easy-monorepo/blob/master/packages/EasyStandard/src/Sniffs/Commenting/FunctionCommentSniff.php
[9]: https://github.com/eonx-com/easy-monorepo/blob/master/packages/EasyStandard/src/Sniffs/ControlStructures/ArrangeActAssertSniff.php
[10]: https://github.com/eonx-com/easy-monorepo/blob/master/packages/EasyStandard/src/Sniffs/ControlStructures/NoNotOperatorSniff.php
[11]: https://github.com/eonx-com/easy-monorepo/blob/master/packages/EasyStandard/src/Sniffs/Exceptions/ThrowExceptionMessageSniff.php
[12]: https://github.com/eonx-com/easy-monorepo/blob/master/packages/EasyStandard/src/Sniffs/Functions/DisallowNonNullDefaultValueSniff.php
[13]: https://github.com/eonx-com/easy-monorepo/blob/master/packages/EasyStandard/src/Sniffs/Methods/TestMethodNameSniff.php
[14]: https://github.com/eonx-com/easy-monorepo/blob/master/packages/EasyStandard/src/Sniffs/Namespaces/Psr4Sniff.php
