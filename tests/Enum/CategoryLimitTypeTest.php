<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use Tourze\ProductLimitRuleBundle\Enum\CategoryLimitType;

/**
 * @internal
 */
#[CoversClass(CategoryLimitType::class)]
class CategoryLimitTypeTest extends AbstractEnumTestCase
{
    public function testEnumCases(): void
    {
        $cases = CategoryLimitType::cases();
        $this->assertGreaterThan(0, count($cases));

        // 测试所有枚举值都有对应的标签
        foreach ($cases as $case) {
            $label = $case->getLabel();
            $this->assertIsString($label);
            $this->assertNotEmpty($label);
        }
    }

    public function testBuyTotalCase(): void
    {
        $case = CategoryLimitType::BUY_TOTAL;
        $this->assertEquals('buy-total', $case->value);
        $this->assertEquals('总次数限购', $case->getLabel());
    }

    public function testBuyYearCase(): void
    {
        $case = CategoryLimitType::BUY_YEAR;
        $this->assertEquals('buy-year', $case->value);
        $this->assertEquals('按年度限购', $case->getLabel());
    }

    public function testToArray(): void
    {
        $case = CategoryLimitType::BUY_TOTAL;
        $array = $case->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertEquals('buy-total', $array['value']);
        $this->assertEquals('总次数限购', $array['label']);
    }
}
