<?php

declare(strict_types=1);

namespace DotTest\DebugBar\Extension;

use DebugBar\DebugBarException;
use Doctrine\DBAL\Configuration;
use Dot\DebugBar\DebugBar;
use Dot\DebugBar\Extension\DebugBarExtension;
use DotTest\DebugBar\CommonTrait;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

class DebugBarExtensionTest extends TestCase
{
    use CommonTrait;

    public function testConstructor(): void
    {
        $this->assertNull(null);
    }

    /**
     * @throws Exception
     */
    public function testGetFunctions(): void
    {
        $dotDebugBar = $this->createMock(DebugBar::class);
        $extension   = new DebugBarExtension($dotDebugBar, $this->config['application']['url']);
        $functions   = $extension->getFunctions();
        $this->assertIsArray($functions);
        $this->assertCount(3, $functions);
        $this->assertInstanceOf(TwigFunction::class, $functions[0]);
        $this->assertInstanceOf(TwigFunction::class, $functions[1]);
        $this->assertInstanceOf(TwigFunction::class, $functions[2]);
        $this->assertSame('debugBarEnabled', $functions[0]->getName());
        $this->assertSame('debugBarCss', $functions[1]->getName());
        $this->assertSame('debugBarJs', $functions[2]->getName());
    }

    /**
     * @throws Exception
     * @throws DebugBarException
     */
    public function testRenderDebugBarEnabledReturnsTrueWhenEnabled(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $dotDebugBar   = new DebugBar($configuration, [
            DebugBar::class => [],
        ]);
        $dotDebugBar->enable();

        $extension = new DebugBarExtension($dotDebugBar, $this->config['application']['url']);
        $this->assertTrue($extension->renderDebugBarEnabled());
    }

    /**
     * @throws Exception
     * @throws DebugBarException
     */
    public function testRenderDebugBarEnabledReturnsFalseWhenDisabled(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $dotDebugBar   = new DebugBar($configuration, [
            DebugBar::class => [],
        ]);
        $dotDebugBar->disable();

        $extension = new DebugBarExtension($dotDebugBar, $this->config['application']['url']);
        $this->assertFalse($extension->renderDebugBarEnabled());
    }

    /**
     * @throws DebugBarException
     * @throws Exception
     */
    public function testWillNotRenderDebugBarCssWhenDisabled(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $dotDebugBar   = new DebugBar($configuration, [
            DebugBar::class => [],
        ]);
        $dotDebugBar->disable();

        $extension = new DebugBarExtension($dotDebugBar, $this->config['application']['url']);
        $this->assertNull($extension->renderDebugBarCss());
    }

    /**
     * @throws DebugBarException
     * @throws Exception
     */
    public function testWillRenderDebugBarCssWhenEnabled(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $dotDebugBar   = new DebugBar($configuration, [
            DebugBar::class => [],
        ]);
        $dotDebugBar->enable();

        $extension = new DebugBarExtension($dotDebugBar, $this->config['application']['url']);
        $html      = $extension->renderDebugBarCss();
        $this->assertStringContainsString('dotkernel.css', $html);
        $this->assertStringContainsString('debugbar.css', $html);
    }

    /**
     * @throws DebugBarException
     * @throws Exception
     */
    public function testWillNotRenderDebugBarJsWhenDisabled(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $dotDebugBar   = new DebugBar($configuration, [
            DebugBar::class => [],
        ]);
        $dotDebugBar->disable();

        $extension = new DebugBarExtension($dotDebugBar, $this->config['application']['url']);
        $this->assertNull($extension->renderDebugBarJs());
    }

    /**
     * @throws DebugBarException
     * @throws Exception
     */
    public function testWillRenderDebugBarJsWhenEnabled(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $dotDebugBar   = new DebugBar($configuration, [
            DebugBar::class => [],
        ]);
        $dotDebugBar->enable();

        $extension = new DebugBarExtension($dotDebugBar, $this->config['application']['url']);
        $html      = $extension->renderDebugBarJs();
        $this->assertStringContainsString('var phpdebugbar = new PhpDebugBar.DebugBar();', $html);
        $this->assertStringContainsString('debugbar.js', $html);
    }
}
