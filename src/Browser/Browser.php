<?php

namespace BrowserBotPHP\Browser\Browser;

use Symfony\Component\Process\Process;

class Browser
{

    const PathNodeJsApp = "/NodeApp/app.js";


    public function Run()
    {

        $process = new Process(["node", __DIR__ . self::PathNodeJsApp, "test"]);
        $process->start();


        // ... do other things
        $x = 1;
        while ($process->isRunning()) {
            sleep(1);
            $x++;
            echo $x . " am wait here" . PHP_EOL;
            echo $process->getOutput() . PHP_EOL;
        }
        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        } else {

            echo $process->getOutput();
        }
    }
}
