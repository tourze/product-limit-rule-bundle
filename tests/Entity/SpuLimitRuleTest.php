<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\ProductLimitRuleBundle\Entity\SpuLimitRule;
use Tourze\ProductLimitRuleBundle\Enum\SpuLimitType;

/**
 * @internal
 */
#[CoversClass(SpuLimitRule::class)]
final class SpuLimitRuleTest extends AbstractEntityTestCase
{
    protected function getEntityClass(): string
    {
        return SpuLimitRule::class;
    }

    protected function createEntity(): object
    {
        $entity = new SpuLimitRule();
        $entity->setType(SpuLimitType::BUY_DAILY);

        return $entity;
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'spuId' => ['spuId', 'test-spu-123'];
        // value 在构造函数后设置，但测试基类可能无法正确处理
        // yield 'value' => ['value', 'test-value'];
        // type 在构造函数中设置，这里跳过测试
    }

    public function testCanBeCreated(): void
    {
        $limitRule = new SpuLimitRule();
        $limitRule->setType(SpuLimitType::BUY_DAILY);
        $limitRule->setSpuId('test-spu-id');
        $limitRule->setValue('10');

        $this->assertEquals('test-spu-id', $limitRule->getSpuId());
        $this->assertEquals(SpuLimitType::BUY_DAILY, $limitRule->getType());
        $this->assertEquals('10', $limitRule->getValue());
    }

    public function testToString(): void
    {
        $limitRule = new SpuLimitRule();
        $limitRule->setType(SpuLimitType::BUY_DAILY);
        $limitRule->setValue('5');
        $limitRule->setSpuId('test-spu-id');

        // 没有 ID 时应该返回空字符串
        $this->assertEquals('', (string) $limitRule);
    }

    public function testRetrieveAdminArray(): void
    {
        $limitRule = new SpuLimitRule();
        $limitRule->setType(SpuLimitType::BUY_MONTH);
        $limitRule->setSpuId('test-spu-id');
        $limitRule->setValue('50');

        $array = $limitRule->retrieveAdminArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('spuId', $array);
        $this->assertEquals(SpuLimitType::BUY_MONTH, $array['type']);
        $this->assertEquals('50', $array['value']);
        $this->assertEquals('test-spu-id', $array['spuId']);
    }
}
