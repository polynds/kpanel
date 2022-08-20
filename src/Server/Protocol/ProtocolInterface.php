<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Server\Protocol;

interface ProtocolInterface
{
    public function encode();

    public function decode($data): self;
}
