<?php

class SecondParameterMissingDefaultValue
{
    private function someMethod(string $param, ?string $value): void
    {
        $result = $param . $value;
    }
}

?>
-----
<?php

class SecondParameterMissingDefaultValue
{
    private function someMethod(string $param, ?string $value = null): void
    {
        $result = $param . $value;
    }
}

?>
