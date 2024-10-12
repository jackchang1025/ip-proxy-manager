# Ip Proxy Manager

Ip Proxy Manager 是一个 PHP Ip 代理服务管理框架,为多个代理提供商提供统一的接口

## 支持平台
- [StormProxies](https://www.stormproxies.cn/)
- [HuaSheng](https://ip.huashengdaili.com/)

## 特性

- 统一的代理服务接口
- 支持多个代理提供商 (StormProxies, HuaSheng)
- 灵活的数据传输对象 (DTO) 系统
- 集成日志功能
- 基于 Saloon HTTP 客户端库
- 支持动态代理和账号密码模式
- 易于扩展,支持添加新的代理提供商

## 要求

- PHP 8.2+
- Composer

## 安装

通过 Composer 安装 Proxy Manager:

```bash

composer require weijiajia/ip-proxy-manager
```

## 使用方法

### 花生代理示例:

```php

use Weijiajia\IpProxyManager\Stormproxies\StormConnector;
use Weijiajia\IpProxyManager\HuaSheng\HuaShengConnector;
use Weijiajia\IpProxyManager\HuaSheng\Requests\ExtractRequest;
use Weijiajia\IpProxyManager\HuaSheng\Dto\ExtractDto;
use Psr\Log\LoggerInterface;

//构建一个花生代理连接器
$connector = new HuaShengConnector();

$logger = new YourLoggerImplementation(); // 替换为您的实际日志实现

//设置日志
$connector->withLogger($logger);

//构建请求数据
$dto = new ExtractDto([
    'app_key' => 'your_app_key_here',
    'num' => 1,
    'protocol' => 'http',
]);

//构建请求
$request = new ExtractRequest($dto);
//获取代理数据
$response = $connector->send($request);

/**
 * @var Collection $proxyList
 */
$proxyList = $dto->getProxyList();
foreach ($proxyList as $proxy) {

/**
 * @var ProxyResponse $proxy
 */
    echo "Proxy: {$proxy->host}:{$proxy->port} {$proxy->expireTime}\n";
}


```


### Stormproxies 示例:

```php

use Weijiajia\IpProxyManager\Stormproxies\StormConnector;
use Weijiajia\IpProxyManager\HuaSheng\HuaShengConnector;

use Weijiajia\IpProxyManager\Stormproxies\DTO\AccountPasswordDto;
use Weijiajia\IpProxyManager\Stormproxies\Requests\AccountPasswordRequest;
use Weijiajia\IpProxyManager\Stormproxies\Requests\DynamicRequest;
use Weijiajia\IpProxyManager\Stormproxies\StormConnector;

use Psr\Log\LoggerInterface;

//构建一个花生代理连接器
$connector = new StormConnector();

$logger = new YourLoggerImplementation(); // 替换为您的实际日志实现

//设置日志
$connector->withLogger($logger);

//账号密码模式
$dto = new AccountPasswordDto([
    'username' => 'your_username',
    'password' => 'your_password',
    'host' => 'proxy_host',
]);

//构建请求
$request = new AccountPasswordRequest($dto);
$response = $connector->send($request);

//提取模式
$dto = new DynamicDto([
    'app_key' => 'your_app_key_here',
]);
$request = new DynamicRequest($dto);
$response = $connector->send($request);

/**
 * @var Collection $proxyList
 */
$proxyList = $dto->getProxyList();
foreach ($proxyList as $proxy) {
    echo "Proxy: {$proxy->host}:{$proxy->port}\n";
}

```

## 扩展

要添加新的代理提供商,请遵循以下步骤:
1. 创建一个新的连接器类,继承 `ProxyConnector`
2. 创建必要的 DTO 类,继承 `BaseDto`
3. 创建请求类,继承 Saloon 的 `Request` 类
4. 实现相应的方法来处理请求和响应

### 新增代理提供商示例 (NewProxy):

```php
// src/NewProxy/NewProxyConnector.php
namespace Weijiajia\IpProxyManager\NewProxy;

use Weijiajia\IpProxyManager\ProxyConnector;

class NewProxyConnector extends ProxyConnector
{
    protected function resolveBaseUrl(): string
    {
        return 'https://api.newproxy.com';
    }
}

// src/NewProxy/Dto/GetProxyDto.php
namespace Weijiajia\IpProxyManager\NewProxy\Dto;

use Weijiajia\IpProxyManager\BaseDto;

class GetProxyDto extends BaseDto
{
    public string $apiKey;
    public int $count;
}

// src/NewProxy/Requests/GetProxy.php
namespace Weijiajia\IpProxyManager\NewProxy\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Weijiajia\IpProxyManager\NewProxy\Dto\GetProxyDto;

class GetProxyRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(protected GetProxyDto $dto)
    {
    }

    public function resolveEndpoint(): string
    {
        return '/getproxy';
    }

    protected function defaultQuery(): array
    {
        return [
            'api_key' => $this->dto->apiKey,
            'count' => $this->dto->count,
        ];
    }
}

// 使用示例
use Weijiajia\IpProxyManager\NewProxy\NewProxyConnector;
use Weijiajia\IpProxyManager\NewProxy\Dto\GetProxyDto;
use Weijiajia\IpProxyManager\NewProxy\Requests\GetProxy;

$connector = new NewProxyConnector();
$connector->withLogger(new YourLoggerImplementation());

$dto = new GetProxyDto([
    'apiKey' => 'your_api_key_here',
    'count' => 5,
]);

$request = new GetProxyRequest($dto);
$response = $connector->send($request);

$proxyList = $dto->getProxyList();
foreach ($proxyList as $proxy) {
    echo "NewProxy: {$proxy->host}:{$proxy->port}\n";
}
```

## 参考文档
- [saloon 文档](https://docs.saloon.dev/)

## 贡献
欢迎贡献!请随时提交 pull requests 或创建 issues 来改进这个项目。

## 许可证
Proxy Manager 是开源软件,基于 [MIT 许可证](LICENSE.md)。
