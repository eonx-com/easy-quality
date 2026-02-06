<?php

#[ApiResource(
    operations: [
        new GetCollection,
        new Post(),
    ]
)]
class EntityWithGetCollectionNoParenthesesNoOrder
{
}