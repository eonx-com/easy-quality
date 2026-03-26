<?php

#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_ADMIN') and object.order > 100"
        ),
    ]
)]
class EntityWithOrderInSecurityExpression
{
}