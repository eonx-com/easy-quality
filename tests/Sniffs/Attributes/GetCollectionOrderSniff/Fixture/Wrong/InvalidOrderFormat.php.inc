<?php

#[ApiResource(
    order: ['field1', 'field2'],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
    ]
)]
class EntityWithInvalidOrderFormat
{
}