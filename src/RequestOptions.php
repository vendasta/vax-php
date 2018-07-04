<?php

namespace Vendasta\Vax;

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
}
