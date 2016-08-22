<?php

namespace Elixir\Cache;

use Elixir\Cache\Encoder\EncoderInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
abstract class CacheAbstract implements CacheInterface, \ArrayAccess
{
    /**
     * @var EncoderInterface
     */
    protected $encoder;

    /**
     * @param EncoderInterface $value
     */
    public function setEncoder(EncoderInterface $value)
    {
        $this->encoder = $value;
    }

    /**
     * @return EncoderInterface
     */
    public function getEncoder()
    {
        return $this->encoder;
    }

    /**
     * {@inheritdoc}
     */
    public function remember($key, $value, $ttl = self::DEFAULT_TTL)
    {
        $get = $this->get($key, null);

        if (null === $get) {
            if (is_callable($value)) {
                $get = call_user_func($value);
            } else {
                $get = $value;
            }

            $this->store($key, $get, $ttl);
        }

        return $get;
    }

    /**
     * @param mixed $ttl
     *
     * @return int
     */
    protected function parseTimeToLive($ttl = self::DEFAULT_TTL)
    {
        if (0 == $ttl) {
            return self::DEFAULT_TTL;
        }

        if ($ttl instanceof \DateTime) {
            $now = time();
            $ttl = $ttl->format('U') - $now;
        } elseif (version_compare(phpversion(), '5.5', '>=') && $ttl instanceof \DateInterval) {
            $ttl = $ttl->format('U');
        } elseif (!is_numeric($ttl)) {
            $time = strtotime($ttl);

            if (false === $time) {
                return self::DEFAULT_TTL;
            }

            $now = time();
            $ttl = $time - $now;
        }

        return (int) $ttl;
    }

    /**
     * @ignore
     */
    public function offsetExists($key)
    {
        return $this->exists($key);
    }

    /**
     * @ignore
     */
    public function offsetSet($key, $value)
    {
        if (null === $key) {
            throw new \InvalidArgumentException('The key can not be undefined.');
        }

        $this->store($key, $value, self::DEFAULT_TTL);
    }

    /**
     * @ignore
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * @ignore
     */
    public function offsetUnset($key)
    {
        $this->delete($key);
    }
}
