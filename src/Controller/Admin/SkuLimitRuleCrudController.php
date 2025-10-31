<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\ProductLimitRuleBundle\Entity\SkuLimitRule;
use Tourze\ProductLimitRuleBundle\Enum\SkuLimitType;

#[AdminCrud(routePath: '/product-limit-rule/sku', routeName: 'product_limit_rule_sku')]
#[IsGranted(attribute: 'ROLE_ADMIN')]
final class SkuLimitRuleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SkuLimitRule::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('SKU限制规则')
            ->setEntityLabelInPlural('SKU限制规则管理')
            ->setPageTitle('index', 'SKU限制规则列表')
            ->setPageTitle('new', '创建SKU限制规则')
            ->setPageTitle('edit', '编辑SKU限制规则')
            ->setPageTitle('detail', 'SKU限制规则详情')
            ->setHelp('index', '管理商品SKU的购买限制规则，包括限购数量、购买周期等设置')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'skuId', 'value'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999);
        yield TextField::new('skuId', 'SKU ID');
        yield ChoiceField::new('type', '限制类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => SkuLimitType::class])
            ->formatValue(function ($value) {
                return $value instanceof SkuLimitType ? $value->getLabel() : '';
            })
        ;
        yield TextField::new('value', '规则值');
        yield TextField::new('createdBy', '创建用户');
        yield TextField::new('updatedBy', '更新用户');
        yield DateTimeField::new('createTime', '创建时间')->hideOnForm();
        yield DateTimeField::new('updateTime', '更新时间')->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        $typeChoices = [];
        foreach (SkuLimitType::cases() as $case) {
            $typeChoices[$case->getLabel()] = $case->value;
        }

        return $filters
            ->add(TextFilter::new('skuId', 'SKU ID'))
            ->add(ChoiceFilter::new('type', '限制类型')->setChoices($typeChoices))
            ->add(TextFilter::new('value', '规则值'))
            ->add(TextFilter::new('createdBy', '创建用户'))
            ->add(TextFilter::new('updatedBy', '更新用户'))
        ;
    }
}
