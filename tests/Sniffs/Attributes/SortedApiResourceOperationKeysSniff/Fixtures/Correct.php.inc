<?php

#[ApiResource(
    collectionOperations: [
        'export' => 'export',
        'export_abc' => 'export_abc',
        'get' => [
            'normalization_context' => [
                'some-context' => 'some-data',
            ],
            'security' => "is_granted(...)",
        ],
        'get_me' => 'get_me',
        'delete' => 'delete',
        'activate' => 'activate',
    ],
    itemOperations: [
        'get' => ['security' => "is_granted(..., object)"],
        'post' => 'post',
        'put' => 'put',
        'put_me' => 'put_me',
        'patch' => 'patch',
        'patch_me' => 'patch_me',
        'delete' => 'delete',
        'delete_me' => 'delete_me',
        'activate' => 'activate',
        'activate_me' => 'activate_me',
        'deactivate' => 'deactivate',
        'deactivate_me' => 'deactivate_me',
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