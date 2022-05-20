<?php
declare(strict_types=1);

#[ApiResource(
    collectionOperations: [
        'post' => [
            'some_attr' => 'some_value',
        ],
        'some_custom_operation' => [
            'some_custom_attr' => 'some_custom_value',
        ],
        'another_custom_opration' => 'another_custom_operation',
        'delete' => 'delete',
        'get' => [
            'normalization_context' => [],
            'security' => "...",
        ],
        'deactivate' => 'deactivate',
        'activate' => 'activate',
        'patch' => 'patch',
        'put' => 'put',
    ],
    itemOperations: [
        'post' => [
            'some_attr' => 'some_value',
        ],
        'some_custom_operation' => [
            'some_custom_attr' => 'some_custom_value',
        ],
        'another_custom_opration' => 'another_custom_operation',
        'delete' => 'delete',
        'get' => [
            'normalization_context' => [],
            'security' => "...",
        ],
        'deactivate' => 'deactivate',
        'activate' => 'activate',
        'patch' => 'patch',
        'put' => 'put',
    ],
    normalizationContext: ['groups' => ['...']]
)]
class SomeClass
{
}

-----
<?php
declare(strict_types=1);

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
