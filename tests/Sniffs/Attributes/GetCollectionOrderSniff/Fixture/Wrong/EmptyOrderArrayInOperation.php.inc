<?php

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(
            order: []
        ),
        new Post(),
    ]
)]
class EntityWithEmptyOrderArrayInOperation
{
}