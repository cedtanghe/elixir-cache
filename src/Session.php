<?php

namespace Elixir\Cache;

use Elixir\Session\SessionInterface;
use Elixir\STDLib\Facade\Session as SessionFacade;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Session extends CacheAbstract
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @param string           $identifier
     * @param SessionInterface $session
     */
    public function __construct($identifier = '___CACHE_SESSION___', SessionInterface $session = null)
    {
        $this->identifier = preg_replace('/[^a-z0-9\-_]+/', '', strtolower($identifier));
        $this->session = $session ?: SessionFacade::resolveInstance();
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
        return null !== $this->get($key, null);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $data = $this->session->get([$this->identifier, $key], null);

        if (null !== $data) {
            $expired = time() > (int) $data['ttl'];

            if ($expired) {
                $this->session->remove([$this->identifier, $key]);

                return is_callable($default) ? call_user_func($default) : $default;
            }

            if (null !== $this->encoder) {
                $data['value'] = $this->encoder->decode($data['value']);
            }

            return $data['value'];
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

        $this->session->set(
            [$this->identifier, $key],
            [
                'value' => $value,
                'ttl' => time() + $this->parseTimeToLive($ttl),
            ]
        );

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $this->session->remove([$this->identifier, $key]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function incremente($key, $step = 1)
    {
        $data = $this->session->get([$this->identifier, $key], null);

        if (null !== $data) {
            $expired = time() > (int) $data['ttl'];

            if ($expired) {
                $this->session->remove([$this->identifier, $key]);

                return 0;
            }

            $value = $data['value'];

            if (null !== $this->encoder) {
                $value = $this->encoder->decode($data['value']);
            }

            $value = (int) $value + $step;
            $data['value'] = $value;

            if (null !== $this->encoder) {
                $data['value'] = $this->encoder->encode($data['value']);
            }

            $this->session->set([$this->identifier, $key], $data);

            return $value;
        }

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function decremente($key, $step = 1)
    {
        $data = $this->session->get([$this->identifier, $key], null);

        if (null !== $data) {
            $expired = time() > (int) $data['ttl'];

            if ($expired) {
                $this->session->remove([$this->identifier, $key]);

                return 0;
            }

            $value = $data['value'];

            if (null !== $this->encoder) {
                $value = $this->encoder->decode($data['value']);
            }

            $value = (int) $value - $step;
            $data['value'] = $value;

            if (null !== $this->encoder) {
                $data['value'] = $this->encoder->encode($data['value']);
            }

            $this->session->set([$this->identifier, $key], $data);

            return $value;
        }

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->session->remove($this->identifier);

        return true;
    }
}
