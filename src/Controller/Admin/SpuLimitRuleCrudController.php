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
use Tourze\ProductLimitRuleBundle\Entity\SpuLimitRule;
use Tourze\ProductLimitRuleBundle\Enum\SpuLimitType;

#[AdminCrud(routePath: '/product-limit-rule/spu', routeName: 'product_limit_rule_spu')]
#[IsGranted(attribute: 'ROLE_ADMIN')]
final class SpuLimitRuleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SpuLimitRule::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('SPU限制规则')
            ->setEntityLabelInPlural('SPU限制规则管理')
            ->setPageTitle('index', 'SPU限制规则列表')
            ->setPageTitle('new', '创建SPU限制规则')
            ->setPageTitle('edit', '编辑SPU限制规则')
            ->setPageTitle('detail', 'SPU限制规则详情')
            ->setHelp('index', '管理产品SPU的购买限制规则，包括限购数量、购买周期、门店限制等设置')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'spuId', 'value'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999);
        yield TextField::new('spuId', 'SPU ID');
        yield ChoiceField::new('type', '限制类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => SpuLimitType::class])
            ->formatValue(function ($value) {
                return $value instanceof SpuLimitType ? $value->getLabel() : '';
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
        foreach (SpuLimitType::cases() as $case) {
            $typeChoices[$case->getLabel()] = $case->value;
        }

        return $filters
            ->add(TextFilter::new('spuId', 'SPU ID'))
            ->add(ChoiceFilter::new('type', '限制类型')->setChoices($typeChoices))
            ->add(TextFilter::new('value', '规则值'))
            ->add(TextFilter::new('createdBy', '创建用户'))
            ->add(TextFilter::new('updatedBy', '更新用户'))
        ;
    }
}
