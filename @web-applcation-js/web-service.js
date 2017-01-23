"use strict";
var WebService = {
    Globals : {},
    ServiceName: function(svcNameValue){ (svcNameValue)!== undefined && (WebService.Globals.__service__ = svcNameValue); return WebService.Globals.__service__;},
    Cache: function(Files,Name) {  
        WebService.__cache__files__[Name === undefined ? WebService.ServiceName() : Name] =  Files;
    },
    CallEventHandle(Handle,Event) {
        return new Promise((resolve,reject) => {
            (typeof(WebService.Instance.__on__[Handle]) === 'function') ? resolve(WebService.Instance.__on__[Handle].call(WebService,Event)) : reject(Event);
        });
    },
    /**
     * @ignore
     */
    Instance: null,
    /**
     * @ignore
     */
    Run: function(Worker) {
        if(WebService.Instance === null) {
            this.__on__ = { OnLine: null, OffLine: null, Activate: null, Install: null, Fetch: null,Fallback: null };
            this.__cache__files__ = {},
            WebService.Instance = this;
            WebService.Globals.__service__ = location.host;
            Worker.addEventListener('install',function(event) {
                var handle = () => {
                    if(WebService.Instance.__cache__files__ !== undefined) {
                        for (let name in WebService.Instance.__cache__files__) {
                            (WebService.Instance.__cache__files__[name].length) && 
                                event.waitUntil( caches.open(name).then((cache) => { return cache.addAll(WebService.Instance.__cache__files__[name]); }) );
                        }
                    }
                };
                WebService.CallEventHandle('Install',event).then( handle ).catch( handle );
            });
            Worker.addEventListener('activate', event => {
                WebService.CallEventHandle('Activate',event).catch((event)=>{
                    event.waitUntil(
                        caches.keys().then( keyList => {
                            return Promise.all(keyList.map( key => {
                                if (!(key in WebService.Instance.__cache__files__)) {
                                    return caches.delete(key);
                                }}));}));
                    return clients.claim();
                });
            });

            Worker.addEventListener('fetch', (event) => {
                var response;
                WebService.CallEventHandle('Fetch',event).catch( event => {
                    event.respondWith(caches.match(event.request).catch(() => {
                        return fetch(event.request);
                    }).then(r => {
                        if(WebService.Instance.__on__.FetchAutoPutToCache !== undefined) {
                            response = r;
                            caches.open(WebService.Instance.__on__.FetchAutoPutToCache).then( cache => {
                                cache.put(event.request, response);
                            });
                            return response.clone();
                        }
                        return r;
                    }).catch(() => {
                        return (typeof(WebService.Instance.__on__.Fallback) === 'function') ? WebService.Instance.__on__.Fallback.call(WebService,event) : false;
                    }));
                });
            });
            
            Worker.addEventListener('online',event => { WebService.CallEventHandle('OnLine',event); });
            Worker.addEventListener('offline', event => { WebService.CallEventHandle('OffLine',event); });
        }
    },
    /**
     * Назначает обработчик для установки ServiceWorker, сработает только в случае наличия navigator.serviceWorker
     * @param {function} callback
     * @returns {WebService}
     */
    OnInstall: function(callback){(typeof(callback) === 'function') && (WebService.Instance.__on__.Install = callback);return WebService;},
    /**
     * Назначает обработчик, если восстановилось соединение с сетью
     * @param {function} callback
     * @returns {WebService}
     */
    OnOnLine: function(callback){(typeof(callback) === 'function') && (WebService.Instance.__on__.OnLine = callback);return WebService;},
    /**
     * Назначает обработчик, если соединение c сетью потеряно
     * @param {function} callback
     * @returns {WebService}
     */
    OnOffLine: function(callback){(typeof(callback) === 'function') && (WebService.Instance.__on__.OffLine = callback);return WebService;},
    /**
     * Вызывается когда приложение иннициализировано и готово запуститься 
     * @param {function} callback
     * @returns {WebService}
     */
    OnActivate: function(callback){(typeof(callback) === 'function') && (WebService.Instance.__on__.Activate = callback);return WebService;},
    OnFetch: function(callback,autoPutToCache){(typeof(callback) === 'function') && (WebService.Instance.__on__.Fetch = callback); WebService.Instance.__on__.FetchAutoPutToCache = (autoPutToCache === true || autoPutToCache === 1) ? WebService.ServiceName() : autoPutToCache; return WebService;},
    OnFallback: function(callback){(typeof(callback) === 'function') && (WebService.Instance.__on__.Fallback = callback);return WebService;}
};
(function(s){WebService.Run(s);})(self);