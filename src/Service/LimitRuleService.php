<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\Service;

use Tourze\ProductLimitRuleBundle\Entity\CategoryLimitRule;
use Tourze\ProductLimitRuleBundle\Entity\SkuLimitRule;
use Tourze\ProductLimitRuleBundle\Entity\SpuLimitRule;
use Tourze\ProductLimitRuleBundle\Repository\CategoryLimitRuleRepository;
use Tourze\ProductLimitRuleBundle\Repository\SkuLimitRuleRepository;
use Tourze\ProductLimitRuleBundle\Repository\SpuLimitRuleRepository;

/**
 * 限购规则服务
 */
class LimitRuleService
{
    public function __construct(
        private readonly SpuLimitRuleRepository $spuLimitRuleRepository,
        private readonly SkuLimitRuleRepository $skuLimitRuleRepository,
        private readonly CategoryLimitRuleRepository $categoryLimitRuleRepository,
    ) {
    }

    /**
     * 根据SPU ID查找限购规则
     *
     * @return SpuLimitRule[]
     */
    public function findSpuLimitRulesBySpuId(string $spuId): array
    {
        return $this->spuLimitRuleRepository->findBySpuId($spuId);
    }

    /**
     * 根据SKU ID查找限购规则
     *
     * @return SkuLimitRule[]
     */
    public function findSkuLimitRulesBySkuId(string $skuId): array
    {
        return $this->skuLimitRuleRepository->findBySkuId($skuId);
    }

    /**
     * 根据分类ID查找限购规则
     *
     * @return CategoryLimitRule[]
     */
    public function findCategoryLimitRulesByCategoryId(string $categoryId): array
    {
        return $this->categoryLimitRuleRepository->findByCategoryId($categoryId);
    }
}
