﻿var devNull = function () { };

/*
    Some libraries are not web-worker aware, they are either browser or Node.
    A web worker should use the browser flavor.
    So trick libs into thinking this is a browser, by existence of a 'window' in the global space.
*/
var window = {};

/* module system */

var module = {} as NodeModule;
var requireError = '';

module.require = (id: string): any => {

    if (id in module) {
        return module[id];
    }

    requireError = 'could not require module "' + id + '"';

    return null;
};

function load(id, src) {
    importScripts(src);
    module[id] = module.exports;
}

//add the makerjs module
importScripts(
    '../../target/js/browser.maker.js',
    '../../external/bezier-js/bezier.js',
    '../../external/opentype/opentype.js'
);
var makerjs: typeof MakerJs = require('makerjs');
module['makerjs'] = makerjs;
module['./../target/js/node.maker.js'] = makerjs;

function runCodeIsolated(javaScript: string) {
    var Fn: any = new Function('require', 'module', 'document', 'console', 'alert', 'playgroundRender', 'opentype', javaScript);
    var result: any = new Fn(module.require, module, mockDocument, mockConsole, devNull, playgroundRender, window['opentype']); //call function with the "new" keyword so the "this" keyword is an instance

    return module.exports || result;
}

function playgroundRender(model: MakerJs.IModel) {

    var response: MakerJsPlaygroundRender.IRenderResponse = {
        requestId: activeRequestId,
        model: model,
        html: getHtml()
    };

    postMessage(response);
}

function postError(requestId: number, error: string) {

    var response: MakerJsPlaygroundRender.IRenderResponse = {
        requestId: requestId,
        error: error
    };

    postMessage(response);
}

function getLogsHtmls() {

    var logHtmls: string[] = [];

    if (logs.length > 0) {
        logHtmls.push('<div class="section"><div class="separator"><span class="console">console:</span></div>');

        logs.forEach(function (log) {
            var logDiv = new makerjs.exporter.XmlTag('div', { "class": "console" });
            logDiv.innerText = log;
            logHtmls.push(logDiv.toString());
        });
        logHtmls.push('</div>');
    }

    return logHtmls;
}

function getHtml() {
    return htmls.concat(getLogsHtmls()).join('');
}

var mockDocument = {
    write: function (html: string) {
        htmls.push(html);
    }
};

var mockConsole = {
    log: function (entry: any) {
        switch (typeof entry) {

            case 'number':
                logs.push('' + entry);
                break;

            case 'string':
                logs.push(entry);
                break;

            default:
                logs.push(JSON.stringify(entry));
        }
    }
};

var baseHtmlLength: number;
var baseLogLength: number;
var htmls: string[];
var logs: string[];
var kit: MakerJs.IKit;
var activeRequestId: number;

onmessage = (ev: MessageEvent) => {

    var request = ev.data as MakerJsPlaygroundRender.IRenderRequest;

    if (request.orderedDependencies) {
        for (var id in request.orderedDependencies) {
            load(id, request.orderedDependencies[id]);
        }
    }

    if (requireError) {
        postError(request.requestId, requireError);
        return;
    }

    if (request.javaScript) {
        htmls = [];
        logs = [];
        kit = runCodeIsolated(request.javaScript);
        baseHtmlLength = htmls.length;
        baseLogLength = logs.length;
    }

    if (requireError) {
        postError(request.requestId, requireError);
        return;
    }

    if (!kit) {
        postError(request.requestId, 'kit was not created');

    } else {

        activeRequestId = request.requestId;
        htmls.length = baseHtmlLength;
        logs.length = baseLogLength;

        try {
            var model = makerjs.kit.construct(kit, request.paramValues);

            var response: MakerJsPlaygroundRender.IRenderResponse = {
                requestId: request.requestId,
                model: model,
                html: getHtml()
            };

            postMessage(response);

        } catch (e) {
            postError(request.requestId, 'runtime error');
        }
    }

};
