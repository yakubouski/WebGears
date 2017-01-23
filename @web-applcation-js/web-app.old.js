"use strict";
const std = {
    is: {
        empty: function(val){ return val===null || val === undefined; },
        function: function(val) { return typeof val === 'function'; },
        object: function(val) { return typeof val === 'object'; },
        array: function(val) { return typeof val === 'array'; },
        null: function(val) { return val===null; }
    },
    class: function(props,object){
        this.this = function(prop){
            !std.is.empty(object) && (this.__this__ = object);
            return std.is.empty(prop) ? this.__this__ : ((prop in this.__this__) ? this.__this__[prop] : undefined);
        };
        for(let p in props) { this[p] = props[p]; (std.is.function(props[p]) && (this[p].this = this.this)); }
    },
    array: {
        extend: function (obj, options) {
            if (!std.is.empty(options)) { std.array.each(options, function (val, k) { obj[k] = val; }); }
            return obj;
        },
        each: function (ar, callback) {
            if (std.is.function(callback)) { 
                !std.is.object(ar) ? [].forEach.call(ar,callback) : callback.call(ar); 
            }
        }
    },
    dom: {
        event: function (object,event,listener) {
             (typeof listener === 'function' && object.addEventListener !== undefined) && object.addEventListener(event,listener);
        },
        on: function (object,event,listener) {
             std.dom.event(object,event,listener);
        },
        __elements_class: function(els,is_nodelist){
            return new std.class({
                class: new std.class({
                    has: function(className) {
                        let t = this.this();
                        if(!t.__nodelist__) { return t[0].classList.contains(className); }
                        let hc = [];
                        for(let i=0;i<t[0].length;i++) {hc[i] = t[0][i].classList.contains(className); }
                        return hc;  
                    },
                    toggle: function(className){ 
                        let t = this.this();
                        if(!t.__nodelist__) { t[0].classList.toggle(className); }
                        else { for(let i=0;i<t[0].length;i++) {t[0][i].classList.toggle(className); } }
                    },
                    add: function(className){ 
                        let t = this.this();
                        if(!t.__nodelist__) { t[0].classList.add(className); }
                        else { for(let i=0;i<t[0].length;i++) {t[0][i].classList.add(className); } }
                    },
                    remove: function(className){ 
                        let t = this.this();
                        if(!t.__nodelist__) { t[0].classList.remove(className); }
                        else { for(let i=0;i<t[0].length;i++) {t[0][i].classList.remove(className); } }
                    }
                },{0:els,__nodelist__:is_nodelist}),
                data: function() {
                    let t = this.this();
                    if(!t.__nodelist__) { return new t[0].dataset; }
                    let ds = [];
                    for(let i=0;i<t[0].length;i++) {ds[i] = t[0][i].dataset; }
                    return ds;  
                }
            },{0:els,__nodelist__:is_nodelist});
        },
        $elements:function(selector){
            return new std.dom.__elements_class(document.querySelectorAll(selector),true);
        },
        $element:function(selector,index){
            return new std.dom.__elements_class(std.is.empty(index) ? document.querySelector(selector) : document.querySelectorAll(selector).item(index),false);
        },
        $each: function(selector,apply){
            if(selector[0]!=='#') {
                
            }
            //let els =  ? std.dom.element(selector)
        }
    }
}

var Std = {
    isEmpty: function(val){ return val===null || val === undefined; },
    isFunction: function(val) { return typeof val === 'function'; },
    isObject: function(val) { return typeof val === 'object'; },
    isNull: function(val) { return val===null; },
    
    Array : {
        Extend: function (obj, options) {
            if (!Std.isEmpty(options)) { Std.Array.Each(options, function (val, k) { obj[k] = val; }); }
            return obj;
        },
        Each: function (ar, callback) {
            if (Std.isFunction(callback)) { [].forEach.call(ar,callback); }
        }
    }
};

var Dom = {
    Event: function (object,event,listener) {
             (typeof listener === 'function' && object.addEventListener !== undefined) && object.addEventListener(event,listener);
    },
    Attr: function(el,attr,value) {
        if(Std.isNull(value)) { el.removeAttribute(attr); }
        else if(Std.isEmpty(value)) {
            if(Std.isObject(attr)) {
                Std.Array.Each(attr,function(v,k){ el.setAttribute(k,v); });
            }
            else {
                return el.getAttribute(attr);
            }
        } else {
            el.setAttribute(attr,value);
        }
    },
    Element: function(selector) {
        return document.querySelector(selector);
    },
    Elements: function(selector) {
        return document.querySelectorAll(selector);
    },
    Create: function(element,options,innerHTML) {
        var el = document.createElement(element);
        Std.Array.extend(el,options);
        !Std.isEmpty(innerHTML) && (el.innerHTML = innerHTML);
        return el;
    }
};

