<?php

declare(strict_types=1);

namespace Tourze\ProductLimitRuleBundle\Tests\Service;

use Knp\Menu\FactoryInterface;
use Knp\Menu\MenuItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use Tourze\ProductLimitRuleBundle\Service\AdminMenu;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // AdminMenu测试的特殊设置可以在这里进行
    }

    public function testMenuBuilding(): void
    {
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $linkGenerator->method('getCurdListPage')
            ->willReturn('/admin/mock-url')
        ;

        // 将Mock服务注入容器
        self::getContainer()->set(LinkGeneratorInterface::class, $linkGenerator);

        $factory = $this->createMock(FactoryInterface::class);
        $factory->method('createItem')
            ->willReturnCallback(function ($name) use ($factory) {
                return new MenuItem($name, $factory);
            })
        ;

        $menuItem = new MenuItem('root', $factory);

        // 从容器获取AdminMenu服务
        $adminMenu = self::getService(AdminMenu::class);

        // 执行菜单构建
        $adminMenu($menuItem);

        // 验证菜单项被创建 - 检查子菜单是否存在
        self::assertTrue($menuItem->hasChildren());
        self::assertGreaterThan(0, $menuItem->count());
    }
}
