<?php

#[ApiResource(
    collectionOperations: [
        'activate' => 'activate',
        'get' => [
            'normalization_context' => [
                'some-context' => 'some-data',
            ],
            'security' => "is_granted(...)",
        ],
        'delete' => 'delete',
    ],
    itemOperations: [
        'get' => ['security' => "is_granted(..., object)"],
        'some-operation' => 'some-operation',
        'another-operation' => 'another-operation',
        'refund' => [
            'controller' => 'controller-fqcn',
            'denormalization_context' => ['groups' => ['empty']],
        ],
        'post' => 'post',
        'deactivate' => 'deactivate',
        'put' => 'put',
        'patch' => 'patch',
        'activate' => 'activate',
        'delete' => 'delete',
    ],
    normalizationContext: ['some-context']
)]
class SomeClass
{
}

?>
-----
<?php

#[ApiResource(
    collectionOperations: [
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

?>
