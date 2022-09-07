<?php

#[ApiResource(
    collectionOperations: [
        'export' => 'export',
        'get' => [
            'normalization_context' => [
                'some-context' => 'some-data',
            ],
            'security' => "is_granted(...)",
        ],
        'delete' => 'delete',
        'activate' => 'activate',
    ],
    itemOperations: [
        'get' => ['security' => "is_granted(..., object)"],
        'post' => 'post',
        'put' => 'put',
        'patch' => 'patch',
        'delete' => 'delete',
        'activate' => 'activate',
        'deactivate' => 'deactivate',
        'another-operation' => 'another-operation',
        'refund' => [
            'controller' => 'controller-fqcn',
            'denormalization_context' => ['groups' => ['empty']],
        ],
        'some-operation' => 'some-operation',
    ],
    normalizationContext: ['some-context']
)]
class SomeClass
{
}