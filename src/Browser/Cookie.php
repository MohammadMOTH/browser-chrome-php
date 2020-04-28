<?php

namespace BrowserBotPHP\Browser\Browser;

class Cookie
{
    protected $_url;
    protected $_key;

    protected $_value;
    protected $_https;

    function __construct($url, $key, $value, bool $https = false)
    {
        $this->_url = $url;
        $this->_key = $key;

        $this->_value = $value;
        $this->_https = $https;
    }
}
