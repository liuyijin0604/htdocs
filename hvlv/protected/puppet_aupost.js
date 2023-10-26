#!/usr/bin/node
const express = require('express');
const puppeteer = require("puppeteer-extra");
const PORT = 8081;

// add stealth plugin and use defaults (all evasion techniques)
const pluginStealth = require("puppeteer-extra-plugin-stealth");
puppeteer.use(pluginStealth());

const app = express();

const eventLoopQueue = () => {
	return new Promise(resolve => setImmediate(() => resolve()));
};

global.worker = 5;
global.pages = [];
global.rcount = 0;
global.inuse = [];

app.use((request, response, next) => {
	response.locals.pt = process.hrtime();
	const url = request.query.url;
	response.set('Access-Control-Allow-Origin', '*');
	next();
});

app.all('*', async (request, response, next) => {
	rcount++;
	next();
});

app.get('/aupost', async (request, response) => {
	try{
		const tn = request.query.tn;
		if(!tn)	return response.status(400).send('Please provide a tracking number. Example: ?tn=AMQ0123456');
		const rid = rcount;
		console.log(rid, new Date(), tn);
		var wid = -1;

		widloop:
		while(wid < 0){
			for(let i = 0; i<worker; i++){
				if(inuse[i] == 0){
					wid = i;
					inuse[i] = rid;
					break widloop;
				}
			}
			await eventLoopQueue();
		}

		const page = pages[wid];
		// await page.click('.search-bar input.search-bar-input', {clickCount: 3});
		await page.$eval('.search-bar input.search-bar-input', el => el.value = '');
		await page.type('.search-bar input.search-bar-input', tn.indexOf(',') < 0? tn+',000' : tn);
		await page.click('.search-bar button.search-bar-submit-button');
		inuse[wid] = 0;
		const result = await page.waitForResponse(res => res.url().indexOf('shipmentsgatewayapi/watchlist/shipments?trackingIds='+tn) > 0 && res.status() === 200);
		console.log(rid, tn, process.hrtime(response.locals.pt).join('.'));
		return response.type('application/json').send(await result.text());
	}catch (err) {
		console.error(err.message);
		return response.status(500).send('Internal Server Error');
	}
});

app.listen(PORT, function() {
	console.log(`App is listening on port ${PORT}`);
	console.log('Launch Puppeteer');
	puppeteer.launch({
		// dumpio: true,
		headless: true,
		ignoreHTTPSErrors: true,
		args: ['--no-sandbox', '--disable-setuid-sandbox', '--proxy-server=socks5://127.0.0.1:9050', '--host-rules=MAP auspost.com.au 13.239.124.193'], // , '--disable-dev-shm-usage'] // , MAP aupost.com.au 13.55.65.136
	}).then(async browser => {
		for(let i=0; i<worker; i++){
			try{
				console.log('Start tracking child '+(i+1));
				pages[i] = await browser.newPage().then(page => {
					page.goto("https://auspost.com.au/mypost/track/#/search");
					page.setDefaultTimeout(2e4);
					return page;
				});
				inuse[i] = 0;
			}catch (e){
				console.log('Timed out starting child '+i);
			}
		}
	});
});

// Make sure node server process stops if we get a terminating signal.
function processTerminator(sig) {
	if (typeof sig === 'string') {
		process.exit(1);
	}
	console.log('%s: Node server stopped.', Date(Date.now()));
}

const signals = [
	'SIGHUP', 'SIGINT', 'SIGQUIT', 'SIGILL', 'SIGTRAP', 'SIGABRT', 'SIGBUS',
	'SIGFPE', 'SIGUSR1', 'SIGSEGV', 'SIGUSR2', 'SIGTERM'];
signals.forEach(sig => {
	process.once(sig, () => processTerminator(sig));
});
