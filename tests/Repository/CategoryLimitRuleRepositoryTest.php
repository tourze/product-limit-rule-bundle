<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\ProductLimitRuleBundle\Entity\CategoryLimitRule;
use Tourze\ProductLimitRuleBundle\Enum\CategoryLimitType;
use Tourze\ProductLimitRuleBundle\Repository\CategoryLimitRuleRepository;

/**
 * @internal
 */
#[CoversClass(CategoryLimitRuleRepository::class)]
#[RunTestsInSeparateProcesses]
final class CategoryLimitRuleRepositoryTest extends AbstractRepositoryTestCase
{
    private CategoryLimitRuleRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(CategoryLimitRuleRepository::class);
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
        $entity = new CategoryLimitRule();
        $entity->setCategoryId('test-category-' . uniqid());
        $entity->setType(CategoryLimitType::BUY_TOTAL);
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
        $repository = self::getService(CategoryLimitRuleRepository::class);
        $this->assertInstanceOf(CategoryLimitRuleRepository::class, $repository);
    }

    public function testSave(): void
    {
        $entity = new CategoryLimitRule();
        $entity->setCategoryId('test-category-save');
        $entity->setType(CategoryLimitType::BUY_TOTAL);
        $entity->setValue('3');

        $this->repository->save($entity, true);
        $this->assertGreaterThan(0, $entity->getId(), 'Entity should have a positive ID after saving');

        // 验证实体确实被持久化到数据库
        $savedEntity = $this->repository->find($entity->getId());
        $this->assertInstanceOf(CategoryLimitRule::class, $savedEntity);
        $this->assertSame('test-category-save', $savedEntity->getCategoryId());
        $this->assertSame(CategoryLimitType::BUY_TOTAL, $savedEntity->getType());
        $this->assertSame('3', $savedEntity->getValue());
    }

    public function testFindByType(): void
    {
        $entity = new CategoryLimitRule();
        $entity->setCategoryId('test-category-type');
        $entity->setType(CategoryLimitType::BUY_YEAR);
        $entity->setValue('2');

        $this->repository->save($entity);

        $results = $this->repository->findBy(['type' => CategoryLimitType::BUY_YEAR]);
        $this->assertGreaterThanOrEqual(1, count($results));
        $this->assertTrue(
            in_array(CategoryLimitType::BUY_YEAR, array_map(fn ($r) => $r->getType(), $results), true)
        );
    }

    public function testFindByCategoryId(): void
    {
        $categoryId = 'category-123';
        $entity1 = new CategoryLimitRule();
        $entity1->setCategoryId($categoryId);
        $entity1->setType(CategoryLimitType::BUY_TOTAL);
        $entity1->setValue('10');

        $entity2 = new CategoryLimitRule();
        $entity2->setCategoryId($categoryId);
        $entity2->setType(CategoryLimitType::BUY_DAILY);
        $entity2->setValue('5');

        // 不同分类的实体
        $entity3 = new CategoryLimitRule();
        $entity3->setCategoryId('different-category');
        $entity3->setType(CategoryLimitType::BUY_MONTH);
        $entity3->setValue('8');

        $this->repository->save($entity1);
        $this->repository->save($entity2);
        $this->repository->save($entity3);
        $this->repository->flush();

        $results = $this->repository->findByCategoryId($categoryId);
        $this->assertCount(2, $results);

        foreach ($results as $result) {
            $this->assertEquals($categoryId, $result->getCategoryId());
        }
    }

    public function testRemove(): void
    {
        $entity = new CategoryLimitRule();
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
        $entity1 = new CategoryLimitRule();
        $entity1->setCategoryId('test-category-batch-1');
        $entity1->setType(CategoryLimitType::BUY_TOTAL);
        $entity1->setValue('5');

        $entity2 = new CategoryLimitRule();
        $entity2->setCategoryId('test-category-batch-2');
        $entity2->setType(CategoryLimitType::BUY_DAILY);
        $entity2->setValue('10');

        $entities = [$entity1, $entity2];

        $this->repository->saveAll($entities, true);

        $this->assertGreaterThan(0, $entity1->getId(), 'First entity should have a positive ID after batch saving');
        $this->assertGreaterThan(0, $entity2->getId(), 'Second entity should have a positive ID after batch saving');

        // 验证实体确实被持久化到数据库
        $savedEntity1 = $this->repository->find($entity1->getId());
        $savedEntity2 = $this->repository->find($entity2->getId());

        $this->assertInstanceOf(CategoryLimitRule::class, $savedEntity1);
        $this->assertInstanceOf(CategoryLimitRule::class, $savedEntity2);
        $this->assertSame('test-category-batch-1', $savedEntity1->getCategoryId());
        $this->assertSame('test-category-batch-2', $savedEntity2->getCategoryId());
    }

    public function testFlush(): void
    {
        $entity = new CategoryLimitRule();
        $entity->setCategoryId('test-category-flush');
        $entity->setType(CategoryLimitType::BUY_TOTAL);
        $entity->setValue('7');

        // 保存并刷新以获得ID
        $this->repository->save($entity, true);
        $entityId = $entity->getId();
        $this->assertGreaterThan(0, $entityId, 'Entity should have ID after flush');

        // 修改实体但不刷新
        $entity->setValue('8');
        $this->repository->save($entity, false);

        // 清空缓存并重新获取，应该还是旧值
        $this->repository->clear();
        $entityFromDb = $this->repository->find($entityId);
        $this->assertInstanceOf(CategoryLimitRule::class, $entityFromDb);
        $this->assertSame('7', $entityFromDb->getValue(), 'Value should still be old value before flush');

        // 重新获取实体，修改并刷新
        $entity = $this->repository->find($entityId);
        $this->assertInstanceOf(CategoryLimitRule::class, $entity);
        $entity->setValue('9');
        $this->repository->save($entity, false);
        $this->repository->flush();

        // 清空缓存并重新获取，现在应该是新值
        $this->repository->clear();
        $updatedEntity = $this->repository->find($entityId);
        $this->assertInstanceOf(CategoryLimitRule::class, $updatedEntity);
        $this->assertSame('9', $updatedEntity->getValue(), 'Value should be updated after flush');
    }

    public function testClear(): void
    {
        $entity = new CategoryLimitRule();
        $entity->setCategoryId('test-category-clear');
        $entity->setType(CategoryLimitType::BUY_TOTAL);
        $entity->setValue('12');

        $this->repository->save($entity, true);
        $savedId = $entity->getId();

        // 清空实体管理器
        $this->repository->clear();

        // 重新获取实体，应该是分离状态的新实例
        $freshEntity = $this->repository->find($savedId);
        $this->assertInstanceOf(CategoryLimitRule::class, $freshEntity);
        $this->assertNotSame($entity, $freshEntity, 'Entity should be a different instance after clear');
        $this->assertSame('test-category-clear', $freshEntity->getCategoryId());
    }

    private function cleanupTestData(): void
    {
        if (!self::getEntityManager()->isOpen() || !self::getEntityManager()->getConnection()->isConnected()) {
            return;
        }

        try {
            // 清理所有测试数据，因为CategoryLimitRule没有特定的标识字段
            /** @var CategoryLimitRule[] $testEntities */
            $testEntities = self::getEntityManager()->createQuery(
                'SELECT c FROM Tourze\ProductLimitRuleBundle\Entity\CategoryLimitRule c WHERE c.value LIKE :pattern'
            )
                ->setParameter('pattern', 'test-%')
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
        $entity = new CategoryLimitRule();
        $entity->setCategoryId('test-category-' . uniqid());
        $entity->setType(CategoryLimitType::BUY_TOTAL);
        $entity->setValue('test-' . uniqid());

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<CategoryLimitRule>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
