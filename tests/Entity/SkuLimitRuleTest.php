<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\ProductLimitRuleBundle\Entity\SkuLimitRule;
use Tourze\ProductLimitRuleBundle\Enum\SkuLimitType;

/**
 * @internal
 */
#[CoversClass(SkuLimitRule::class)]
final class SkuLimitRuleTest extends AbstractEntityTestCase
{
    protected function getEntityClass(): string
    {
        return SkuLimitRule::class;
    }

    protected function createEntity(): object
    {
        $entity = new SkuLimitRule();
        $entity->setType(SkuLimitType::BUY_DAILY);

        return $entity;
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'skuId' => ['skuId', 'test-sku-123'];
        // value 在构造函数后设置，但测试基类可能无法正确处理
        // yield 'value' => ['value', 'test-value'];
        // type 在构造函数中设置，这里跳过测试
    }

    public function testCanBeCreated(): void
    {
        $limitRule = new SkuLimitRule();
        $limitRule->setType(SkuLimitType::BUY_DAILY);
        $limitRule->setSkuId('test-sku-id');
        $limitRule->setValue('5');

        $this->assertEquals('test-sku-id', $limitRule->getSkuId());
        $this->assertEquals(SkuLimitType::BUY_DAILY, $limitRule->getType());
        $this->assertEquals('5', $limitRule->getValue());
    }

    public function testToString(): void
    {
        $limitRule = new SkuLimitRule();
        $limitRule->setType(SkuLimitType::MIN_QUANTITY);
        $limitRule->setValue('3');

        // 没有 ID 时应该返回空字符串
        $this->assertEquals('', (string) $limitRule);
    }

    public function testRetrieveAdminArray(): void
    {
        $limitRule = new SkuLimitRule();
        $limitRule->setType(SkuLimitType::SKU_MUTEX);
        $limitRule->setSkuId('test-sku-id');
        $limitRule->setValue('sku-group-1');

        $array = $limitRule->retrieveAdminArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('skuId', $array);
        $this->assertEquals(SkuLimitType::SKU_MUTEX, $array['type']);
        $this->assertEquals('sku-group-1', $array['value']);
        $this->assertEquals('test-sku-id', $array['skuId']);
    }
}
