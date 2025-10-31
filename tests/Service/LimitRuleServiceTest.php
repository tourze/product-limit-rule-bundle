<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\ProductLimitRuleBundle\Entity\CategoryLimitRule;
use Tourze\ProductLimitRuleBundle\Entity\SkuLimitRule;
use Tourze\ProductLimitRuleBundle\Entity\SpuLimitRule;
use Tourze\ProductLimitRuleBundle\Enum\CategoryLimitType;
use Tourze\ProductLimitRuleBundle\Enum\SkuLimitType;
use Tourze\ProductLimitRuleBundle\Enum\SpuLimitType;
use Tourze\ProductLimitRuleBundle\Repository\CategoryLimitRuleRepository;
use Tourze\ProductLimitRuleBundle\Repository\SkuLimitRuleRepository;
use Tourze\ProductLimitRuleBundle\Repository\SpuLimitRuleRepository;
use Tourze\ProductLimitRuleBundle\Service\LimitRuleService;

/**
 * @internal
 */
#[CoversClass(LimitRuleService::class)]
#[RunTestsInSeparateProcesses]
final class LimitRuleServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 子类可以重写此方法添加自定义的 setUp 逻辑
    }

    public function testServiceCanBeInstantiated(): void
    {
        $service = self::getService(LimitRuleService::class);
        $this->assertInstanceOf(LimitRuleService::class, $service);
    }

    public function testFindSpuLimitRulesBySpuId(): void
    {
        $service = self::getService(LimitRuleService::class);

        // 测试空结果
        $result = $service->findSpuLimitRulesBySpuId('non-existent-spu');
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testFindSkuLimitRulesBySkuId(): void
    {
        $service = self::getService(LimitRuleService::class);

        // 测试空结果
        $result = $service->findSkuLimitRulesBySkuId('non-existent-sku');
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testFindCategoryLimitRulesByCategoryId(): void
    {
        $service = self::getService(LimitRuleService::class);

        // 测试空结果
        $result = $service->findCategoryLimitRulesByCategoryId('non-existent-category');
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testFindSpuLimitRulesWithExistingData(): void
    {
        $service = self::getService(LimitRuleService::class);
        $repository = self::getService(SpuLimitRuleRepository::class);

        // 创建测试数据
        $rule = new SpuLimitRule();
        $rule->setSpuId('test-spu-123');
        $rule->setType(SpuLimitType::BUY_TOTAL);
        $rule->setValue('10');
        $repository->save($rule, true);

        // 测试查询
        $result = $service->findSpuLimitRulesBySpuId('test-spu-123');
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertSame('test-spu-123', $result[0]->getSpuId());
    }

    public function testFindSkuLimitRulesWithExistingData(): void
    {
        $service = self::getService(LimitRuleService::class);
        $repository = self::getService(SkuLimitRuleRepository::class);

        // 创建测试数据
        $rule = new SkuLimitRule();
        $rule->setSkuId('test-sku-456');
        $rule->setType(SkuLimitType::BUY_DAILY);
        $rule->setValue('5');
        $repository->save($rule, true);

        // 测试查询
        $result = $service->findSkuLimitRulesBySkuId('test-sku-456');
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertSame('test-sku-456', $result[0]->getSkuId());
    }

    public function testFindCategoryLimitRulesWithExistingData(): void
    {
        $service = self::getService(LimitRuleService::class);
        $repository = self::getService(CategoryLimitRuleRepository::class);

        // 创建测试数据
        $rule = new CategoryLimitRule();
        $rule->setCategoryId('test-category-789');
        $rule->setType(CategoryLimitType::BUY_TOTAL);
        $rule->setValue('20');
        $repository->save($rule, true);

        // 测试查询
        $result = $service->findCategoryLimitRulesByCategoryId('test-category-789');
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertSame('test-category-789', $result[0]->getCategoryId());
    }
}
