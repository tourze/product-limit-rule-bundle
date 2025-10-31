<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use Tourze\ProductLimitRuleBundle\Enum\SkuLimitType;

/**
 * @internal
 */
#[CoversClass(SkuLimitType::class)]
class SkuLimitTypeTest extends AbstractEnumTestCase
{
    public function testCases(): void
    {
        $this->assertSame('buy-total', SkuLimitType::BUY_TOTAL->value);
        $this->assertSame('buy-year', SkuLimitType::BUY_YEAR->value);
        $this->assertSame('buy-quarter', SkuLimitType::BUY_QUARTER->value);
        $this->assertSame('buy-month', SkuLimitType::BUY_MONTH->value);
        $this->assertSame('buy-daily', SkuLimitType::BUY_DAILY->value);
        $this->assertSame('specify-coupon', SkuLimitType::SPECIFY_COUPON->value);
        $this->assertSame('sku-mutex', SkuLimitType::SKU_MUTEX->value);
        $this->assertSame('min-quantity', SkuLimitType::MIN_QUANTITY->value);
    }

    public function testLabels(): void
    {
        $this->assertSame('总次数限购', SkuLimitType::BUY_TOTAL->getLabel());
        $this->assertSame('按年度限购', SkuLimitType::BUY_YEAR->getLabel());
        $this->assertSame('按季度限购', SkuLimitType::BUY_QUARTER->getLabel());
        $this->assertSame('按月度限购', SkuLimitType::BUY_MONTH->getLabel());
        $this->assertSame('按日限购', SkuLimitType::BUY_DAILY->getLabel());
        $this->assertSame('特定优惠券购买', SkuLimitType::SPECIFY_COUPON->getLabel());
        $this->assertSame('SKU购买互斥', SkuLimitType::SKU_MUTEX->getLabel());
        $this->assertSame('最低购买数量', SkuLimitType::MIN_QUANTITY->getLabel());
    }

    public function testAllCasesHaveUniqueValues(): void
    {
        $values = array_map(fn (SkuLimitType $case) => $case->value, SkuLimitType::cases());
        $uniqueValues = array_unique($values);

        $this->assertSameSize($values, $uniqueValues);
        $this->assertCount(count($values), $uniqueValues);
    }

    public function testAllCasesHaveNonEmptyLabels(): void
    {
        foreach (SkuLimitType::cases() as $case) {
            $this->assertNotEmpty($case->getLabel());
            $this->assertIsString($case->getLabel());
        }
    }

    public function testToArray(): void
    {
        $case = SkuLimitType::BUY_TOTAL;
        $array = $case->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertEquals('buy-total', $array['value']);
        $this->assertEquals('总次数限购', $array['label']);
    }
}
