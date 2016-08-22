<?php

namespace Elixir\Cache;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface CacheInterface
{
    /**
     * @var int
     */
    const DEFAULT_TTL = 31556926;

    /**
     * @param string $key
     *
     * @return bool
     */
    public function exists($key);

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * @param string               $key
     * @param mixed                $value
     * @param int|string|\DateTime $ttl
     *
     * @return bool
     */
    public function store($key, $value, $ttl = self::DEFAULT_TTL);

    /**
     * @param string               $key
     * @param mixed                $value
     * @param int|string|\DateTime $ttl
     *
     * @return mixed
     */
    public function remember($key, $value, $ttl = self::DEFAULT_TTL);

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete($key);

    /**
     * @param string $key
     * @param int    $step
     *
     * @return int
     */
    public function incremente($key, $step = 1);

    /**
     * @param string $key
     * @param int    $step
     *
     * @return int
     */
    public function decremente($key, $step = 1);

    /**
     * @return bool
     */
    public function flush();
}
