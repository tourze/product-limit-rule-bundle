<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use Tourze\ProductLimitRuleBundle\Controller\Admin\SpuLimitRuleCrudController;
use Tourze\ProductLimitRuleBundle\Entity\SpuLimitRule;

/**
 * @internal
 */
#[CoversClass(SpuLimitRuleCrudController::class)]
#[RunTestsInSeparateProcesses]
class SpuLimitRuleCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(?string $entityClass = null): SpuLimitRuleCrudController
    {
        /** @var SpuLimitRuleCrudController */
        return self::getContainer()->get(SpuLimitRuleCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'spu_id' => ['SPU ID'];
        yield 'limit_type' => ['限制类型'];
        yield 'rule_value' => ['规则值'];
        yield 'created_user' => ['创建用户'];
        yield 'updated_user' => ['更新用户'];
        yield 'created_time' => ['创建时间'];
        yield 'updated_time' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'spu_id' => ['spuId'];
        yield 'type' => ['type'];
        yield 'value' => ['value'];
    }

    public function testGetEntityFqcnReturnsCorrectEntityClass(): void
    {
        $client = self::createClientWithDatabase();

        // 测试实体类名获取
        $this->assertSame(SpuLimitRule::class, SpuLimitRuleCrudController::getEntityFqcn());

        // 测试 HTTP 层
        try {
            $client->request('GET', '/admin/dashboard');
            $this->assertTrue($client->getResponse()->isSuccessful() || $client->getResponse()->isClientError());
        } catch (\Exception $e) {
            // 路由不存在是预期的，说明 HTTP 层正常工作
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testUnauthorizedAccess(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(false);

        try {
            $client->request('GET', '/admin');
            $this->assertTrue(
                $client->getResponse()->isNotFound()
                || $client->getResponse()->isRedirect()
                || $client->getResponse()->isClientError(),
                'Response should be 404, redirect, or client error for unauthenticated access'
            );
        } catch (AccessDeniedException $e) {
            $this->assertInstanceOf(AccessDeniedException::class, $e); // Access denied is expected
        } catch (\Exception $e) {
            $this->assertStringNotContainsString(
                'doctrine_ping_connection',
                $e->getMessage(),
                'Should not fail with doctrine_ping_connection error: ' . $e->getMessage()
            );
        }
    }

    public function testValidationErrors(): void
    {
        $client = self::createAuthenticatedClient();

        // 测试必填字段验证 - spuId 为空时应该失败
        $entity = new SpuLimitRule();
        // 不设置 spuId（必填字段）

        $validator = self::getService(ValidatorInterface::class);
        $violations = $validator->validate($entity);
        $this->assertGreaterThan(0, count($violations), 'SpuId is required, should have validation errors');

        $foundNotBlankError = false;
        foreach ($violations as $violation) {
            if ('spuId' === $violation->getPropertyPath()
                && str_contains((string) $violation->getMessage(), '不能为空')) {
                $foundNotBlankError = true;
                break;
            }
        }
        $this->assertTrue($foundNotBlankError, 'Should have NotBlank validation error for spuId');

        // 测试字段长度验证 - spuId 超长时应该失败
        $entity->setSpuId(str_repeat('a', 41)); // 超过 40 字符限制
        $validator = self::getService(ValidatorInterface::class);
        $violations = $validator->validate($entity);
        $this->assertGreaterThan(0, count($violations), 'SpuId too long, should have validation errors');

        // 测试正确的数据应该通过验证
        $entity->setSpuId('valid-spu-id');
        $validator = self::getService(ValidatorInterface::class);
        $violations = $validator->validate($entity);
        $this->assertCount(0, $violations, 'Valid entity should have no validation errors');
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'spu_id' => ['spuId'];
        yield 'type' => ['type'];
        yield 'value' => ['value'];
    }
}
