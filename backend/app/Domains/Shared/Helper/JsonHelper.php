<?php


namespace App\Domains\Shared\Helper;

use App\Domains\Shared\Exception\JsonDecodingException;
use App\Domains\Shared\Exception\JsonEncodingException;

/**
 * A utility class to convert a JSON string to an Array
 * and vice versa.
 */
class JsonHelper
{
    /**
     * @param string $json
     * @return array
     * @throws JsonDecodingException
     */
    public static function jsonStringToArray(string $json): array
    {
        if (empty($json)) {
            throw new JsonDecodingException();
        }

        $data = @json_decode($json, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new JsonDecodingException();
        }

        return $data;
    }

    /**
     * @param array $data
     * @return string
     * @throws JsonEncodingException
     */
    public static function arrayToJsonString(array $data): string
    {
        $json = @json_encode($data);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new JsonEncodingException();
        }

        return $json;
    }
}
