<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Utils;

use InvalidArgumentException;

class Json
{
    public static function encode($data, int $options = JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    {
        $json = json_encode($data, $options);
        if ($json === false) {
            self::throwJsonError(json_last_error());
        }

        return $json;
    }

    public static function decode(?string $json)
    {
        if (is_null($json)) {
            return null;
        }

        $data = json_decode($json, true);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            self::throwJsonError(json_last_error());
        }

        return $data;
    }

    private static function throwJsonError(int $code): void
    {
        switch ($code) {
            case JSON_ERROR_DEPTH:
                $msg = 'Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $msg = 'Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $msg = 'Unexpected control character found';
                break;
            case JSON_ERROR_UTF8:
                $msg = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                $msg = 'Unknown error code:' . $code;
        }

        throw new InvalidArgumentException('JSON encoding failed: ' . $msg);
    }
}
