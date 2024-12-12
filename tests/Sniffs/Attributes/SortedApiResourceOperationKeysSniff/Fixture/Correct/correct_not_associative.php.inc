<?php

#[ApiResource(
    operations: [
        new Get(
            security: "is_granted(...)",
            status: 200
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['some-group:some-action']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['some-group:some-action']]
        ),
        new Put(
            status: 201
        ),
        new Patch(
            status: 201
        ),
        new Delete(
            status: 204
        ),
        new Get(
            uriTemplate: '/custom/uri',
            security: "is_granted(...)",
            status: 200
        ),
        new GetCollection(
            uriTemplate: '/custom/uri',
            normalizationContext: [
                'groups' => [
                    'some-group:some-action',
                ],
            ]
        ),
        new Post(
            uriTemplate: '/custom/uri',
            denormalizationContext: ['groups' => ['some-group:some-action']]
        ),
        new Put(
            uriTemplate: '/custom/uri',
            status: 201
        ),
        new Patch(
            uriTemplate: '/custom/uri',
            status: 201
        ),
        new Delete(
            uriTemplate: '/custom/uri',
            status: 204
        ),
    ],
    normalizationContext: ['some-context']
)]
class SomeClass
{
}