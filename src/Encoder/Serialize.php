<?php

namespace Elixir\Cache\Encoder;

use Elixir\Cache\Encoder\EncoderInterface;

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
