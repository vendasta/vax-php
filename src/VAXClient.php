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
     * Options constructor.
     * @param float $default_timeout
     */
    public function __construct(float $default_timeout)
    {
        $this->timeout = $default_timeout;
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
    }
}

class VAXClient
{
    // $default_timeout is a number in milliseconds
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
}
