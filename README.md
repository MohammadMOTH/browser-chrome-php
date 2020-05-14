# browser-chrome-php
browser headless-chrome php for laravel 



Save Image Method SavePhotoPath($path) 

Set Type Output ``` TypeOutput(int $SetType)``` **OutputEverySecandUpdate** or **OutputNramal**

Set Url  ```SetUrl()```

Set js Code Injection ```SetJsCodePath()```


Set cookie or array Cookies a``` SetCookie(Cookie) , SetCookies(Cookie[])```

Set User Agent ``` SetUserAgent()```

Get Time Start ```GetTimeStart()```

Run this method before run browser ```Before()```

Run this method after run browser ```After()```

Check if time out ```IsTimeOut()```

Check If run or not ```IsRun()```

Run Browser And Wait ```RunAndWait()```

Clear Output```ClearOutput()```

Get output as object from js ```Output($OnRun = false)```

show output Debug ```DebugOutput()```

Run browser as async ```Run()```

**How Can Install**
```
composer require symfony/process 4.0.x-dev
composer require browserbotphp/browser
composer require browserbotphp/stemplates
npm install puppeteer
```
**Example**
test3.js file as javascript 
```javascript 
var output = {}; // output object
var canexit = false; // when is true then stop browser
```
PHP Example
```PHP
    $proxy = new Proxy("168.81.230.104", 120, "Username", "Passowrd", true); //true  for Set as default 

        $browserRun =  new  BrowserRun(300); // 300 sec , time out

        $Cookie = new Cookie(
            "www.google.com",
            "SS",
   "%3Afe9f6279b37a4296539c30b49dff87cad9cf789dbd843afa4f51d785f8a06388ca3ca683dc8ff2c55fc80a9b3a22a153886cc238854473bb1135fb28417e9508"
        );
        $browserRun->SetCookie($Cookie); // Set Cookies 

        $browserRun->SetJsCodePath("./test3.js"); // run js


        $browserRun->SetUrl("https://www.google.com"); // set url

        $browserRun->SavePhotoPath('./public/test.png');  // set path photo

        $browserRun->Run(); // run browser as async

        while (ListBrowser::UpdateBrwoser()) { // update all browser
            var_dump($browserRun->DebugOutput());
            sleep(1);
        }

        var_dump($browserRun->DebugOutput()); // update Get output debug 
          while ($browserRun->IsRun()) { // check if run 
            var_dump($browserRun->DebugOutput());
             var_dump($browserRun->Output()); // get output as object
          }
```
