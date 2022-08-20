<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Lib\Logger\Handel\Formatter;

class SimpleFormatter implements FormatterInterface
{
    /** @var null|object|string */
    protected $message;

    protected array $context = [];

    private string $level;

    public function __construct(string $level, $message, array $context)
    {
        $this->message = $message;
        $this->context = $context;
        $this->level = $level;
    }

    public function handel(): string
    {
        if ($this->context) {
            return sprintf('[%s]:%s:[%s]', ucwords($this->level), (string) $this->message, var_export($this->context, true));
        }
        return sprintf('[%s]:%s', ucwords($this->level), (string) $this->message);
    }
}
