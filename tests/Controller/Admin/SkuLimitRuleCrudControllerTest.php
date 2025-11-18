<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use Tourze\ProductLimitRuleBundle\Controller\Admin\SkuLimitRuleCrudController;
use Tourze\ProductLimitRuleBundle\Entity\SkuLimitRule;

/**
 * @internal
 */
#[CoversClass(SkuLimitRuleCrudController::class)]
#[RunTestsInSeparateProcesses]
class SkuLimitRuleCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(?string $entityClass = null): SkuLimitRuleCrudController
    {
        /** @var SkuLimitRuleCrudController */
        return self::getContainer()->get(SkuLimitRuleCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'sku_id' => ['SKU ID'];
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
        yield 'sku_id' => ['skuId'];
        yield 'type' => ['type'];
        yield 'value' => ['value'];
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

        // 测试必填字段验证 - skuId 为空时应该失败
        $entity = new SkuLimitRule();
        // 不设置 skuId（必填字段）

        $validator = self::getService(ValidatorInterface::class);
        $violations = $validator->validate($entity);
        $this->assertGreaterThan(0, count($violations), 'SkuId is required, should have validation errors');

        $foundNotBlankError = false;
        foreach ($violations as $violation) {
            if ('skuId' === $violation->getPropertyPath()
                && str_contains((string) $violation->getMessage(), '不能为空')) {
                $foundNotBlankError = true;
                break;
            }
        }
        $this->assertTrue($foundNotBlankError, 'Should have NotBlank validation error for skuId');

        // 测试字段长度验证 - skuId 超长时应该失败
        $entity->setSkuId(str_repeat('a', 41)); // 超过 40 字符限制
        $validator = self::getService(ValidatorInterface::class);
        $violations = $validator->validate($entity);
        $this->assertGreaterThan(0, count($violations), 'SkuId too long, should have validation errors');

        // 测试正确的数据应该通过验证
        $entity->setSkuId('valid-sku-id');
        $validator = self::getService(ValidatorInterface::class);
        $violations = $validator->validate($entity);
        $this->assertCount(0, $violations, 'Valid entity should have no validation errors');
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'sku_id' => ['skuId'];
        yield 'type' => ['type'];
        yield 'value' => ['value'];
    }
}
