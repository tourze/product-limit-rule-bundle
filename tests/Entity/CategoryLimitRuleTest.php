<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\ProductLimitRuleBundle\Entity\CategoryLimitRule;
use Tourze\ProductLimitRuleBundle\Enum\CategoryLimitType;

/**
 * @internal
 */
#[CoversClass(CategoryLimitRule::class)]
class CategoryLimitRuleTest extends AbstractEntityTestCase
{
    protected function getEntityClass(): string
    {
        return CategoryLimitRule::class;
    }

    protected function createEntity(): object
    {
        $entity = new CategoryLimitRule();
        $entity->setCategoryId('test-category-id');
        $entity->setType(CategoryLimitType::BUY_TOTAL);

        return $entity;
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'categoryId' => ['categoryId', 'test-category-id'];
        yield 'value' => ['value', 'test-value'];
        yield 'remark' => ['remark', 'test-remark'];
    }

    public function testEntity(): void
    {
        $entity = new CategoryLimitRule();
        $entity->setCategoryId('category-123');
        $entity->setType(CategoryLimitType::BUY_TOTAL);

        // 测试categoryId
        $this->assertEquals('category-123', $entity->getCategoryId());
        $entity->setCategoryId('category-456');
        $this->assertEquals('category-456', $entity->getCategoryId());

        // 测试基本的 getter/setter
        $entity->setValue('test-value');
        $this->assertEquals('test-value', $entity->getValue());

        $this->assertEquals(CategoryLimitType::BUY_TOTAL, $entity->getType());

        $entity->setRemark('test-remark');
        $this->assertEquals('test-remark', $entity->getRemark());
    }
}
