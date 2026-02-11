<?php

#[ApiResource(
    order: ['createdAt' => 'DESC', 'invalidElement'],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
    ]
)]
class EntityWithMixedValidAndInvalidOrder
{
}