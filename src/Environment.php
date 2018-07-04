<?php

namespace Vendasta\Vax;


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
