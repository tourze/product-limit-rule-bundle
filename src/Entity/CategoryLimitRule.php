<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\Entity;

use BizUserBundle\Entity\BizUser;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Attribute\UpdateUserColumn;
use Tourze\DoctrineUserBundle\Traits\CreateUserAware;
use Tourze\ProductLimitRuleBundle\Enum\CategoryLimitType;
use Tourze\ProductLimitRuleBundle\Repository\CategoryLimitRuleRepository;

#[ORM\Entity(repositoryClass: CategoryLimitRuleRepository::class)]
#[ORM\Table(name: 'category_limit_rule', options: ['comment' => '分类限制规则表'])]
class CategoryLimitRule implements \Stringable
{
    use TimestampableAware;
    use CreateUserAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 40, nullable: false, options: ['comment' => '分类ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 40)]
    private string $categoryId = '';

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '限制值'])]
    #[Assert\Length(max: 255)]
    private ?string $value = null;

    #[ORM\Column(type: Types::STRING, enumType: CategoryLimitType::class, options: ['comment' => '限制类型'])]
    #[Assert\NotNull]
    #[Assert\Choice(callback: [CategoryLimitType::class, 'cases'])]
    private CategoryLimitType $type = CategoryLimitType::BUY_TOTAL;

    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '备注'])]
    #[Assert\Length(max: 255)]
    private ?string $remark = null;

    #[UpdateUserColumn]
    #[ORM\ManyToOne(targetEntity: BizUser::class)]
    #[ORM\JoinColumn(name: 'update_user', referencedColumnName: 'id', nullable: true)]
    private ?BizUser $updateUser = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryId(): string
    {
        return $this->categoryId;
    }

    public function setCategoryId(string $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function getType(): CategoryLimitType
    {
        return $this->type;
    }

    public function setType(CategoryLimitType $type): void
    {
        $this->type = $type;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    public function getUpdateUser(): ?BizUser
    {
        return $this->updateUser;
    }

    public function setUpdateUser(?BizUser $updateUser): void
    {
        $this->updateUser = $updateUser;
    }

    public function __toString(): string
    {
        if (null === $this->id) {
            return '';
        }

        return sprintf('分类限制规则 #%d - 分类:%s - %s', $this->id, $this->categoryId, $this->type->value);
    }
}
