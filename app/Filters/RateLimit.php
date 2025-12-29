<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RateLimit implements FilterInterface
{
    protected $maxRequests = 10;
    protected $windowSeconds = 60;

    public function before(RequestInterface $request, $arguments = null)
    {
        $ip = $request->getIPAddress();
        $cache = \Config\Services::cache();

        $key = "rate_limit_{$ip}";
        $requests = $cache->get($key);

        if ($requests === null) {
            $cache->save($key, 1, $this->windowSeconds);
            return;
        }

        if ($requests >= $this->maxRequests) {
            return \Config\Services::response()
                ->setStatusCode(429)
                ->setJSON([
                    'error' => 'Too many requests',
                    'message' => 'Rate limit exceeded. Please try again later.'
                ]);
        }

        $cache->save($key, $requests + 1, $this->windowSeconds);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $response->setHeader('X-RateLimit-Limit', (string)$this->maxRequests);
        $response->setHeader('X-RateLimit-Window', (string)$this->windowSeconds);
    }
}
