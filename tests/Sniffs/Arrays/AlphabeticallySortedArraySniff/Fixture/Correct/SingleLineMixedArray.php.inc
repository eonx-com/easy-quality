<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Arrays\AlphabeticallySortedArraySniff\Fixture\Correct;

$array = [
    SomeFile::TEST_CONST => 'abc',
    'aud' => self::ANOTHER_TEST_CONST,
    'exp' => \time() + 3600,
    'iat' => \time(),
    'iss' => self::TEST_CONST,
    self::ANOTHER_TEST_CONST => [
        'email' => $someObject->getEmail(),
        'fn' => $someObject->getFn(),
        'ln' => $someObject->getLn(),
        'phone' => $someObject->getPhone(),
    ],
    'sub' => $someObject->getValue(),
];

$array2 = array(
    SomeFile::TEST_CONST => 'abc',
    'aud' => self::ANOTHER_TEST_CONST,
    'exp' => \time() + 3600,
    'iat' => \time(),
    'iss' => self::TEST_CONST,
    self::ANOTHER_TEST_CONST => array(
        'email' => $someObject->getEmail(),
        'fn' => $someObject->getFn(),
        'ln' => $someObject->getLn(),
        'phone' => $someObject->getPhone(),
    ),
    'sub' => $someObject->getValue(),
);
