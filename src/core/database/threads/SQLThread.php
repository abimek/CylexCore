<?php
declare(strict_types=1);

namespace core\database\threads;

use core\database\objects\Query;
use Exception;
use mysqli;
use pocketmine\utils\UUID;
use Threaded;

class SQLThread extends Thread
{

    private $inputThreaded;
    private $outputThreaded;
    private $closeThreaded;


    public function __construct(array $data)
    {
        $this->inputThreaded = new Threaded();
        $this->inputThreaded[] = serialize($data);
        $this->outputThreaded = new Threaded();
        $this->closeThreaded = new Threaded();
        self::start(PTHREADS_INHERIT_NONE);
    }

    /**
     * @return Threaded
     */
    public function getOutput(): Threaded
    {
        return $this->outputThreaded;
    }

    /**
     * @param Query $query
     * @param callable $callable
     */
    public function query(Query $query)
    {
        $this->inputThreaded[] = $query->serializeInfo();
    }

    public function close()
    {
        $this->closeThreaded[] = serialize(true);
    }

    public function emptyQuery(string $statement, ?array $parameters, int $dbType)
    {
        $key = UUID::fromRandom()->toString();
        $this->inputThreaded[] = serialize([
            "key" => $key,
            "statement" => $statement,
            "parameters" => $parameters,
            "db" => $dbType
        ]);
    }

    /**
     * Runs code on the thread.
     */
    public function run(): void
    {
        $input = $this->inputThreaded->shift();
        $input = unserialize($input);
        $conn = new mysqli("140.82.11.202", "TbLAVM@Snh1sXwv.s@!Z=irE", "u884_dg2h4KJtE4", "s884_elementalfac");
        $conn2 = new mysqli($input[0], $input[1], $input[2], $input[3]);
        if ($conn->connect_error) {
            throw new Exception("Database Connection has failed: " . $conn->connect_error);
        }
        while (true) {
            while (($input = $this->inputThreaded->shift()) !== null) {
                $data = unserialize($input);
                $key = $data["key"];
                $statement = $data["statement"];
                $parameters = $data["parameters"];
                $db = $data["db"];
                if ($db === 0) {
                    $result = null;
                    if ($parameters === null) {
                        $v = $conn->query($statement);
                        if ($v === false) {
                            throw new Exception("An error occurred while doing a query: " . $conn->error);
                        }
                    } else {
                        $stmt = $conn->prepare($statement);
                        if ($stmt === false) {
                            throw new Exception("An error occurred while doing a query: " . $conn->error);
                        }
                        $str = "";
                        $p = [];
                        foreach ($parameters as $parameter) {
                            if (is_string($parameter)) {
                                $str .= "s";
                            }
                            if (is_int($parameter)) {
                                $str .= "i";
                            }
                            if (is_double($parameter)) {
                                $str .= "d";
                            }
                            $p[] = $parameter;
                        }
                        if (!empty($p)) {
                            $stmt->bind_param($str, ...$p);
                        }
                        $stmt->execute();
                        $stmt_result = $stmt->get_result();
                        $columns = [];
                        if (!is_bool($stmt_result) && $stmt_result->num_rows > 0) {
                            while ($row = $stmt_result->fetch_assoc()) {
                                $columns[] = $row;
                            }
                        }
                        $result = $columns;
                    }
                    $output = [];
                    $output["key"] = $key;
                    $output["data"] = $result;
                    $this->outputThreaded[] = serialize($output);
                } else {
                    $result = null;
                    if ($parameters === null) {
                        $v = $conn2->query($statement);
                        if ($v === false) {
                            throw new DatabaseException("An error occurred while doing a query: " . $conn2->error);
                        }
                    } else {
                        $stmt = $conn2->prepare($statement);
                        if ($stmt === false) {
                            throw new DatabaseException("An error occurred while doing a query: " . $conn2->error);
                        }
                        $str = "";
                        $p = [];
                        foreach ($parameters as $parameter) {
                            if (is_string($parameter)) {
                                $str .= "s";
                            }
                            if (is_int($parameter)) {
                                $str .= "i";
                            }
                            if (is_double($parameter)) {
                                $str .= "d";
                            }
                            $p[] = $parameter;
                        }
                        if (!empty($p)) {
                            $stmt->bind_param($str, ...$p);
                        }
                        $stmt->execute();
                        $stmt_result = $stmt->get_result();
                        $columns = [];
                        if (!is_bool($stmt_result) && $stmt_result->num_rows > 0) {
                            while ($row = $stmt_result->fetch_assoc()) {
                                $columns[] = $row;
                            }
                        }
                        $result = $columns;
                    }
                    $output = [];
                    $output["key"] = $key;
                    $output["data"] = $result;
                    $this->outputThreaded[] = serialize($output);
                }
            }
            foreach ($this->closeThreaded as $object) {
                return;
            }
            usleep(10000);
        }
    }
}