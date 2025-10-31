<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use Tourze\ProductLimitRuleBundle\Enum\SpuLimitType;

/**
 * @internal
 */
#[CoversClass(SpuLimitType::class)]
class SpuLimitTypeTest extends AbstractEnumTestCase
{
    public function testCases(): void
    {
        $this->assertSame('buy-total', SpuLimitType::BUY_TOTAL->value);
        $this->assertSame('buy-year', SpuLimitType::BUY_YEAR->value);
        $this->assertSame('buy-quarter', SpuLimitType::BUY_QUARTER->value);
        $this->assertSame('buy-month', SpuLimitType::BUY_MONTH->value);
        $this->assertSame('buy-daily', SpuLimitType::BUY_DAILY->value);
        $this->assertSame('specify-coupon', SpuLimitType::SPECIFY_COUPON->value);
        $this->assertSame('spu-mutex', SpuLimitType::SPU_MUTEX->value);
    }

    public function testLabels(): void
    {
        $this->assertSame('总次数限购', SpuLimitType::BUY_TOTAL->getLabel());
        $this->assertSame('按年度限购', SpuLimitType::BUY_YEAR->getLabel());
        $this->assertSame('按季度限购', SpuLimitType::BUY_QUARTER->getLabel());
        $this->assertSame('按月度限购', SpuLimitType::BUY_MONTH->getLabel());
        $this->assertSame('按日限购', SpuLimitType::BUY_DAILY->getLabel());
        $this->assertSame('特定优惠券购买', SpuLimitType::SPECIFY_COUPON->getLabel());
        $this->assertSame('SPU购买互斥', SpuLimitType::SPU_MUTEX->getLabel());
    }

    public function testAllCasesHaveUniqueValues(): void
    {
        $values = array_map(fn (SpuLimitType $case) => $case->value, SpuLimitType::cases());
        $uniqueValues = array_unique($values);

        $this->assertSameSize($values, $uniqueValues);
        $this->assertCount(count($values), $uniqueValues);
    }

    public function testAllCasesHaveNonEmptyLabels(): void
    {
        foreach (SpuLimitType::cases() as $case) {
            $this->assertNotEmpty($case->getLabel());
            $this->assertIsString($case->getLabel());
        }
    }

    public function testToArray(): void
    {
        $case = SpuLimitType::BUY_TOTAL;
        $array = $case->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertEquals('buy-total', $array['value']);
        $this->assertEquals('总次数限购', $array['label']);
    }
}
