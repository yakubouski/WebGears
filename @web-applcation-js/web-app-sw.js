/*
 *
 *  Air Horner
 *  Copyright 2015 Google Inc. All rights reserved.
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      https://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License
 *
 */

// Version 0.57

self.addEventListener('install', e => {
  e.waitUntil(
    caches.open('airhorner').then(cache => {
      return cache.addAll([
        '/',
        '/@web-application-js/web-service.js',
        '/@web-application-js/web-app.js'
    ])
      .then(() => self.skipWaiting());
    })
  )
});

self.addEventListener('activate',  event => {
  event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request).then(response => {
      return response || fetch(event.request);
    })
  );
});
/*
var _ = self;
importScripts('/@web-application-js/web-service.js');
WebService.OnInstall(function(e){
    console.log('service.OnInstall');
    this.Cache([
        '/',
        '/@web-application-js/web-service.js',
        '/@web-application-js/web-app.js'
    ]);
}).OnActivate(function(event){
    console.log('service.OnActivate');
    event.waitUntil(_.clients.claim());
}).OnFetch(function(event){
    event.respondWith(
        _.caches.match(event.request).then(response => {
          return response || fetch(event.request);
        })
      );
}).OnOnLine(function(){
    console.log('online');
}).OnOffLine(function(){
    console.log('offline');
});
            
*/