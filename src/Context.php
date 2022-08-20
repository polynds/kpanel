<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel;

class Context
{
    protected static array $data = [];

    public static function get(string $id, $default = null)
    {
        return self::$data[$id] ?? $default;
    }

    public static function set(string $id, $data): bool
    {
        self::$data[$id] = $data;
        return true;
    }

    public static function setNX(string $id, $data): bool
    {
        if (self::has($id)) {
            return false;
        }
        return self::set($id, $data);
    }

    public static function pull(string $id)
    {
        $data = self::get($id);
        if (self::remove($id)) {
            return $data;
        }
        return false;
    }

    public static function has(string $id): bool
    {
        return array_key_exists($id, self::$data);
    }

    public static function remove(string $id): bool
    {
        if (self::has($id)) {
            unset(self::$data[$id]);
            return true;
        }
        return false;
    }

    public static function clear()
    {
        self::$data = [];
    }
}
