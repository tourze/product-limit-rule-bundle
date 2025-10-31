<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Tourze\ProductLimitRuleBundle\Entity\CategoryLimitRule;
use Tourze\ProductLimitRuleBundle\Enum\CategoryLimitType;

class CategoryLimitRuleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $rule1 = new CategoryLimitRule();
        $rule1->setCategoryId('category-electronics');
        $rule1->setType(CategoryLimitType::BUY_TOTAL);
        $rule1->setValue('10');
        $rule1->setRemark('电子产品总购买数量限制');
        $manager->persist($rule1);

        $rule2 = new CategoryLimitRule();
        $rule2->setCategoryId('category-books');
        $rule2->setType(CategoryLimitType::BUY_YEAR);
        $rule2->setValue('5');
        $rule2->setRemark('图书年度购买数量限制');
        $manager->persist($rule2);

        $rule3 = new CategoryLimitRule();
        $rule3->setCategoryId('category-clothing');
        $rule3->setType(CategoryLimitType::BUY_QUARTER);
        $rule3->setValue('3');
        $rule3->setRemark('服装季度购买数量限制');
        $manager->persist($rule3);

        $manager->flush();
    }
}
