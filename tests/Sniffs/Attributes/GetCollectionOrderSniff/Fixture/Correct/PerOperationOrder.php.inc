<?php

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(
            order: ['name' => 'ASC']
        ),
        new Post(),
        new GetCollection(
            uriTemplate: '/custom',
            order: ['id' => 'DESC']
        ),
    ]
)]
class EntityWithPerOperationOrder
{
}