<?php

namespace Vendasta\Vax;

/**
 * Class RequestOptions
 * @package Vendasta\Vax
 *
 * Possible options that can be used with any VAX Request.
 */
final class RequestOptions
{
    /**
     * timeout: (float, default=10000) Float describing the timeout of the
     * request in milliseconds. Use 0 to wait indefinitely.
     */
    const TIMEOUT = 'timeout';

    /**
     * include_token: (bool, default=true) Bool describing whether or not
     * to include the authorization token on the request.
     */
    const INCLUDE_TOKEN = 'include_token';

    /**
     * include_token: (RetryOptions, default=null) Class describing retry behaviour.
     * Look at Vendasta\Vax\RetryOptions for more information.
     */
    const RETRY_OPTIONS = 'retry_options';
}
