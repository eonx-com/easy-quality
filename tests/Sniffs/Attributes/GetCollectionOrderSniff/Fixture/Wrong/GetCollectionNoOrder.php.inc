<?php

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['read']],
            security: "is_granted('ROLE_USER')"
        ),
    ]
)]
class EntityWithGetCollectionButNoOrder
{
}
