<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineIpBundle\Traits\CreatedFromIpAware;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\ProductLimitRuleBundle\Enum\SkuLimitType;
use Tourze\ProductLimitRuleBundle\Repository\SkuLimitRuleRepository;

/**
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: SkuLimitRuleRepository::class)]
#[ORM\Table(name: 'product_sku_limit_rule', options: ['comment' => '产品SKU限购限制'])]
#[ORM\UniqueConstraint(name: 'product_sku_limit_rule_idx_unique', columns: ['sku_id', 'type'])]
class SkuLimitRule implements \Stringable, AdminArrayInterface
{
    use BlameableAware;
    use TimestampableAware;
    use SnowflakeKeyAware;
    use CreatedFromIpAware;

    #[Ignore]
    #[Assert\NotBlank(message: 'SKU ID 不能为空')]
    #[Assert\Length(max: 40, maxMessage: 'SKU ID 不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 40, nullable: false, options: ['comment' => 'SKU ID'])]
    private string $skuId = '';

    #[Assert\Choice(callback: [SkuLimitType::class, 'cases'], message: '请选择正确的限制类型')]
    #[ORM\Column(type: Types::STRING, length: 30, enumType: SkuLimitType::class, options: ['comment' => '类型'])]
    private SkuLimitType $type = SkuLimitType::BUY_DAILY;

    #[Assert\Length(max: 150, maxMessage: '规则值不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 150, nullable: true, options: ['comment' => '规则值'])]
    private ?string $value = null;

    public function __toString(): string
    {
        if (null === $this->getId() || '' === $this->getId()) {
            return '';
        }

        return "{$this->getType()->getLabel()} {$this->getValue()}";
    }

    public function getType(): SkuLimitType
    {
        return $this->type;
    }

    public function setType(SkuLimitType $type): void
    {
        $this->type = $type;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function getSkuId(): string
    {
        return $this->skuId;
    }

    public function setSkuId(string $skuId): void
    {
        $this->skuId = $skuId;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'type' => $this->getType(),
            'value' => $this->getValue(),
            'skuId' => $this->getSkuId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }
}
