<?php

namespace BrowserBotPHP\Browser\Browser;



/**
 * Cookie for Browser
 * Class Cookie
 */
class Cookie
{

    public $domain;
    public $expirationDate;
    public $hostOnly = false;
    public $httpOnly = true;
    public $name = "key";
    public $path = "/";
    public $sameSite = "no_restriction";
    public $secure = true;
    public $session = false;
    public $storeId = "0";
    public $value = "";
    public $id = null;
    function __construct()
    {
        if (method_exists($this, $method_name = '__construct' . func_num_args())) {
            call_user_func_array(array($this, $method_name),  func_get_args());
        }
    }
    function __construct10(
        string $domain,
        int  $expirationDate,
        bool $hostOnly,
        bool $httpOnly,
        string $name,
        string $path,
        string $sameSite,
        bool $secure,
        bool $session,
        string $storeId,
        string $value,
        string $id
    ) {
        $this->domain = $domain;
        $this->expirationDate = $expirationDate;
        $this->hostOnly = $hostOnly;
        $this->httpOnly = $httpOnly;
        $this->name = $name;
        $this->path = $path;
        $this->sameSite = $sameSite;
        $this->secure = $secure;
        $this->session = $session;
        $this->storeId = $storeId;
        $this->value =  $value;
        $this->id = $id;
    }
    function __construct3(
        string $domain,
        string $name = "key",
        string $value = ""

    ) {
        $this->domain =  $domain;
        $this->expirationDate =   time() + 172800;
        $this->name =    $name;
        $this->value = $value;
    }
    /*
     "domain": "exmple.com",
        "expirationDate": 151848484, // Math.floor(Date.now()/1000) + 172800
        "hostOnly": false,
        "httpOnly": true,
        "name": "key",
        "path": "/",
        "sameSite": "no_restriction",
        "secure": true,
        "session": false,
        "storeId": "0",
        "value": "value",
        "id": 1
        */
}
