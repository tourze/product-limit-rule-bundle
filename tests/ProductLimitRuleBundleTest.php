<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use Tourze\ProductLimitRuleBundle\ProductLimitRuleBundle;

/**
 * @internal
 */
#[CoversClass(ProductLimitRuleBundle::class)]
#[RunTestsInSeparateProcesses]
final class ProductLimitRuleBundleTest extends AbstractBundleTestCase
{
}
