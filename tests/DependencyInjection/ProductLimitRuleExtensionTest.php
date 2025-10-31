<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use Tourze\ProductLimitRuleBundle\DependencyInjection\ProductLimitRuleExtension;

/**
 * @internal
 */
#[CoversClass(ProductLimitRuleExtension::class)]
final class ProductLimitRuleExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    public function testGetAlias(): void
    {
        $extension = new ProductLimitRuleExtension();
        $this->assertSame('product_limit_rule', $extension->getAlias());
    }

    public function testIsExtension(): void
    {
        $extension = new ProductLimitRuleExtension();
        $this->assertInstanceOf(ExtensionInterface::class, $extension);
    }
}
