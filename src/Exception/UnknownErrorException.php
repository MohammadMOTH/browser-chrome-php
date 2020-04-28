<?php

namespace BrowserBotPHP\Browser\Exception;




class UnknownErrorException extends Exception
{


    public function __construct($message, Exception $previous = null)
    {

        parent::__construct("Got Unknown Error : " . $message, 1, $previous);
    }
}
