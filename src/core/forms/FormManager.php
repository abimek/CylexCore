<?php
declare(strict_types=1);

namespace core\forms;

use core\forms\entity\listener\EntityFormListener;
use core\main\managers\Manager;

class FormManager extends Manager
{

    protected function init(): void
    {
        $this->registerListener(new EntityFormListener());
    }

    protected function close(): void
    {
        // TODO: Implement close() method.
    }
}