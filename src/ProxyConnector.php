<?php

namespace Weijiajia\IpProxyManager;

use Saloon\Http\Connector;
use Weijiajia\IpProxyManager\Trait\HasLogger;

abstract class ProxyConnector extends Connector
{
    use HasLogger;
}
