<?php

declare(strict_types=1);

namespace Ayacoo\Tiktok\Tests\Unit\Helper;

use Ayacoo\Tiktok\Helper\TiktokHelper;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\AbstractOEmbedHelper;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class TiktokHelperTest extends UnitTestCase
{
    private const TIKTOK_URL = 'https://www.tiktok.com/';

    private TiktokHelper $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new TiktokHelper('tiktok');
    }

    /**
     * @test
     */
    public function isAbstractOEmbedHelper(): void
    {
        self::assertInstanceOf(AbstractOEmbedHelper::class, $this->subject);
    }

    /**
     * @test
     */
    public function getOEmbedUrlReturnsUrl()
    {
        $mediaId = '123456';
        $expectedUrl = 'https://www.tiktok.com/oembed?url=https://www.tiktok.com/video/123456';

        $params = [$mediaId];
        $methodName = 'getOEmbedUrl';
        $result = $this->buildReflectionForProtectedFunction($methodName, $params);

        self::assertEquals($expectedUrl, $result);
    }

    /**
     * @test
     */
    public function getPublicUrlReturnsPublicUrl()
    {
        $properties = ['tiktok_username' => 'username123'];
        $videoId = '123456';

        $fileMock = $this->getMockBuilder(File::class)
            ->onlyMethods(['getProperties'])
            ->disableOriginalConstructor()
            ->getMock();
        $fileMock->method('getProperties')->willReturn($properties);

        $tiktokHelperMock = $this->getMockBuilder(TiktokHelper::class)
            ->onlyMethods(['getOnlineMediaId'])
            ->disableOriginalConstructor()
            ->getMock();
        $tiktokHelperMock->method('getOnlineMediaId')->with($fileMock)->willReturn($videoId);

        $result = $tiktokHelperMock->getPublicUrl($fileMock);
        $expectedUrl = 'https://www.tiktok.com/@username123/video/123456';
        self::assertEquals($expectedUrl, $result);
    }

    /**
     * @test
     */
    public function getMetaData()
    {
        $fileMock = $this->getMockBuilder(File::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Mocking the getOnlineMediaId() and getOEmbedData() methods
        $tiktokHelper = $this->getMockBuilder(TiktokHelper::class)
            ->onlyMethods(['getOnlineMediaId', 'getOEmbedData'])
            ->disableOriginalConstructor()
            ->getMock();

        $tiktokHelper->method('getOnlineMediaId')->willReturn('123456');
        $tiktokHelper->method('getOEmbedData')->willReturn([
            'width' => 640,
            'height' => 480,
            'title' => 'Test Title',
            'author_name' => 'Test Author',
            'html' => '<iframe>Test HTML</iframe>',
            'thumbnail_url' => 'https://www.example.com/thumbnail.jpg',
            'author_url' => 'https://www.tiktok.com/@testuser',
        ]);

        $expectedMetaData = [
            'width' => 640,
            'height' => 480,
            'title' => 'Test Title',
            'author' => 'Test Author',
            'tiktok_html' => '<iframe>Test HTML</iframe>',
            'tiktok_thumbnail' => 'https://www.example.com/thumbnail.jpg',
            'tiktok_author_url' => 'https://www.tiktok.com/@testuser',
            'tiktok_username' => 'testuser',
        ];

        $result = $tiktokHelper->getMetaData($fileMock);

        self::assertEquals($expectedMetaData, $result);
    }

    /**
     * @test
     */
    public function handleTiktokTitle()
    {
        $title = 'Test <span>Title</span>';
        $expectedTitle = 'Test Title';

        $params = [$title];
        $methodName = 'handleTiktokTitle';
        $result = $this->buildReflectionForProtectedFunction($methodName, $params);

        self::assertEquals($expectedTitle, $result);
    }

    /**
     * @test
     */
    public function handleTiktokTitleWithEmojis()
    {
        $title = 'Test <span>Title 😊</span>';
        $expectedTitle = 'Test Title';

        $params = [$title];
        $methodName = 'handleTiktokTitle';
        $result = $this->buildReflectionForProtectedFunction($methodName, $params);

        self::assertEquals($expectedTitle, $result);
    }

    private function buildReflectionForProtectedFunction(string $methodName, array $params)
    {
        $reflectionCalendar = new \ReflectionClass($this->subject);
        $method = $reflectionCalendar->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($this->subject, $params);
    }
}
