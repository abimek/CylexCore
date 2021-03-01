<?php
declare(strict_types=1);

namespace core\main\data\formatter;

trait JsonFormatter
{

    public function encodeJson(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function decodeJson(string $data): array
    {
        return json_decode($data, true);
    }

}