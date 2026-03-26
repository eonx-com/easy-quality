<?php

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(
            order: ['field1']
        ),
        new Post(),
    ]
)]
class EntityWithInvalidOrderFormatInOperation
{
}