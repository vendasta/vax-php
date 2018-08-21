<?php

namespace Vendasta\VaxTest;

use Vendasta\Vax\RetryOptions;
use PHPUnit\Framework\TestCase;
use Vendasta\Vax\Options;
use Vendasta\Vax\VAXClient;

class VAXTestClient extends VAXClient
{
    public function __construct(float $default_timeout = 10000)
    {
        parent::__construct($default_timeout);
    }

    public function buildVAXOptions(array $options): Options {
        return parent::buildVAXOptions($options);
    }

    public function getMaxCallDuration(Options $opts): float {
        return parent::getMaxCallDuration($opts);
    }

    public function isRetryWithinMaxCallDuration(float $retryTime, float $maxTime): bool {
        return parent::isRetryWithinMaxCallDuration($retryTime, $maxTime);
    }
}

class VAXClientTest extends TestCase
{
    public function testGetMaxCallDuration() {
        $cases = [
            [
                "name" => "Returns 60 seconds if opts doesn't have retry options",
                "opts" => new Options(10000),
                "expected_offset" => 60,
            ],
            [
                "name" => "Returns max call duration when set on opts",
                "opts" => new Options(10000, null, new RetryOptions(30500)),
                "expected_offset" => 30.5,
            ]
        ];

        $client = new VAXTestClient();

        foreach ($cases as $case) {
            self::assertEquals(
                microtime(true) + $case['expected_offset'],
                $client->getMaxCallDuration($case['opts']),
                $case['name'],
                0.5
            );
        }
    }

    public function testIsRetryWithinMaxCallDuration() {
        $cases = [
            [
                "name" => "Is within max call duration: 2 seconds is before 30",
                "retry_time" => 2000,
                "max_time" => microtime(true) + 30,
                "expected" => true,
            ],
            [
                "name" => "Is not within max call duration: 2 seconds is after 1",
                "retry_time" => 2000,
                "max_time" => microtime(true) + 1,
                "expected" => false,
            ],
        ];

        $client = new VAXTestClient();

        foreach ($cases as $case) {
            self::assertEquals(
                $case['expected'],
                $client->isRetryWithinMaxCallDuration($case['retry_time'], $case['max_time']),
                $case['name']
            );
        }
    }
}
