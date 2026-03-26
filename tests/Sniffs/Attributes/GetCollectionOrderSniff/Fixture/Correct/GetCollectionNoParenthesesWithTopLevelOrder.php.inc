<?php

#[ApiResource(
    order: ['id' => 'DESC'],
    operations: [
        new GetCollection,
        new Post(),
    ]
)]
class EntityWithGetCollectionNoParenthesesButHasOrder
{
}