<?php

namespace Vendasta\Vax;

/**
 * Class Environment
 * @package Vendasta\Vax
 *
 * Environment keys that can be used.
 */
abstract class Environment
{
    /**
     * Local environment
     */
    const LOCAL = "LOCAL";

    /**
     * Test environment
     */
    const TEST = "TEST";

    /**
     * Demo environment
     */
    const DEMO = "DEMO";

    /**
     * Prod environment
     */
    const PROD = "PROD";
}
