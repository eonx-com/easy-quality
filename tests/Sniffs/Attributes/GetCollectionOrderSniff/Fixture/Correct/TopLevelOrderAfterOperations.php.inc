<?php

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
    ],
    order: ['createdAt' => 'DESC']
)]
class EntityWithTopLevelOrderAfterOperations
{
}