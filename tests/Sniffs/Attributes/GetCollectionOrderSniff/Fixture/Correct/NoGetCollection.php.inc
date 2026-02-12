<?php

#[ApiResource(
    operations: [
        new Get(),
        new Post(),
        new Put(),
        new Delete(),
    ]
)]
class EntityWithoutGetCollection
{
}