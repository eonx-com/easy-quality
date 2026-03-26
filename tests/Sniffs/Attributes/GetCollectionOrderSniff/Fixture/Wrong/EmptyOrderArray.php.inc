<?php

#[ApiResource(
    order: [],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
    ]
)]
class EntityWithEmptyOrderArray
{
}