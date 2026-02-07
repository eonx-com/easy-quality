<?php

#[ApiResource(
    order: ['createdAt' => 'DESC'],
    operations: [
        new Get(),
        new GetCollection(),
        new GetCollection(
            uriTemplate: '/custom',
            order: ['name' => 'ASC']
        ),
        new Post(),
    ]
)]
class EntityWithMixedOrder
{
}