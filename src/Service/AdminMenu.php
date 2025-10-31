<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\Service;

use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Attribute\MenuProvider;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\ProductLimitRuleBundle\Entity\CategoryLimitRule;
use Tourze\ProductLimitRuleBundle\Entity\SkuLimitRule;
use Tourze\ProductLimitRuleBundle\Entity\SpuLimitRule;

#[MenuProvider]
final class AdminMenu implements MenuProviderInterface
{
    public function __construct(private readonly LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        $limitRuleMenu = $item->getChild('产品限制');
        if (null === $limitRuleMenu) {
            $limitRuleMenu = $item->addChild('产品限制');
        }

        $limitRuleMenu->addChild('分类限制规则')->setUri($this->linkGenerator->getCurdListPage(CategoryLimitRule::class));
        $limitRuleMenu->addChild('SKU限制规则')->setUri($this->linkGenerator->getCurdListPage(SkuLimitRule::class));
        $limitRuleMenu->addChild('SPU限制规则')->setUri($this->linkGenerator->getCurdListPage(SpuLimitRule::class));
    }
}
