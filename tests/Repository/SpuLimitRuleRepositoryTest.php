<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\ProductLimitRuleBundle\Entity\SpuLimitRule;
use Tourze\ProductLimitRuleBundle\Enum\SpuLimitType;
use Tourze\ProductLimitRuleBundle\Repository\SpuLimitRuleRepository;

/**
 * @internal
 */
#[CoversClass(SpuLimitRuleRepository::class)]
#[RunTestsInSeparateProcesses]
final class SpuLimitRuleRepositoryTest extends AbstractRepositoryTestCase
{
    private SpuLimitRuleRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(SpuLimitRuleRepository::class);
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
        $entity = new SpuLimitRule();
        $entity->setSpuId('test-data-fixture-spu');
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
        $repository = self::getService(SpuLimitRuleRepository::class);
        $this->assertInstanceOf(SpuLimitRuleRepository::class, $repository);
    }

    public function testSave(): void
    {
        $entity = new SpuLimitRule();
        $entity->setSpuId('test-spu-save');
        $entity->setValue('10');

        $this->repository->save($entity, false);
        $this->assertGreaterThan(0, $entity->getId(), 'Entity should have a positive ID after saving');

        // 手动刷新并验证实体被持久化
        self::getEntityManager()->flush();
        $savedEntity = $this->repository->find($entity->getId());
        $this->assertInstanceOf(SpuLimitRule::class, $savedEntity);
        $this->assertSame('test-spu-save', $savedEntity->getSpuId());
        $this->assertSame(SpuLimitType::BUY_DAILY, $savedEntity->getType());
        $this->assertSame('10', $savedEntity->getValue());
    }

    public function testFindBySpuId(): void
    {
        $entity = new SpuLimitRule();
        $entity->setSpuId('test-spu-find-by-id');
        $entity->setValue('20');

        $this->repository->save($entity);

        $results = $this->repository->findBySpuId('test-spu-find-by-id');
        $this->assertCount(1, $results);
        $this->assertEquals('test-spu-find-by-id', $results[0]->getSpuId());
    }

    public function testRemove(): void
    {
        $entity = new SpuLimitRule();
        $entity->setSpuId('test-spu-remove');
        $entity->setValue('100');

        $this->repository->save($entity);
        $savedId = $entity->getId();

        $this->repository->remove($entity, false);
        $this->repository->flush();

        $found = $this->repository->find($savedId);
        $this->assertNull($found);
    }

    public function testSaveAll(): void
    {
        $entity1 = new SpuLimitRule();
        $entity1->setSpuId('test-spu-batch-1');
        $entity1->setValue('5');

        $entity2 = new SpuLimitRule();
        $entity2->setSpuId('test-spu-batch-2');
        $entity2->setValue('30');

        $entities = [$entity1, $entity2];

        $this->repository->saveAll($entities, true);

        $this->assertGreaterThan(0, $entity1->getId(), 'First entity should have a positive ID after batch saving');
        $this->assertGreaterThan(0, $entity2->getId(), 'Second entity should have a positive ID after batch saving');

        // 验证实体确实被持久化到数据库
        $savedEntity1 = $this->repository->find($entity1->getId());
        $savedEntity2 = $this->repository->find($entity2->getId());

        $this->assertInstanceOf(SpuLimitRule::class, $savedEntity1);
        $this->assertInstanceOf(SpuLimitRule::class, $savedEntity2);
        $this->assertSame('test-spu-batch-1', $savedEntity1->getSpuId());
        $this->assertSame('test-spu-batch-2', $savedEntity2->getSpuId());
    }

    public function testFlush(): void
    {
        $entity = new SpuLimitRule();
        $entity->setSpuId('test-spu-flush');
        $entity->setValue('365');

        // 保存并刷新以获得ID
        $this->repository->save($entity, true);
        $entityId = $entity->getId();
        $this->assertGreaterThan(0, $entityId, 'Entity should have ID after flush');

        // 修改实体但不刷新
        $entity->setValue('200');
        $this->repository->save($entity, false);

        // 清空缓存并重新获取，应该还是旧值
        $this->repository->clear();
        $entityFromDb = $this->repository->find($entityId);
        $this->assertInstanceOf(SpuLimitRule::class, $entityFromDb);
        $this->assertSame('365', $entityFromDb->getValue(), 'Value should still be old value before flush');

        // 重新获取实体，修改并刷新
        $entity = $this->repository->find($entityId);
        $this->assertInstanceOf(SpuLimitRule::class, $entity);
        $entity->setValue('100');
        $this->repository->save($entity, false);
        $this->repository->flush();

        // 清空缓存并重新获取，现在应该是新值
        $this->repository->clear();
        $updatedEntity = $this->repository->find($entityId);
        $this->assertInstanceOf(SpuLimitRule::class, $updatedEntity);
        $this->assertSame('100', $updatedEntity->getValue(), 'Value should be updated after flush');
    }

    public function testClear(): void
    {
        $entity = new SpuLimitRule();
        $entity->setSpuId('test-spu-clear');
        $entity->setValue('3');

        $this->repository->save($entity, true);
        $savedId = $entity->getId();

        // 清空实体管理器
        $this->repository->clear();

        // 重新获取实体，应该是分离状态的新实例
        $freshEntity = $this->repository->find($savedId);
        $this->assertInstanceOf(SpuLimitRule::class, $freshEntity);
        $this->assertNotSame($entity, $freshEntity, 'Entity should be a different instance after clear');
        $this->assertSame('test-spu-clear', $freshEntity->getSpuId());
    }

    private function cleanupTestData(): void
    {
        if (!self::getEntityManager()->isOpen() || !self::getEntityManager()->getConnection()->isConnected()) {
            return;
        }

        try {
            /** @var SpuLimitRule[] $testEntities */
            $testEntities = self::getEntityManager()->createQuery(
                'SELECT s FROM Tourze\ProductLimitRuleBundle\Entity\SpuLimitRule s WHERE s.spuId LIKE :pattern'
            )
                ->setParameter('pattern', 'test-spu-%')
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
        $entity = new SpuLimitRule();
        $entity->setSpuId('test-spu-' . uniqid());
        $entity->setValue('5');

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<SpuLimitRule>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
