<?php

namespace Vendasta\Vax;

class VAXClient
{
    /**
     * $default_timeout is a number in milliseconds
     */
    private $default_timeout;

    /**
     * VAXClient constructor.
     * @param float $default_timeout
     */
    public function __construct(float $default_timeout = 10000)
    {
        $this->default_timeout = $default_timeout;
    }

    /**
     * @param array $options possible keys:
     *              \Vendasta\Vax\RequestOptions::*
     * @return Options
     */
    protected function buildVAXOptions(array $options): Options {
        $opts = new Options($this->default_timeout);
        $opts->FromOptions($options);
        return $opts;
    }

    /**
     * @param Options $opts calculated request options
     * @return float (timestamp) future timestamp at which this call should fail.
     */
    protected function getMaxCallDuration(Options $opts): float {
        return microtime(true) + (($opts->retry_options != null) ?
                ($opts->retry_options->getMaxCallDuration() / 1000) : 60);
    }

    /**
     * @param float $retryTime (milliseconds) time in which the request will retry again
     * @param float $maxTime (timestamp) when this call reaches it's maximum duration
     * @return bool whether or not to wait for the retry
     */
    protected function isRetryWithinMaxCallDuration(float $retryTime, float $maxTime): bool {
        return microtime(true) + ($retryTime / 1000) < $maxTime;
    }
}
