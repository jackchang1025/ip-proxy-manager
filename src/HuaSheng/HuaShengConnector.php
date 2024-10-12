<?php

namespace Weijiajia\IpProxyManager\HuaSheng;

use Weijiajia\IpProxyManager\ProxyConnector;
use Saloon\Traits\Plugins\AcceptsJson;

class HuaShengConnector extends ProxyConnector
{
    use AcceptsJson;
    
    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://mobile.huashengdaili.com/';
    }

    public ?int $tries = 5;
}
