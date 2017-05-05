<?php
/**
 * This file is part of ruogoo.
 *
 * Created by HyanCat.
 *
 * Copyright (C) HyanCat. All rights reserved.
 */

namespace Ruogoo\ReplayAttack\Middleware;

use Closure;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Ruogoo\ReplayAttack\Exception\ReplayAttackException;

class ReplayAttack
{
    /**
     * @var \Illuminate\Contracts\Cache\Repository
     */
    private $cache;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * Expire time interval in seconds.
     * @var int
     */
    private $expire;

    public function __construct(Config $config, Cache $cache)
    {
        $this->enabled = $config->get('replay_attack.enabled');
        $this->expire  = (int)$config->get('replay_attack.expire');
        $this->cache   = $cache;
    }

    /**
     * @var \Illuminate\Http\Request
     */
    private $request;

    public function handle($request, Closure $next)
    {
        $this->request = $request;
        if (! $this->enabled) {
            return $next($request);
        }

        if (! $this->checkTimestamp()) {
            throw new ReplayAttackException();
        }
        if (! $this->checkNonce()) {
            throw new ReplayAttackException();
        }

        return $next($request);
    }

    protected function checkTimestamp(): bool
    {
        if (! $this->request->has('timestamp')) {
            return false;
        }
        $timestamp = $this->request->get('timestamp');

        return $this->isExpired($timestamp);
    }

    protected function checkNonce(): bool
    {
        if (! $this->request->has('nonce')) {
            return false;
        }
        $nonce = $this->request->get('nonce');
        $key   = 'api:nonce:' . $nonce;
        if ($this->cache->has($key)) {
            return false;
        }
        $this->cache->put($key, time(), $this->expire / 60);

        return true;
    }

    private function isExpired($timestamp): bool
    {
        return abs(time() - (int)$timestamp) <= $this->expire;
    }
}
