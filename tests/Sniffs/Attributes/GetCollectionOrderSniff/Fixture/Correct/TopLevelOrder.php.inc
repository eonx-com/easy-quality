<?php

#[ApiResource(
    order: ['createdAt' => 'DESC'],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
    ]
)]
class EntityWithTopLevelOrder
{
}