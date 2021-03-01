<?php
declare(strict_types=1);

namespace core\main\data\formatter;

trait BooleanFormatter
{

    public function encodeBool(bool $bool): int
    {
        return ($bool) ? 1 : 0;
    }

    public function decodeBool(int $bool): bool
    {
        return ($bool === 1) ? true : false;
    }

    public function boolToString(bool $bool): string
    {
        return ($bool) ? "true" : "false";
    }

}