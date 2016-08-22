<?php

namespace Elixir\Cache\Encoder;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Serialize implements EncoderInterface
{
    /**
     * {@inheritdoc}
     */
    public function encode($value)
    {
        return serialize($value);
    }

    /**
     * {@inheritdoc}
     */
    public function decode($value)
    {
        return unserialize($value);
    }
}
