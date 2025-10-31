<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use Tourze\ProductLimitRuleBundle\Entity\SkuLimitRule;

/**
 * @extends ServiceEntityRepository<SkuLimitRule>
 */
#[AsRepository(entityClass: SkuLimitRule::class)]
#[Autoconfigure(public: true)]
class SkuLimitRuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SkuLimitRule::class);
    }

    /**
     * 根据 SKU ID 查找限制规则
     *
     * @return SkuLimitRule[]
     */
    public function findBySkuId(string $skuId): array
    {
        return $this->findBy(['skuId' => $skuId]);
    }

    /**
     * 保存实体
     */
    public function save(SkuLimitRule $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 删除实体
     */
    public function remove(SkuLimitRule $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 批量保存
     */
    /**
     * @param SkuLimitRule[] $entities
     */
    public function saveAll(array $entities, bool $flush = true): void
    {
        foreach ($entities as $entity) {
            $this->getEntityManager()->persist($entity);
        }

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 刷新实体管理器
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * 清空实体管理器
     */
    public function clear(): void
    {
        $this->getEntityManager()->clear();
    }
}
