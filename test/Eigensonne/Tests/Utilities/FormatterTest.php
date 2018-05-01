<?php
/**
 * Created by PhpStorm.
 * User: thao.truong
 * Date: 01.05.18
 * Time: 21:32
 */

namespace Eigensonne\Tests\Utilities;

use Eigensonne\Utilities\Formatter;
use PHPUnit\Framework\TestCase;

class FormatterTest extends TestCase {

    public function testRelativeTimeFormatter() {
        $this->assertEquals('1 minute ago', Formatter::timeIntervalRelativeToNow(time() - 65));
        $this->assertEquals('2 minutes ago', Formatter::timeIntervalRelativeToNow(time() - 120));
        $this->assertEquals('0 minute ago', Formatter::timeIntervalRelativeToNow(time() - 59));
        $this->assertEquals('1 hour ago', Formatter::timeIntervalRelativeToNow(time() - (70 * 60)));
        $this->assertEquals('2 hours ago', Formatter::timeIntervalRelativeToNow(time() - (125 * 60)));
        $this->assertEquals('1 day ago', Formatter::timeIntervalRelativeToNow(time() - (25 * 60 * 60)));
        $this->assertEquals('3 days ago', Formatter::timeIntervalRelativeToNow(time() - (74 * 60 * 60)));
        $this->assertEquals('1 month ago', Formatter::timeIntervalRelativeToNow(time() - (31 * 24 * 60 * 60)));
        $this->assertEquals('2 months ago', Formatter::timeIntervalRelativeToNow(time() - (64 * 24 * 60 * 60)));
        $this->assertEquals('1 year ago', Formatter::timeIntervalRelativeToNow(time() - (367 * 24 * 60 * 60)));
        $this->assertEquals('2 years ago', Formatter::timeIntervalRelativeToNow(time() - (750 * 24 * 60 * 60)));
    }
}
