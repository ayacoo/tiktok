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
}
