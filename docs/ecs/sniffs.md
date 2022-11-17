---eonx_docs--- title: 'EasyQuality: Sniffs' weight: 1001 ---eonx_docs---

## Arrays

### [AlphabeticallySortedArrayKeysSniff](https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Arrays/AlphabeticallySortedArrayKeysSniff.php)

Arrays must be sorted by keys alphabetically.

```php
// Incorrect
$array = ['z' => 1, 'g' => 2, 'a' => 3];
```

```php
// Correct
$array = ['a' => 3, 'g' => 2, 'z' => 1];
```

**Configuration**

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

## Attributes

### [DoctrineColumnTypeSniff](https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Attributes/DoctrineColumnTypeSniff.php)

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

**Configuration**

- `replacePairs` - array with doctrine column type replace pairs

example:

```
'replacePairs' => [
    'string' => Types::STRING,
    'date' => Types::DATE_IMMUTABLE,
    'datetime' => Types::DATETIME_IMMUTABLE,
]
```

### [SortAttributesAlphabeticallySniff](https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Attributes/SortAttributesAlphabeticallySniff.php)

Sort attributes alphabetically taking into account an attribute name and content.

```php
// Incorrect
<?php

#[AttributeB]
#[\Group\AttributeB]
#[\Group\AttributeA('paramS')]
#[AttributeA]
#[\Group\AttributeA]
#[\Group\AttributeA('paramA')]
class Whatever
{

	#[UnknownOrder] #[AttributeB]
	#[AttributeA]
	public function method()
	{
	}

	#[AttributeB]
	#[\Group\AttributeB]
	#[\Group\AttributeA('paramS')]
	#[AttributeA]
	#[\Group\AttributeA]
	#[\Group\AttributeA('paramA')]
	public function method2()
	{
	}
}

```

```php
// Correct
<?php

#[AttributeA]
#[AttributeB]
#[\Group\AttributeA]
#[\Group\AttributeA('paramA')]
#[\Group\AttributeA('paramS')]
#[\Group\AttributeB]
class Whatever
{

	#[AttributeA]
	#[AttributeB]
	#[UnknownOrder]
	public function method()
	{
	}

	#[AttributeA]
	#[AttributeB]
	#[\Group\AttributeA]
	#[\Group\AttributeA('paramA')]
	#[\Group\AttributeA('paramS')]
	#[\Group\AttributeB]
	public function method2()
	{
	}
}

```

### [SortedApiResourceOperationKeysSniff](https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Attributes/SortedApiResourceOperationKeysSniff.php)

Api operations must be sorted

**Sort order**

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

## Classes

### [AvoidPrivatePropertiesSniff](https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Classes/AvoidPrivatePropertiesSniff.php)

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

### [AvoidPublicPropertiesSniff](https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Classes/AvoidPublicPropertiesSniff.php)

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

### [MakeClassAbstractSniff](https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Classes/MakeClassAbstractSniff.php)

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

### [PropertyTypeSniff](https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Classes/PropertyTypeSniff.php)

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

**Configuration**

- `replacePairs` - array with property type replace pairs

example:

```
'replacePairs' => [
    'DateTime' => 'DateTimeImmutable',
    'Carbon' => 'CarbonImmutable',
]
```

### [RequirePublicConstructorSniff](https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Classes/RequirePublicConstructorSniff.php)

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

### [RequireStrictDeclarationSniff](https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Classes/RequireStrictDeclarationSniff.php)

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

### [StrictDeclarationFormatSniff](https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Classes/StrictDeclarationFormatSniff.php)

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

## Commenting

### [AnnotationSortingSniff](https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Commenting/AnnotationSortingSniff.php)

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

**Configuration**

- `alwaysTopAnnotations` - A list of annotations that should always come first in the list, without regard to sorting.
  Default value: `[]`.

### [FunctionCommentSniff](https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Commenting/FunctionCommentSniff.php)

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

## Control Structures

### [ArrangeActAssertSniff](https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/ControlStructures/ArrangeActAssertSniff.php)

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

**Configuration**

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

### [NoNotOperatorSniff](https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/ControlStructures/NoNotOperatorSniff.php)

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

### [UseYieldInsteadOfReturnSniff](https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/ControlStructures/UseYieldInsteadOfReturnSniff.php)

Checks that `yield` is used instead of `return` in specified classes and methods.

**Configuration**

- `applyTo` - An array of regular expressions to match the class namespace and method name. Example:

```
[
    [
        'namespace' => '/^App\\\Tests/',
        'patterns' => ['/provide[A-Z]*/'],
    ]
]
```

## Exceptions

### [ThrowExceptionMessageSniff](https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Exceptions/ThrowExceptionMessageSniff.php)

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

**Configuration**

- `validPrefixes` - An array of prefixes that are valid for starting the message text. Default value: `['exceptions.']`.

## Functions

### [DisallowNonNullDefaultValueSniff](https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Functions/DisallowNonNullDefaultValueSniff.php)

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

## Methods

### [TestMethodNameSniff](https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Methods/TestMethodNameSniff.php)

Checks that a method name matches/does not match a specific regex.

**Configuration**

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

## Namespaces

### [Psr4Sniff](https://github.com/eonx-com/easy-quality/blob/main/src/Sniffs/Namespaces/Psr4Sniff.php)

Checks that a namespace name matches PSR-4 project structure.

**Configuration**

- `composerJsonPath` - A relative path to the project file `composer.json`. Default value: `composer.json`.
