<?php

namespace Elixir\Cache;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Memcached extends CacheAbstract
{
    /**
     * @var \Memcached
     */
    protected $engine;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @param string $identifier
     *
     * @throws \RuntimeException
     */
    public function __construct($identifier = '___CACHE_MEMCACHED___')
    {
        if (!class_exists('\Memcached')) {
            throw new \RuntimeException('Memcached is not available.');
        }

        $this->identifier = preg_replace('/[^a-z0-9\-_]+/', '', strtolower($identifier));
        $this->engine = new \Memcached($this->identifier);
    }

    /**
     * @ignore
     */
    public function __destruct()
    {
        $this->engine = null;
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
        if (!$this->engine->get($key)) {
            return $this->engine->getResultCode() == \Memcached::RES_NOTFOUND;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $value = $this->engine->get($key, $default);

        if (null !== $this->encoder) {
            $value = $this->encoder->decode($value);
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function store($key, $value, $ttl = self::DEFAULT_TTL)
    {
        if (null !== $this->encoder) {
            $value = $this->encoder->encode($value);
        }

        return $this->engine->set(
            $key,
            $value,
            time() + $this->parseTimeToLive($ttl)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return $this->engine->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function incremente($key, $step = 1)
    {
        return $this->engine->increment($key, $step);
    }

    /**
     * {@inheritdoc}
     */
    public function decremente($key, $step = 1)
    {
        return $this->engine->decrement($key, $step);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        return $this->engine->flush();
    }

    /**
     * @ignore
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->engine, $method], $arguments);
    }
}
