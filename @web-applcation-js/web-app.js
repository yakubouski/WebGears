"use strict";
var Application = {
    Globals : {},
    /**
     * @ignore
     */
    Instance: null,
    /**
     * @ignore
     */
    Run: function() {
        if(this.__run__ === undefined) {
            this.__run__ = 1;
            this.__on__ = { OnLine: null, OffLine: null, Initialize: null, Install: null, Run: null };
            Application.Instance = this;
            (document.addEventListener !== undefined) && document.addEventListener('DOMContentLoaded',function(e){
                (typeof(Application.Instance.__on__.Initialize) === 'function') && Application.Instance.__on__.Initialize.call(Application,e);
                (typeof(Application.Instance.__on__.Install) === 'function') && ('serviceWorker' in navigator) && Application.Instance.__on__.Install.call(Application,navigator.serviceWorker);
                (typeof(Application.Instance.__on__.OnLine) === 'function') && document.addEventListener('online',function(e){
                    Application.Instance.__on__.OnLine.call(Application,e);
                });
                (typeof(Application.Instance.__on__.OffLine) === 'function') && document.addEventListener('offline',function(e){
                    Application.Instance.__on__.OffLine.call(Application,e);
                });
                (typeof(Application.Instance.__on__.Run) === 'function') && Application.Instance.__on__.Run.call(Application);
            });
        }
    },
    /**
     * Назначает обработчки срабатывающий при инициализации приложения, вызывается после загрузки всей страницы
     * @param {function} callback
     * @returns {Application}
     */
    OnInitialize: function(callback){(typeof(callback) === 'function') && (Application.Instance.__on__.Initialize = callback);return Application;},
    /**
     * Назначает обработчик для установки ServiceWorker, сработает только в случае наличия navigator.serviceWorker
     * @param {function} callback
     * @returns {Application}
     */
    OnInstall: function(callback){(typeof(callback) === 'function') && (Application.Instance.__on__.Install = callback);return Application;},
    /**
     * Назначает обработчик, если восстановилось соединение с сетью
     * @param {function} callback
     * @returns {Application}
     */
    OnOnLine: function(callback){(typeof(callback) === 'function') && (Application.Instance.__on__.OnLine = callback);return Application;},
    /**
     * Назначает обработчик, если соединение c сетью потеряно
     * @param {function} callback
     * @returns {Application}
     */
    OnOffLine: function(callback){(typeof(callback) === 'function') && (Application.Instance.__on__.OffLine = callback);return Application;},
    /**
     * Вызывается когда приложение иннициализировано и готово запуститься
     * @param {function} callback
     * @returns {Application}
     */
    OnRun: function(callback){(typeof(callback) === 'function') && (Application.Instance.__on__.Run = callback);return Application;}
};
(function(){Application.Run();})();