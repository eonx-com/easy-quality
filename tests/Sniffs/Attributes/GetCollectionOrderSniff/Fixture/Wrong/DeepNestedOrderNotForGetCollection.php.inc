<?php

#[ApiResource(
    operations: [
        new GetCollection(
            extraProperties: [
                'filters' => ['order' => 'custom'],
            ]
        ),
    ]
)]
class EntityWithDeepNestedOrder
{
}