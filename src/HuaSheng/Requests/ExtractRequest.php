<?php

namespace Weijiajia\IpProxyManager\HuaSheng\Requests;

use Weijiajia\IpProxyManager\BaseDto;
use Weijiajia\IpProxyManager\Exception\ProxyException;
use Weijiajia\IpProxyManager\HuaSheng\DTO\ExtractDto;
use Weijiajia\IpProxyManager\ProxyResponse;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;
use Weijiajia\IpProxyManager\Request;
use Saloon\Http\Response;

class ExtractRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    public function __construct(ExtractDto $dto)
    {
        parent::__construct($dto);

        if (empty($this->dto->get('session'))) {
            throw new \InvalidArgumentException("请配置代理 session");
        }
    }

    /**
     * @param Response $response
     * @return BaseDto
     * @throws \JsonException|ProxyException
     */
    public function createDtoFromResponse(Response $response): BaseDto
    {
        $data = $response->json();
        if ($data['status'] !== '0' || empty($data['list'])) {
            throw new ProxyException($response,$response->body());
        }

        $this->dto->setProxyList((new Collection($data['list']))->map(fn(array $item) => new ProxyResponse(
            host: $item['sever'] ?? null,
            port: $item['port'] ?? null,
            user: $item['user'] ?? null,
            password: $item['password'] ?? null,
            url: "http://{$item['sever']}:{$item['port']}",
            expireTime: isset($item['expire_time']) ? Carbon::parse($item['expire_time']) : null,
        )));

        return $this->dto;
    }
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/servers.php';
    }

    protected function defaultQuery(): array
    {
        return $this->dto->toQueryParameters();
    }
}
