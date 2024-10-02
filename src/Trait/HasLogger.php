<?php

namespace Weijiajia\Trait;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Saloon\Http\PendingRequest;

trait HasLogger
{
    protected bool $booted = false;

    protected ?LoggerInterface $logger = null;

    public function withLogger(LoggerInterface $logger): static
    {
        $this->logger = $logger;
        return $this;
    }

    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    protected function defaultRequestMiddle(): \Closure
    {
        return function (RequestInterface $request){
            $this->getLogger()
                ?->debug('request', [
                'method'  => $request->getMethod(),
                'uri'     => (string) $request->getUri(),
                'headers' => $request->getHeaders(),
                'body'    => (string)$request->getBody(),
            ]);
            return $request;
        };
    }

    protected function defaultResponseMiddle(): \Closure
    {
        return function (ResponseInterface $response){
            $this->getLogger()
                ?->info('response', [
                'status'  => $response->getStatusCode(),
                'headers' => $response->getHeaders(),
                'body'    => (string) $response->getBody(),
            ]);
            return $response;
        };
    }

    public function bootHasLogger(PendingRequest $pendingRequest): void
    {
        if ($this->booted || $this->getLogger() === null) {
            return;
        }

        $this->booted = true;

        $connector = $pendingRequest->getConnector();

        $connector->middleware()->onRequest($this->defaultRequestMiddle());
        $connector->middleware()->onResponse($this->defaultResponseMiddle());
    }
}
