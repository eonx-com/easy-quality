<?php

#[AttributeB]
#[\Group\AttributeB]
#[\Group\AttributeA('paramS')]
#[AttributeA]
#[\Group\AttributeA]
#[\Group\AttributeA('paramA')]
class Whatever
{
    #[UnknownOrder] #[AttributeB]
    #[AttributeA]
    public function method()
    {
    }

    #[AttributeB]
    #[\Group\AttributeB]
    #[\Group\AttributeA('paramS')]
    #[AttributeA]
    #[\Group\AttributeA]
    #[\Group\AttributeA('paramA')]
    public function method2()
    {
    }
}
-----
<?php

#[AttributeA]
#[AttributeB]
#[\Group\AttributeA]
#[\Group\AttributeA('paramA')]
#[\Group\AttributeA('paramS')]
#[\Group\AttributeB]
class Whatever
{
    #[AttributeA]
    #[AttributeB]
    #[UnknownOrder]
    public function method()
    {
    }

    #[AttributeA]
    #[AttributeB]
    #[\Group\AttributeA]
    #[\Group\AttributeA('paramA')]
    #[\Group\AttributeA('paramS')]
    #[\Group\AttributeB]
    public function method2()
    {
    }
}
