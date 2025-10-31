<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\DataFixtures;

use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\ProductLimitRuleBundle\Entity\SkuLimitRule;
use Tourze\ProductLimitRuleBundle\Enum\SkuLimitType;

#[When(env: 'test')]
#[When(env: 'dev')]
class SkuLimitRuleFixtures extends Fixture implements FixtureGroupInterface
{
    public const TEST_SKU_LIMIT_RULE_REFERENCE = 'test-sku-limit-rule';

    public static function getGroups(): array
    {
        return ['product-limit-rule'];
    }

    public function load(ObjectManager $manager): void
    {
        // 创建测试用的 SKU 限制规则
        $limitRule = new SkuLimitRule();
        $limitRule->setSkuId('test-sku-id-001');
        $limitRule->setType(SkuLimitType::BUY_DAILY);
        $limitRule->setValue('10');
        $limitRule->setCreatedBy('1');
        $limitRule->setUpdatedBy('1');
        $limitRule->setCreateTime(CarbonImmutable::now());
        $limitRule->setUpdateTime(CarbonImmutable::now());
        $limitRule->setCreatedFromIp('127.0.0.1');

        $manager->persist($limitRule);
        $this->addReference(self::TEST_SKU_LIMIT_RULE_REFERENCE, $limitRule);

        // 创建更多测试数据
        $limitRule2 = new SkuLimitRule();
        $limitRule2->setSkuId('test-sku-id-002');
        $limitRule2->setType(SkuLimitType::MIN_QUANTITY);
        $limitRule2->setValue('5');
        $limitRule2->setCreatedBy('1');
        $limitRule2->setUpdatedBy('1');
        $limitRule2->setCreateTime(CarbonImmutable::now());
        $limitRule2->setUpdateTime(CarbonImmutable::now());
        $limitRule2->setCreatedFromIp('127.0.0.1');

        $manager->persist($limitRule2);

        $manager->flush();
    }
}
