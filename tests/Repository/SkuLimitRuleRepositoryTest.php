<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\ProductLimitRuleBundle\Entity\SkuLimitRule;
use Tourze\ProductLimitRuleBundle\Enum\SkuLimitType;
use Tourze\ProductLimitRuleBundle\Repository\SkuLimitRuleRepository;

/**
 * @internal
 */
#[CoversClass(SkuLimitRuleRepository::class)]
#[RunTestsInSeparateProcesses]
final class SkuLimitRuleRepositoryTest extends AbstractRepositoryTestCase
{
    private SkuLimitRuleRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(SkuLimitRuleRepository::class);
        $this->cleanupTestData();

        // 检查当前测试是否需要 DataFixtures 数据
        $currentTest = $this->name();
        if ('testCountWithDataFixtureShouldReturnGreaterThanZero' === $currentTest) {
            // 为计数测试创建测试数据
            $this->createTestDataForCountTest();
        }
    }

    private function createTestDataForCountTest(): void
    {
        $entity = new SkuLimitRule();
        $entity->setSkuId('test-data-fixture-sku');
        $entity->setValue('10');
        $this->repository->save($entity, true);
    }

    protected function onTearDown(): void
    {
        if (self::getEntityManager()->isOpen() && self::getEntityManager()->getConnection()->isConnected()) {
            $this->cleanupTestData();
        }
    }

    public function testCanBeInstantiated(): void
    {
        $repository = self::getService(SkuLimitRuleRepository::class);
        $this->assertInstanceOf(SkuLimitRuleRepository::class, $repository);
    }

    public function testSave(): void
    {
        $entity = new SkuLimitRule();
        $entity->setSkuId('test-sku-save');
        $entity->setValue('3');

        $this->repository->save($entity, false);
        $this->assertGreaterThan(0, $entity->getId(), 'Entity should have a positive ID after saving');

        // 手动刷新并验证实体被持久化
        self::getEntityManager()->flush();
        $savedEntity = $this->repository->find($entity->getId());
        $this->assertInstanceOf(SkuLimitRule::class, $savedEntity);
        $this->assertSame('test-sku-save', $savedEntity->getSkuId());
        $this->assertSame(SkuLimitType::BUY_DAILY, $savedEntity->getType());
        $this->assertSame('3', $savedEntity->getValue());
    }

    public function testFindBySkuId(): void
    {
        $entity = new SkuLimitRule();
        $entity->setSkuId('test-sku-find-by-id');
        $entity->setValue('2');

        $this->repository->save($entity);

        $results = $this->repository->findBySkuId('test-sku-find-by-id');
        $this->assertCount(1, $results);
        $this->assertEquals('test-sku-find-by-id', $results[0]->getSkuId());
    }

    public function testRemove(): void
    {
        $entity = new SkuLimitRule();
        $entity->setSkuId('test-sku-remove');
        $entity->setValue('group-1');

        $this->repository->save($entity);
        $savedId = $entity->getId();

        $this->repository->remove($entity, false);
        $this->repository->flush();

        $found = $this->repository->find($savedId);
        $this->assertNull($found);
    }

    public function testSaveAll(): void
    {
        $entity1 = new SkuLimitRule();
        $entity1->setSkuId('test-sku-batch-1');
        $entity1->setValue('5');

        $entity2 = new SkuLimitRule();
        $entity2->setSkuId('test-sku-batch-2');
        $entity2->setValue('10');

        $entities = [$entity1, $entity2];

        $this->repository->saveAll($entities, true);

        $this->assertGreaterThan(0, $entity1->getId(), 'First entity should have a positive ID after batch saving');
        $this->assertGreaterThan(0, $entity2->getId(), 'Second entity should have a positive ID after batch saving');

        // 验证实体确实被持久化到数据库
        $savedEntity1 = $this->repository->find($entity1->getId());
        $savedEntity2 = $this->repository->find($entity2->getId());

        $this->assertInstanceOf(SkuLimitRule::class, $savedEntity1);
        $this->assertInstanceOf(SkuLimitRule::class, $savedEntity2);
        $this->assertSame('test-sku-batch-1', $savedEntity1->getSkuId());
        $this->assertSame('test-sku-batch-2', $savedEntity2->getSkuId());
    }

    public function testFlush(): void
    {
        $entity = new SkuLimitRule();
        $entity->setSkuId('test-sku-flush');
        $entity->setValue('group-1');

        // 保存并刷新以获得ID
        $this->repository->save($entity, true);
        $entityId = $entity->getId();
        $this->assertGreaterThan(0, $entityId, 'Entity should have ID after flush');

        // 修改实体但不刷新
        $entity->setValue('group-2');
        $this->repository->save($entity, false);

        // 清空缓存并重新获取，应该还是旧值
        $this->repository->clear();
        $entityFromDb = $this->repository->find($entityId);
        $this->assertInstanceOf(SkuLimitRule::class, $entityFromDb);
        $this->assertSame('group-1', $entityFromDb->getValue(), 'Value should still be old value before flush');

        // 重新获取实体，修改并刷新
        $entity = $this->repository->find($entityId);
        $this->assertInstanceOf(SkuLimitRule::class, $entity);
        $entity->setValue('group-3');
        $this->repository->save($entity, false);
        $this->repository->flush();

        // 清空缓存并重新获取，现在应该是新值
        $this->repository->clear();
        $updatedEntity = $this->repository->find($entityId);
        $this->assertInstanceOf(SkuLimitRule::class, $updatedEntity);
        $this->assertSame('group-3', $updatedEntity->getValue(), 'Value should be updated after flush');
    }

    public function testClear(): void
    {
        $entity = new SkuLimitRule();
        $entity->setSkuId('test-sku-clear');
        $entity->setValue('15');

        $this->repository->save($entity, true);
        $savedId = $entity->getId();

        // 清空实体管理器
        $this->repository->clear();

        // 重新获取实体，应该是分离状态的新实例
        $freshEntity = $this->repository->find($savedId);
        $this->assertInstanceOf(SkuLimitRule::class, $freshEntity);
        $this->assertNotSame($entity, $freshEntity, 'Entity should be a different instance after clear');
        $this->assertSame('test-sku-clear', $freshEntity->getSkuId());
    }

    private function cleanupTestData(): void
    {
        if (!self::getEntityManager()->isOpen() || !self::getEntityManager()->getConnection()->isConnected()) {
            return;
        }

        try {
            /** @var SkuLimitRule[] $testEntities */
            $testEntities = self::getEntityManager()->createQuery(
                'SELECT s FROM Tourze\ProductLimitRuleBundle\Entity\SkuLimitRule s WHERE s.skuId LIKE :pattern'
            )
                ->setParameter('pattern', 'test-sku-%')
                ->getResult()
            ;

            foreach ($testEntities as $entity) {
                self::getEntityManager()->remove($entity);
            }

            self::getEntityManager()->flush();
        } catch (\Exception $e) {
            // 忽略数据库清理失败的情况
        }
    }

    protected function createNewEntity(): object
    {
        $entity = new SkuLimitRule();
        $entity->setSkuId('test-sku-' . uniqid());
        $entity->setValue('2');

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<SkuLimitRule>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
