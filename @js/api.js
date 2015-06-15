var Api = {
    File: {
	Supported: function() { return (window.File && window.FileReader && window.FileList && window.Blob); },
	Bind: function(Selector){
	    var object = {
		CallbackOverride: {
		    methodChange: function(){  }
		},
		ElementSubClass: null,
		Files: null,
		Init: function(Selector){
		    $(Selector).change(function(){
			object.ElementSubClass = this;
			object.Files = this.files;
			object.CallbackOverride.methodChange.call(object);
		    });
		    return this;
		},
		Is: function(File,Mime,Size){
		    if(Mime !== undefined && !File.type.match(new RegExp(Mime,'ig'))) return false;
		    if(Size !== undefined && File.size>Size) return false;
		    return true;
		},
		/**
		 * @param {FileObject} File
		 * @returns {FileReader}
		 */
		Reader: function() {
		    var reader = new FileReader();
		    reader.Read = function(File,Method) {
			Method === undefined && (Method = 'readAsDataURL');
			this.CompleteOverride !== undefined && (this.onload = this.CompleteOverride);
			this[Method](File);
			return this;
		    };
		    reader.Complete = function(Function){reader.CompleteOverride = Function; return this; };
		    return reader;
		},
		Change: function(Function) {this.CallbackOverride.methodChange = Function; return this;}
	    };
	    return object.Init(Selector);
	}
    },
    Storage: {
	Supported: function(storage) {
	    try {
		(storage === undefined) && (storage = 'localStorage');
		return storage in window && window[storage] !== null;
	    } catch (e) {
		return false;
	    }
	},
	Enum: function(iterator,storage) {
	    (storage === undefined) && (storage = 'localStorage');
	    if (Api.Storage.Supported(storage)) {
		var items = [];
		for (var item in (storage === 'localStorage' ? localStorage : sessionStorage)) {
		    if (typeof iterator === 'function') {
			iterator(item, Api.Storage.Get(item));
		    }
		    else {
			items[items.length] = item;
		    }

		}
		return items;
	    }
	    return false;
	},
	Set: function(name, value,storage) {
	    (storage === undefined) && (storage = 'localStorage');
	    if (Api.Storage.Supported(storage)) {
		value = (value !== null ? (typeof value === 'object' ? JSON.stringify(value) : String(value)) : null);
		value !== null ? (storage === 'localStorage' ? localStorage : sessionStorage).setItem(encodeURIComponent(name), value) : (storage === 'localStorage' ? localStorage : sessionStorage).removeItem(encodeURIComponent(name));
	    }
	},
	Get: function(name, default_value,storage) {
	    (storage === undefined) && (storage = 'localStorage');
	    if (Api.Storage.Supported(storage)) {
		return (storage === 'localStorage' ? localStorage : sessionStorage)[encodeURIComponent(name)] !== undefined ? JSON.parse((storage === 'localStorage' ? localStorage : sessionStorage)[encodeURIComponent(name)]) : default_value;
	    }
	},
	Value: function(name, value, default_getvalue,storage) {
	    return value !== undefined ? Api.Storage.Set(name, value,storage) :
		    Api.Storage.Get(name, default_getvalue,storage);
	},
	Clear: function(storage) {
	    (storage === undefined) && (storage = 'localStorage');
	    Api.Storage.Supported(storage) && (storage === 'localStorage' ? localStorage : sessionStorage).clear();
	}
    },
    /**
     * Для мобильных устройств, включает вибрацию
     * @param {int} durations продолжительность вибрации, либо массив со значениями
     */
    Vibrate: function(durations){
	if("vibrate" in navigator)  return navigator.vibrate(durations);
	if("oVibrate" in navigator)  return navigator.oVibrate(durations);
	if("mozVibrate" in navigator)  return navigator.mozVibrate(durations);
	if("webkitVibrate" in navigator)  return navigator.webkitVibrate(durations);
    },
};