<?php

namespace Elixir\Cache\Encoder;

use Elixir\Cache\Encoder\EncoderInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class JSON implements EncoderInterface
{
    /**
     * {@inheritdoc}
     */
    public function encode($value)
    {
        return json_encode($value, JSON_PRETTY_PRINT);
    }
    
    /**
     * {@inheritdoc}
     */
    public function decode($value)
    {
        return json_decode($value, true);
    }
}
