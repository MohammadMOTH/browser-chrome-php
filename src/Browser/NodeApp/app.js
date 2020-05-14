try {


    const puppeteer = require('puppeteer');

    process.on('unhandledRejection', err => {
        console.error('There was an uncaught error', err)
        process.exit(1) //mandatory (as per the Node.js docs)
    })


    var path = process.argv;
    const data = JSON.parse(path[2]);


    (async () => {
        //confinger if have proxy
        var args = [];
        ////////////////
        if (data.Proxy != null) {
            var proxyUrl = 'http://' + data.Proxy.ip + ':' + data.Proxy.port;
            var username = data.Proxy.username;
            var password = data.Proxy.password;
            args.push(`--proxy-server=${proxyUrl}`, `--no-sandbox`); // TODO تاكد منها قبل النقل
        }
        const browser = await puppeteer.launch({
            args: args
        });

        //////////////////////

        //new page
        const page = await browser.newPage();

        if (data.UserAgent != null)
            await page.setUserAgent(data.UserAgent);
        if (data.Proxy != null) {
            await page.authenticate({ username, password });
        }
        await page.setCookie(...data.Cookies);
        await page.goto(data.Url);
        ////////////
        try {

            await page.addScriptTag({ path: data.FileOutputJs }); // base js file

        }
        catch (err) {
            console.log(err.message)

        }
        ///////////
        ////////////
        try {
            if (data.JsInfoPath != null)
                await page.addScriptTag({ path: data.JsInfoPath });

        }
        catch (err) {
            console.error(err.message)
            process.exit(1);
        }
        ///////////
        for (let index = 0; index < data.JsCodePath.length; index++) {
            try {
                await page.addScriptTag({ path: data.JsCodePath[index] });
            }
            catch (err) {
                console.error(err.message)
                process.exit(1);
            }

        }
var outputX ;
        while (!await page.evaluate(() => { return window.canexit; })) {
/*  if (data.hasOwnProperty("TypeOutput") && data.TypeOutput == 1) {

                console.log("<o$$&ut>" + JSON.stringify(await page.evaluate(() => { return window.output; })) + "</o$$&ut>");
            }
*/
            if (data.hasOwnProperty("outputprint") && data.outputprint) {
                outputX = await page.evaluate(() => { return window.output; });
                console.log("<o$$&ut>" + JSON.stringify(outputX) + "</o$$&ut>");
            }

            await sleepms(1000);

        }
        if (data.SaveImage != null)
            await page.screenshot({ path: data.SaveImage });

        const output = await page.evaluate(() => { return window.output; });
        console.log("<o$$&ut>" + JSON.stringify(output) + "</o$$&ut>");

        await browser.close();
    })();
} catch (error) {
    console.error(error.message)
    process.exit();
}

function sleepms(ms) {
    return new Promise((resolve) => {
        setTimeout(resolve, ms);
    });
}
