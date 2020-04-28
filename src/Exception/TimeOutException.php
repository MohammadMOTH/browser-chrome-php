<?php

namespace BrowserBotPHP\Browser\Exception;




class TimeOutException extends Exception
{


    public function __construct($message, Exception $previous = null)
    {

        parent::__construct("Time out :" . $message, 0, $previous);
    }
}
