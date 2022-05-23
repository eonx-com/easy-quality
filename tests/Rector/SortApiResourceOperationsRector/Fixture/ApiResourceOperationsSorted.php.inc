<?php
declare(strict_types=1);

use ApiPlatform\Core\Annotation\ApiResource;

#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [],
            'security' => "...",
        ],
        'post' => [
            'some_attr' => 'some_value',
        ],
        'put' => 'put',
        'patch' => 'patch',
        'delete' => 'delete',
        'activate' => 'activate',
        'deactivate' => 'deactivate',
        'another_custom_opration' => 'another_custom_operation',
        'some_custom_operation' => [
            'some_custom_attr' => 'some_custom_value',
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [],
            'security' => "...",
        ],
        'post' => [
            'some_attr' => 'some_value',
        ],
        'put' => 'put',
        'patch' => 'patch',
        'delete' => 'delete',
        'activate' => 'activate',
        'deactivate' => 'deactivate',
        'another_custom_opration' => 'another_custom_operation',
        'some_custom_operation' => [
            'some_custom_attr' => 'some_custom_value',
        ],
    ],
    normalizationContext: ['groups' => ['...']]
)]
class SomeClass
{
}

