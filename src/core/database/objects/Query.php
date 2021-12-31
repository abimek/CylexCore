<?php
declare(strict_types=1);

namespace core\database\objects;

use Ramsey\Uuid\Uuid;
class Query
{

    public const MAIN_DB = 0;
    public const SERVER_DB = 1;

    /**
     * @var string
     */
    private $statement;

    /**
     * @var array|null
     */
    private $parameters;
    /**
     * @var callable
     */
    private $callable;

    /**
     * @var string
     */
    private $key;

    /**
     * @var int
     */
    private $db = 0;

    public function __construct(string $statement, ?array $parameters, callable $callable, int $type = self::MAIN_DB)
    {
        $this->parameters = $parameters;
        $this->statement = $statement;
        $this->callable = $callable;
        $this->key = Uuid::uuid4()->toString();
        $this->db = $type;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return callable
     */
    public function getCallable(): callable
    {
        return $this->callable;
    }

    /**
     * @return string
     */
    public function serializeInfo(): string
    {
        return serialize([
            "key" => $this->key,
            "statement" => $this->statement,
            "parameters" => $this->parameters,
            "db" => $this->db
        ]);
    }
}