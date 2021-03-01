<?php
declare(strict_types=1);

namespace core\database\related_objects;

interface DatabaseObjectI
{

    public function encodeData(): string;
}