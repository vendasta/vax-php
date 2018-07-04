<?php
namespace Vendasta\Vax\Auth;

interface FetchAuthToken {
    public function fetchToken(): string;
}
