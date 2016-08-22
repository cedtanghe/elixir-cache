<?php

namespace Elixir\Cache;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class APC extends CacheAbstract
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @param string $identifier
     *
     * @throws \RuntimeException
     */
    public function __construct($identifier = '___CACHE_APC___')
    {
        if (!(extension_loaded('apc') && ini_get('apc.enabled'))) {
            throw new \RuntimeException('APC is not available.');
        }

        $this->identifier = preg_replace('/[^a-z0-9\-_]+/', '', strtolower($identifier));
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key)
    {
        return apc_exists($this->identifier.$key);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $result = apc_fetch($this->identifier.$key, $success);

        if ($success) {
            if (null !== $this->encoder) {
                $result = $this->encoder->decode($result);
            }

            return $result;
        }

        return is_callable($default) ? call_user_func($default) : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function store($key, $value, $ttl = self::DEFAULT_TTL)
    {
        if (null !== $this->encoder) {
            $value = $this->encoder->encode($value);
        }

        return apc_store($this->identifier.$key, $value, $this->parseTimeToLive($ttl));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return apc_delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function incremente($key, $step = 1)
    {
        return apc_inc($this->identifier.$key, $step);
    }

    /**
     * {@inheritdoc}
     */
    public function decremente($key, $step = 1)
    {
        return apc_dec($this->identifier.$key, $step);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        return apc_clear_cache('user');
    }
}
