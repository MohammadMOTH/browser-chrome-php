try {


    const puppeteer = require('puppeteer');

    process.on('unhandledRejection', err => {
        console.error('There was an uncaught error', err)
        process.exit(1) //mandatory (as per the Node.js docs)
    })


    var path = process.argv;
    const data = JSON.parse(path[2]);


    (async () => {
        const proxyUrl = 'http://' + data.Proxy.ip + ':' + data.Proxy.port;
        const username = data.Proxy.username;
        const password = data.Proxy.password;

        const browser = await puppeteer.launch({
            args: [`--proxy-server=${proxyUrl}`, `--no-sandbox`]
        });
        const page = await browser.newPage();

        if (data.UserAgent != null)
            await page.setUserAgent(data.UserAgent);

        await page.authenticate({ username, password });

        await page.setCookie(...data.Cookies);
        await page.goto(data.Url);
        ////////////
        try {

            await page.addScriptTag({ path: data.FileOutputJs }); // base js file

        }
        catch (err) {
            console.error(err.message)
            process.exit(1);
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

        while (!await page.evaluate(() => { return window.canexit; })) {

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
