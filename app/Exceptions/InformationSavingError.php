<?php

namespace App\Exceptions;


class InformationSavingError extends \Exception {

    /**
     * @param mixed $message
     */
    public function setMessage ($message)
    {
        $this->message = $message;
    }
}