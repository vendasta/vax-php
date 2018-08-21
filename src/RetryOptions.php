<?php

namespace Vendasta\Vax;


class RetryOptions
{
    /**
     * @var float $max_call_duration how long should this request retry for, defaults to 30 seconds.
     */
    private $max_call_duration;

    /**
     * @var float $initial is the initial value of the retry envelope, defaults to 1 second.
     */
    private $initial;

    /**
     * @var float $max the maximum value of the retry envelope, defaults to 30 seconds.
     */
    private $max;

    /**
     * @var float $multiplier the factor by which the retry envelope increases.
     * It should be greater than 1 and defaults to 2.
     */
    private $multiplier;

    /**
     * @var float $cur the current retry envelope
     */
    private $cur;

    /**
     * @var bool $disable_jitter disables the default randomization of backoff durations.
     */
    private $disable_jitter;

    /**
     * @var array $retry_on_codes int response codes to retry on
     * Defaults to [
     *      408, // RequestTimeout
     *      500, // InternalServerError
     *      503, // ServiceUnavailable
     * ]
     */
    private $retry_on_codes;

    /**
     * RetryOptions constructor.
     * @param float $max_call_duration (milliseconds) how long should this request retry for, defaults to 30 second
     * @param float $initial (milliseconds) initial value of the retry envelope, defaults to 1 second
     * @param float $max (milliseconds) maximum value of the retry envelope, defaults to 30 seconds
     * @param float $multiplier factor by which the retry envelope increases, defaults to 2
     * @param array $retry_on_codes ([]int) http response codes to retry on, defaults to [408, 500, 503]
     * @param bool $disable_jitter disables the default randomization of backoff durations
     */
    public function __construct(float $max_call_duration = 30000, float $initial = 1000, float $max = 30000, float $multiplier = 2, array $retry_on_codes = null,  bool $disable_jitter = false)
    {
        $this->max_call_duration = ($max_call_duration > 0) ? $max_call_duration : 30000;
        $this->initial = ($initial > 0) ? $initial : 1000;
        $this->max = ($max > 0) ? $max : 30000;
        $this->multiplier = ($multiplier > 1) ? $multiplier : 2;
        $this->disable_jitter = $disable_jitter;
        $this->cur = $this->initial;
        $this->retry_on_codes = ($retry_on_codes != null) ? $retry_on_codes : [408, 500, 503];
    }

    /**
     * Based on the settings, calculates how long a request should sleep for. The time gets longer with each
     * subsequent call.
     * @return float (milliseconds) time in which a request should wait before retrying
     */
    public function pause(): float {
        if ($this->disable_jitter) {
            $d = $this->cur;
        } else {
            $d = rand($this->cur / 2, $this->cur);
        }
        $this->cur = $this->cur * $this->multiplier;
        if ($this->cur > $this->max) {
            $this->cur = $this->max;
        }
        return $d;
    }

    /**
     * Determine if this request should retry
     * @param int $statusCode status code to check
     * @return bool Whether or not this request should retry
     */
    public function shouldRetry(int $statusCode): bool {
        return in_array($statusCode, $this->retry_on_codes);
    }

    /**
     * @return float max duration this call should last for.
     */
    public function getMaxCallDuration(): float {
        return $this->max_call_duration;
    }
}
