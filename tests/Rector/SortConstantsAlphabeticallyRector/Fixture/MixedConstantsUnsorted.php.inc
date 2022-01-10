<?php
declare(strict_types=1);

class SomeClass
{
    public const PUBLIC_CONST1 = 'some-value';
    private const PRIVATE_CONST1 = 'some-value';
    protected const PROTECTED_CONST1 = 'some-value';
    public const PUBLIC_CONST2 = 'some-value';
    protected const PROTECTED_CONST2 = 'some-value';
    private const PRIVATE_CONST2 = 'some-value';
}

?>

-----
<?php
declare(strict_types=1);

class SomeClass
{
    private const PRIVATE_CONST1 = 'some-value';
    private const PRIVATE_CONST2 = 'some-value';
    protected const PROTECTED_CONST1 = 'some-value';
    protected const PROTECTED_CONST2 = 'some-value';
    public const PUBLIC_CONST1 = 'some-value';
    public const PUBLIC_CONST2 = 'some-value';
}

?>
