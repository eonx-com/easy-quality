<?php
class AlreadyHasDefaultNullValue
{
    public function __construct(?string $value = null){
        $result = $value;
    }
}
