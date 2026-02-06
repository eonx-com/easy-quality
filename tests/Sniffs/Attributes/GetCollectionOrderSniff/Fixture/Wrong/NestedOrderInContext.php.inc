<?php

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['order' => 'field'],
            paginationItemsPerPage: 10
        ),
    ]
)]
class EntityWithNestedOrderInContext
{
}