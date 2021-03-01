<?php
declare(strict_types=1);

namespace core\forms\entity;

use JsonSerializable;

class Button implements JsonSerializable
{


    public $data;

    /**
     * Button constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        //TODO - Ask twisted if we should make this customizable because we probably will only use it for buttons but not im not 100% sure
        $this->data = [
            "button_name" => $name,
            "data" => null,
            "mode" => 0,
            "text" => "",
            "type" => 1,
        ];
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return $this->data;
    }
}
