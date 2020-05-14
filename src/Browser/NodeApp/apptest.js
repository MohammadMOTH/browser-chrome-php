const puppeteer = require('puppeteer');
console.log(process.argv);

const windowSet = (page, name, value) =>
    page.evaluateOnNewDocument(`
    Object.defineProperty(window, '${name}', {
      get() {
        return '${value}'
      }
    })
`)
    ;

//var fs = require('fs');
(async () => {
    const proxyUrl = 'http://168.81.230.104:3199';
    const username = 'tamimalazrak-4fbhj';
    const password = 'D49CjUnIvy';

    const browser = await puppeteer.launch({
        args: [`--proxy-server=${proxyUrl}`, `--no-sandbox`]
    });

    const page = await browser.newPage();

    await page.authenticate({ username, password });
    //await page.addScriptTag({path: 'test.js'});
    await page.goto('http://www.example.com');
 //   await page.addScriptTag({ url: 'https://code.jquery.com/jquery-3.2.1.min.js' });

  //  await page.addScriptTag({ path: 'test.js' });
   /* const title = await page.evaluate(() => {
        const $ = window.$; //otherwise the transpiler will rename it and won't work

        return $(document.querySelector("body > div > h1")).html();
    });*/ const dimensions = await page.evaluate(() => {

        return {
            width: document.documentElement.clientWidth,
            height: document.documentElement.clientHeight,
            deviceScaleFactor: window.devicePixelRatio,
            X: window.x
        };
    });
  //  await page.evaluate(() => { console.log(window.x) });
    console.log( dimensions);
   //console.log(title);

   // await sleep(3000);
    //console.log(await page.evaluate(() => { return window.x }));
    // await page.evaluate(() => console.log(`url is ${location.href}`));
    //await page.screenshot({ path: '.././beta/public/file3.png' });
    await browser.close();
})();
function sleep(ms) {
    return new Promise((resolve) => {
        setTimeout(resolve, ms);
    });
}
