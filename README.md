# Product Limit Rule Bundle

[English](README.md) | [中文](README.zh-CN.md)

产品限制规则管理包，用于管理 SPU 和 SKU 的限制规则。

## 功能特性

- **SPU 限制规则**：支持对 SPU 的各种限购规则配置
- **SKU 限制规则**：支持对 SKU 的各种限购规则配置
- **多种限制类型**：支持按时间、数量、互斥等多种限制类型
- **易于扩展**：基于枚举的类型系统，便于添加新的限制类型

## 安装

```bash
composer require tourze/product-limit-rule-bundle
```

## 使用方法

### 1. 注册 Bundle

在 `config/bundles.php` 中添加：

```php
return [
    // ...
    Tourze\ProductLimitRuleBundle\ProductLimitRuleBundle::class => ['all' => true],
];
```

### 2. SPU 限制规则

```php
use Tourze\ProductLimitRuleBundle\Entity\SpuLimitRule;
use Tourze\ProductLimitRuleBundle\Enum\SpuLimitType;

// 创建 SPU 每日限购规则
$limitRule = new SpuLimitRule();
$limitRule->setSpuId('spu-123');
$limitRule->setType(SpuLimitType::BUY_DAILY);
$limitRule->setValue('10'); // 每日限购10件
```

### 3. SKU 限制规则

```php
use Tourze\ProductLimitRuleBundle\Entity\SkuLimitRule;
use Tourze\ProductLimitRuleBundle\Enum\SkuLimitType;

// 创建 SKU 最低购买数量规则
$limitRule = new SkuLimitRule();
$limitRule->setSkuId('sku-456');
$limitRule->setType(SkuLimitType::MIN_QUANTITY);
$limitRule->setValue('3'); // 最低购买3件
```

## 支持的限制类型

### SPU 限制类型 (SpuLimitType)

- `BUY_TOTAL` - 总次数限购
- `BUY_YEAR` - 按年度限购
- `BUY_QUARTER` - 按季度限购
- `BUY_MONTH` - 按月度限购
- `BUY_DAILY` - 按日限购
- `SPECIFY_COUPON` - 特定优惠券购买
- `SPU_MUTEX` - SPU购买互斥
- `BUY_MONTH_STORE` - 按月度门店限购
- `BUY_QUARTER_STORE` - 按季度门店限购
- `BUY_YEAR_STORE` - 按年度门店限购
- `BUY_STORE_TOTAL` - 按门店总次数限购

### SKU 限制类型 (SkuLimitType)

- `BUY_TOTAL` - 总次数限购
- `BUY_YEAR` - 按年度限购
- `BUY_QUARTER` - 按季度限购
- `BUY_MONTH` - 按月度限购
- `BUY_DAILY` - 按日限购
- `SPECIFY_COUPON` - 特定优惠券购买
- `SKU_MUTEX` - SKU购买互斥
- `MIN_QUANTITY` - 最低购买数量

## 许可证

MIT License