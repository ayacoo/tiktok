<?php

declare(strict_types=1);

namespace Ayacoo\Tiktok\Tests\Unit\Tca\DisplayCond;

use Ayacoo\Tiktok\Tca\DisplayCond\IsTiktok;
use PHPUnit\Framework\TestCase;

final class IsTiktokTest extends TestCase
{
    private IsTiktok $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new IsTiktok();
    }

    public function testReturnsFalseIfParametersAreEmpty(): void
    {
        self::assertFalse($this->subject->match([]));
    }

    public function testReturnsFalseIfRecordIsNotArray(): void
    {
        $params = ['record' => 'not-an-array'];
        self::assertFalse($this->subject->match($params));
    }

    public function testReturnsFalseIfTiktokHtmlIsMissing(): void
    {
        $params = ['record' => []];
        self::assertFalse($this->subject->match($params));
    }

    public function testReturnsFalseIfTiktokHtmlIsEmptyString(): void
    {
        $params = ['record' => ['tiktok_html' => '']];
        self::assertFalse($this->subject->match($params));
    }

    public function testReturnsTrueIfTiktokHtmlHasContent(): void
    {
        $params = ['record' => ['tiktok_html' => '<blockquote>...</blockquote>']];
        self::assertTrue($this->subject->match($params));
    }
}