var Application = new std.class({
    Run: function(){
        if(this.__run__ === undefined) {
            this.__run__ = 1;
            alert('run');
            (document.addEventListener !== undefined) && document.addEventListener('DOMContentLoaded',function(e){
                alert('loaded');
                (typeof(Application.On.Initialize) === 'function') && Application.On.Initialize.call(Application);
                (typeof(Application.On.Install) === 'function') && ('serviceWorker' in navigator) && Application.On.Install.call(Application,navigator.serviceWorker);
                (typeof(Application.On.OnLine) === 'function') && document.addEventListener('online',function(e){
                    Application.On.OnLine.call(Application,e);
                });
                (typeof(Application.On.OffLine) === 'function') && document.addEventListener('offline',function(e){
                    Application.On.OffLine.call(Application,e);
                });
                (typeof(Application.On.Run) === 'function') && Application.On.Run.call(Application);
            });
        }
    },
    On : {
        OnLine: null,
        OffLine: null,
        Initialize: null,
        Install: null,
        Run: null
    }
},{Globals:{}});
/*
var Application = {
    Instance: null,
    Initialize: function(globals){
        std.is.empty(Application.Instance) && (Application.Instance = new std.class({
            
        },{Globals: std.is.empty(globals) ? {} : globals }));
        return Application.Instance;
    },
    Storage: {
        Get: function(key,default_value) {
            return (typeof(localStorage) !== "undefined") ? (localStorage[key] !== undefined ? localStorage[key] : default_value) : default_value;
        },
        Set: function(key,value) {
            (typeof(localStorage) !== "undefined") && localStorage.setItem(key,value);
        },
        Isset: function(key) {
            return (typeof(localStorage) !== "undefined" && localStorage[key] !== undefined);
        },
        Unset: function(key) {
            (typeof(localStorage) !== "undefined") && localStorage.removeItem(key);
        },
        Clear: function() {
            (typeof(localStorage) !== "undefined") && localStorage.clear();
        }
    },
    Api: {
        Test: function(){
            alert(1434);
        },

        Worker: function(WorkerScript,OnThreadMessage) {
            return new Promise(function(success,fail){
                if(typeof(Worker) !== 'undefined') {
                    var Thread = new Worker(WorkerScript);
                    Thread.onmessage = function(event) {
                        OnThreadMessage.call(Application.Instance,event.data,event);
                    };
                    success(Thread);
                }
                fail();
            });
        },
        Notify: function(Title,Options,OnClickNotify) {
            return new Promise(function(success,fail){
                (typeof(Notification) !== 'undefined') && Notification.requestPermission().then(function(permission){
                    if(permission.toLowerCase() === 'granted') {
                        (Options.tag === undefined) && (Options.tag = window.location.hostname);
                        var notification = new Notification(Title,Options);
                        (typeof OnClickNotify === 'function') && (notification.onclick = function(e){
                            OnClickNotify.call(Application.Instance,e.currentTarget,e); 
                        });
                        success();
                    }
                },fail);
            });
        }
    },
    Require: function(url,options) {
        document.head.appendChild(Application.dom.create('script', 
            Std.Array.Extend({src: url, onload: function () {}, onreadystatechange: function () {}},options || (options = {}))));
    }
};
(function(){
    var AppMain = new Application.Main();
    Dom.Event(document,"DOMContentLoaded",function(e){
        (typeof(OnOnline) === 'function') &&  Dom.Event(document,"online",function(e){OnOnline.call(AppMain,e);});
        (typeof(OnOffline) === 'function') &&  Dom.Event(document,"offline",function(e){OnOffline.call(AppMain,e);});
        (typeof(OnInitialize) === 'function') && OnInitialize.call(AppMain,e); 
    });
    //var el = Application.dom.element('script[main]');
    //Application.require(!el ? '/js/main.js' : Application.dom.attr(el,'main'),{async: false});
})();
*/
(function(){Application.Run();})();