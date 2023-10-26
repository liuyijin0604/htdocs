#!/usr/bin/node
const express = require('express');
const puppeteer = require("puppeteer-extra");
const PORT = 8082;

// add stealth plugin and use defaults (all evasion techniques)
const pluginStealth = require("puppeteer-extra-plugin-stealth");
puppeteer.use(pluginStealth());

const app = express();
global.rcount = 0;
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

app.get('/track', async (request, response) => {
	const tn = request.query.tn;
	if(!tn)	return response.status(400).send('Please provide a tracking number. Example: ?tn=AMQ0123456');
	const tnc = (tn.match(/,/g) || []).length + 1;
	const fc = request.query.fc || '01151';
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
	try{
		let data = {};
		page.removeAllListeners('response').on('response', async res => {
			if(res.url().indexOf('restapi/track') > 0 && res.ok()){
				const j = await res.json();
				for(i in j.dat){
					if(j.dat[i]['delay'] == 0){
						data[j.dat[i].no] = j.dat[i].track.z1;
						if(Object.keys(data).length >= tnc) page.goto('about:blank');
					}
				}
			}
		});

		await page.goto('https://t.17track.net/en#nums='+tn+'&fc='+fc,{
			timeout: 2e4,
			waitUntil: 'networkidle0',
		});
		response.type('application/json').send(data);
		console.log(rid, tn, process.hrtime(response.locals.pt).join('.'));
		inuse[wid] = 0;
		page.goto('about:blank');
	}catch (err) {
		console.error(err.message);
		inuse[wid] = 0;
		page.goto('about:blank');
		return response.status(500).send('Internal Server Error');
	}
});

app.listen(PORT, function() {
	console.log(`App is listening on port ${PORT}`);
	console.log('Launch Puppeteer');
	puppeteer.launch({
		// dumpio: true,
		headless: true,
		args: ['--no-sandbox', '--disable-setuid-sandbox', '--proxy-server=socks5://127.0.0.1:9050'], // , '--disable-dev-shm-usage']
	}).then(async browser => {
		for(let i=0; i<worker; i++){
			try{
				console.log('Start tracking child '+(i+1));
				pages[i] = await browser.newPage();
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
