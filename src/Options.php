<?php

namespace Vendasta\Vax;

class Options
{
    /**
     * Stored options for \RequestOptions::INCLUDE_TOKEN
     */
    public $include_token = true;

    /**
     * Stored options for \RequestOptions::TIMEOUT
     */
    public $timeout;

    /**
     * @var \Vendasta\Vax\RetryOptions $retry_options Stored options for \RequestOptions::RETRY_OPTIONS
     */
    public $retry_options;

    /**
     * Options constructor.
     * @param float $default_timeout
     * @param bool $include_token
     * @param RetryOptions $retry_options
     */
    public function __construct(float $default_timeout, bool $include_token = null, RetryOptions $retry_options = null)
    {
        $this->timeout = $default_timeout;
        $this->include_token = ($include_token != null) ? $include_token : true;
        $this->retry_options = $retry_options;
    }

    /**
     * @param array $options possible keys:
     *              \Vendasta\Vax\RequestOptions::*
     * @return Options
     */
    public function FromOptions(array $options)
    {
        if (array_key_exists(RequestOptions::TIMEOUT, $options)) {
            $this->timeout = $options[RequestOptions::TIMEOUT];
        }
        if (array_key_exists(RequestOptions::INCLUDE_TOKEN, $options)) {
            $this->include_token = $options[RequestOptions::INCLUDE_TOKEN];
        }
        if (array_key_exists(RequestOptions::RETRY_OPTIONS, $options)) {
            $this->retry_options = $options[RequestOptions::RETRY_OPTIONS];
        }
    }
}
