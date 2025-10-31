<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\DataFixtures;

use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\ProductLimitRuleBundle\Entity\SpuLimitRule;
use Tourze\ProductLimitRuleBundle\Enum\SpuLimitType;

#[When(env: 'test')]
#[When(env: 'dev')]
class SpuLimitRuleFixtures extends Fixture implements FixtureGroupInterface
{
    public const TEST_SPU_LIMIT_RULE_REFERENCE = 'test-spu-limit-rule';

    public static function getGroups(): array
    {
        return ['product-limit-rule'];
    }

    public function load(ObjectManager $manager): void
    {
        // 创建测试用的 SPU 限制规则
        $limitRule = new SpuLimitRule();
        $limitRule->setSpuId('test-spu-id-001');
        $limitRule->setType(SpuLimitType::BUY_DAILY);
        $limitRule->setValue('50');
        $limitRule->setCreatedBy('1');
        $limitRule->setUpdatedBy('1');
        $limitRule->setCreateTime(CarbonImmutable::now());
        $limitRule->setUpdateTime(CarbonImmutable::now());
        $limitRule->setCreatedFromIp('127.0.0.1');

        $manager->persist($limitRule);
        $this->addReference(self::TEST_SPU_LIMIT_RULE_REFERENCE, $limitRule);

        // 创建更多测试数据
        $limitRule2 = new SpuLimitRule();
        $limitRule2->setSpuId('test-spu-id-002');
        $limitRule2->setType(SpuLimitType::BUY_MONTH);
        $limitRule2->setValue('100');
        $limitRule2->setCreatedBy('1');
        $limitRule2->setUpdatedBy('1');
        $limitRule2->setCreateTime(CarbonImmutable::now());
        $limitRule2->setUpdateTime(CarbonImmutable::now());
        $limitRule2->setCreatedFromIp('127.0.0.1');

        $manager->persist($limitRule2);

        $manager->flush();
    }
}
