<?php

declare(strict_types=1);

namespace Ayacoo\Tiktok\Tests\Unit\Rendering;

use Ayacoo\Tiktok\Event\ModifyTiktokOutputEvent;
use Ayacoo\Tiktok\Helper\TiktokHelper;
use Ayacoo\Tiktok\Rendering\TiktokRenderer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Rendering\FileRendererInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class TiktokRendererTest extends UnitTestCase
{
    private TiktokRenderer $subject;

    protected bool $resetSingletonInstances = true;

    protected function setUp(): void
    {
        parent::setUp();

        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        $configurationManagerMock = $this->getMockBuilder(ConfigurationManager::class)
            ->onlyMethods(['getConfiguration'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->subject = new TiktokRenderer($eventDispatcherMock, $configurationManagerMock);
    }

    #[Test]
    public function hasFileRendererInterface(): void
    {
        self::assertInstanceOf(FileRendererInterface::class, $this->subject);
    }

    #[Test]
    public function canRenderWithMatchingMimeTypeReturnsTrue(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers']['tiktok'] = TiktokHelper::class;

        $fileResourceMock = $this->createMock(File::class);
        $fileResourceMock->expects(self::any())->method('getMimeType')->willReturn('video/tiktok');
        $fileResourceMock->expects(self::any())->method('getExtension')->willReturn('tiktok');

        $result = $this->subject->canRender($fileResourceMock);
        self::assertTrue($result);
    }

    #[Test]
    public function canRenderWithMatchingMimeTypeReturnsFalse(): void
    {
        $fileResourceMock = $this->createMock(File::class);
        $fileResourceMock->expects(self::any())->method('getMimeType')->willReturn('video/youtube');
        $fileResourceMock->expects(self::any())->method('getExtension')->willReturn('youtube');

        $result = $this->subject->canRender($fileResourceMock);
        self::assertFalse($result);
    }

    #[Test]
    #[DataProvider('getPrivacySettingWithExistingConfigReturnsBooleanDataProvider')]
    public function getPrivacySettingWithExistingConfigReturnsBoolean(array $pluginConfig, bool $expected)
    {
        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        $configurationManagerMock = $this->getMockBuilder(ConfigurationManager::class)
            ->onlyMethods(['getConfiguration'])
            ->disableOriginalConstructor()
            ->getMock();

        $configurationManagerMock
            ->expects(self::atLeastOnce())
            ->method('getConfiguration')
            ->with(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT)
            ->willReturn($pluginConfig);

        $subject = new TiktokRenderer($eventDispatcherMock, $configurationManagerMock);

        $params = [];
        $methodName = 'getPrivacySetting';
        $result = $this->buildReflectionForProtectedFunction($methodName, $params, $subject);

        self::assertEquals($expected, $result);
    }

    public static function getPrivacySettingWithExistingConfigReturnsBooleanDataProvider(): array
    {
        return [
            'Privacy setting true' => [
                [
                    'plugin.' => [
                        'tx_tiktok.' => [
                            'settings.' => [
                                'privacy' => true,
                            ],
                        ],
                    ],
                ],
                true,
            ],
            'Privacy setting false' => [
                [
                    'plugin.' => [
                        'tx_tiktok.' => [
                            'settings.' => [
                                'privacy' => false,
                            ],
                        ],
                    ],
                ],
                false,
            ],
            'Privacy setting non-existing' => [
                [],
                false,
            ],
        ];
    }

    #[Test]
    public function renderReturnsTiktokHtml(): void
    {
        $iframe = '<iframe src="https://www.tiktok.com" />';

        $fileResourceMock = $this->createMock(File::class);
        $fileResourceMock->expects(self::any())->method('getMimeType')->willReturn('video/tiktok');
        $fileResourceMock->expects(self::any())->method('getExtension')->willReturn('tiktok');
        $fileResourceMock->expects(self::any())->method('getProperty')->with('tiktok_html')->willReturn($iframe);

        $event = new ModifyTiktokOutputEvent($iframe);

        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $eventDispatcherMock->expects(self::once())->method('dispatch')->with(self::anything())->willReturn($event);

        $configurationManagerMock = $this->getMockBuilder(ConfigurationManager::class)
            ->onlyMethods(['getConfiguration'])
            ->disableOriginalConstructor()
            ->getMock();

        $subject = new TiktokRenderer($eventDispatcherMock, $configurationManagerMock);

        $expected = $iframe;

        $result = $subject->render($fileResourceMock, 100, 100);
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderWithPrivacyTrueReturnsModifiedTiktokHtml(): void
    {
        $iframe = '<iframe src="https://www.tiktok.com" />';
        $expected = '<iframe data-name="script-tiktok" data-src="https://www.tiktok.com" />';

        $fileResourceMock = $this->createMock(File::class);
        $fileResourceMock->expects(self::any())->method('getMimeType')->willReturn('video/tiktok');
        $fileResourceMock->expects(self::any())->method('getExtension')->willReturn('tiktok');
        $fileResourceMock->expects(self::any())->method('getProperty')->with('tiktok_html')->willReturn($iframe);

        $event = new ModifyTiktokOutputEvent($expected);
        $eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcherMock->expects(self::once())->method('dispatch')->with($event)->willReturn($event);

        $configurationManagerMock = $this->getMockBuilder(ConfigurationManager::class)
            ->onlyMethods(['getConfiguration'])
            ->disableOriginalConstructor()
            ->getMock();

        $pluginConfig = [
            'plugin.' => [
                'tx_tiktok.' => [
                    'settings.' => [
                        'privacy' => true,
                    ],
                ],
            ],
        ];

        $configurationManagerMock
            ->expects(self::atLeastOnce())
            ->method('getConfiguration')
            ->with(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT)
            ->willReturn($pluginConfig);

        $subject = new TiktokRenderer(
            $eventDispatcherMock,
            $configurationManagerMock
        );

        $result = $subject->render($fileResourceMock, 100, 100);
        self::assertSame($expected, $result);
    }

    protected function buildReflectionForProtectedFunction(
        string $methodName,
        array $params,
        TiktokRenderer $subject
    ): mixed {
        $reflectionCalendar = new \ReflectionClass($subject);
        $method = $reflectionCalendar->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($subject, $params);
    }
}
