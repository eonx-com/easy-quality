<?php

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\ParentTestCase;

final class MyTestCase extends ParentTestCase
{
    /**
     * Example:
     * 'permission' => [
     *     'method' => 'POST',
     *     'uri' => '/providers',
     *     'permission' => PermissionsInterface::PERMISSION_PROVIDER_CREATE,
     *     'data' => ['name' => 'provider name'],
     * ],
     * ...
     *
     * @return mixed[]
     *
     * @see testAccessDeniedWithApiKeyAuth
     * @see testAccessDeniedWithJwtAuth
     */
    public function provideData(): iterable
    {
        yield ['item'];
    }
}

?>
-----
<?php

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\ParentTestCase;

final class MyTestCase extends ParentTestCase
{
    /**
     * Example:
     * 'permission' => [
     *     'method' => 'POST',
     *     'uri' => '/providers',
     *     'permission' => PermissionsInterface::PERMISSION_PROVIDER_CREATE,
     *     'data' => ['name' => 'provider name'],
     * ],
     * ...
     *
     * @return iterable<mixed>
     *
     * @see testAccessDeniedWithApiKeyAuth
     * @see testAccessDeniedWithJwtAuth
     */
    public function provideData(): iterable
    {
        yield ['item'];
    }
}

?>
