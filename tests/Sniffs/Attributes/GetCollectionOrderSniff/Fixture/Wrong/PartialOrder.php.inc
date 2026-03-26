<?php

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(
            order: ['name' => 'ASC']
        ),
        new Post(),
        new GetCollection(
            uriTemplate: '/custom'
        ),
    ]
)]
class EntityWithPartialOrder
{
}
