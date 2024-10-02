<?php

namespace Weijiajia;

use Saloon\Http\Connector;
use Weijiajia\Trait\HasLogger;

abstract class ProxyConnector extends Connector
{
    use HasLogger;
}
