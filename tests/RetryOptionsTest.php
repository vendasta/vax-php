<?php

namespace Vendasta\VaxTest;

use PHPUnit\Framework\TestCase;
use Vendasta\Vax\RetryOptions;

class RetryOptionsTest extends TestCase
{
    public function testBackoffMaximum() {
        $opts = new RetryOptions(30000, 1000, 30000, 2, null, true);
        $cases = [1000.0, 2000.0, 4000.0, 8000.0, 16000.0, 30000.0, 30000.0, 30000.0];

        foreach ($cases as $case) {
            $actual = $opts->pause();

            self::assertEquals($case, $actual);
        }
    }
}
