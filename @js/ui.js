/**
 * Вычисляет интервал в днях между  датами
 * @param {DateTime} Begin
 * @param {DateTime} End
 * @returns {DateTime: begin,DateTime: end,int: days}
 */
var DateInterval = function (Begin,End) {
    Begin = DateTime(Begin);
    End = DateTime(End);
    return {
	begin: Begin,
	end: End,
	days: Math.floor((End.Date() - Begin.Date()) / 86400000)
    };
};
/**
 * 
 * @param {String|DateTime} date
 * @param {int} d
 * @param {int} m
 * @param {int} Y
 * @returns {{date,Y,N,t,m,d,H,i,s,z}}
 */
DateTime = function(date,d,m,Y) {
    if( !(date instanceof Date) ) {
	YmdHis = date.match(/(\d{4}).(\d{1,2}).(\d{1,2})\s?(\d{1,2})?.?(\d{1,2})?.?(\d{1,2})?/);
	dt = new Date(
		Y !== undefined ? Y : parseInt(YmdHis[1]),
		parseInt(m !== undefined ? m : YmdHis[2]) - 1, 
		parseInt(d !== undefined ? d : YmdHis[3]), 
		parseInt(YmdHis[4]!==undefined ? YmdHis[4] : 0), 
		parseInt(YmdHis[5] ? YmdHis[5] : 0), 
		parseInt(YmdHis[6] ? YmdHis[6] : 0)
	);
    }
    else {
	dt = new Date(
		Y !== undefined ? Y : date.getFullYear(),
		m !== undefined ? parseInt(m) - 1 : date.getMonth(),
		d !== undefined ? parseInt(d) : date.getDate()
	);
    }
    return {
	date: dt,
	Y: parseInt(dt.getFullYear()),
	N: dt.getDay() ? dt.getDay() : 7,
	t: (32 - new Date(dt.getFullYear(), dt.getMonth(), 32).getDate()),
	m: dt.getMonth()+1,
	d: dt.getDate(),
	H: dt.getHours(),
	i: dt.getMinutes(),
	s: dt.getSeconds(),
	z: Math.floor((dt - new Date(dt.getFullYear(), 0, 0)) / 86400000),

	Date: function(d,m,Y) {
	    return new Date(Y !== undefined ? Y: this.Y, m !== undefined ? m : parseInt(this.m)-1, d !== undefined ? d: this.d);
	},

	Format: function(format) {
	    return format.replace('Y',this.Y).
		    replace('N',this.N).
		    replace('t',this.t).
		    replace('m',this.m<10?('0'+this.m):this.m).
		    replace('d',this.d<10?('0'+this.d):this.d).
		    replace('H',this.H<10?('0'+this.H):this.H).
		    replace('i',this.i<10?('0'+this.i):this.i).
		    replace('s',this.s<10?('0'+this.s):this.s);
	}
    };
};
var Ui = {
    On: function(){
	$(document).on('click','.dialog',function(e){
	    e.preventDefault(); e.stopPropagation();
	    Dialog.Modal($(this).attr('href'));
	}).on('click', '.dialog-close', function(e) {
	    e.preventDefault();
	    e.stopPropagation();
	    Dialog.Close();
	}).on('click','.wait',function(){
	    Wait.Begin($(this).attr('wait'));
	}).on('click','div.ui-info>.button-close',function(){
	    $(this).closest('div.ui-info').hide(200,function(){
		$(this).remove();
	    });
	}).ready(function(){
	    //$('input[type="file"]').fileselect();
	    $('form.ajax').ajaxForm({
		beforeSubmit: function(data,form){
		    $(form).data('onbefore') !== undefined && (result = window[($(form).data('onbefore'))](data,form));
		    return (result === undefined ? true : result);
		},
		success: function(data,result,state,form){
		    $(form).data('onsuccess') !== undefined && window[($(form).data('onsuccess'))](data);
		},
		error: function(data){
		    $(this).data('onerror') !== undefined && window[($(form).data('onerror'))]();
		},
		dataType: $(this).data('result')!==undefined ? $(this).data('result') : 'html'
	    });
	});
	
	$(window).on('resize','.sizing',function(){});
	
	$.fn.fileselect = function() {
	    $(this).each(function(){
		if($(this).attr('size') !== undefined) {
		    var size = parseInt($(this).attr('size')) - 1; 
		    if(size) {
			while(size --) {
			    $(this).before($(this).clone()).addClass('empty').attr('disabled',true);
			}
			var inputFields = $('input[type="file"][name="'+$(this).attr('name')+'"]');

			$(inputFields).change(function(){
			    var eFiles = inputFields.filter(function(){ return $(this).val() === '' || $(this).val() === undefined; });
			    eFiles.addClass('empty').attr('disabled',true).filter(':first').removeClass('empty').removeAttr('disabled');
			});
		    }
		}
	    });
	};
	
	$.fn.draggable = function(opt) {
	    opt = $.extend({handle:"",cursor:"move"}, opt);
	    if(opt.handle === "") {
		var $el = this;
	    } else {
		var $el = this.find(opt.handle);
	    }

	    return $el.css('cursor', opt.cursor).on("mousedown", function(e) {
		if(opt.handle === "") {
		    var $drag = $(this).addClass('draggable');
		} else {
		    var $drag = $(this).addClass('active-handle').parent().addClass('draggable');
		}
		var z_idx = $drag.css('z-index'),
		    drg_h = $drag.outerHeight(),
		    drg_w = $drag.outerWidth(),
		    pos_y = $drag.offset().top + drg_h - e.pageY,
		    pos_x = $drag.offset().left + drg_w - e.pageX;
		$drag.css('z-index', 1000).parents().on("mousemove", function(e) {
		    $('.draggable').offset({
			top:e.pageY + pos_y - drg_h,
			left:e.pageX + pos_x - drg_w
		    }).on("mouseup", function() {
			$(this).removeClass('draggable').css('z-index', z_idx);
		    });
		});
		e.preventDefault(); // disable selection
	    }).on("mouseup", function() {
		if(opt.handle === "") {
		    $(this).removeClass('draggable');
		} else {
		    $(this).removeClass('active-handle').parent().removeClass('draggable');
		}
	    });
	};
    },
    OnResize: function(call){
	$(window).on('resize',call);
	call();
    },
    widget: function() {
	
    },
    /**
     * Получения данных в режиме простоя
     * @param {int} seconds
     * @param {string} url
     * @param {function} callback
     * @param {string} type
     */
    Idle: function(seconds, url, callback, type) {
	var timerproc = function() {
	    ((url && url !== undefined && url !== '') && $.get(url, callback, type).success(function(data) {
		callback(data);
	    })) || callback(type);
	};
	timerproc();
	setInterval(timerproc, seconds * 1000);
	return this;
    },
    /**
     * Вывод данных в лог консоли
     * @param {type} txt
     */
    Log: function(txt) {
	console.log(txt);
    },
    Location: function(Location) {
	window.location = Location;
    },
    Reload: function() {
	window.location.reload();
    },
    /**
     * Выравнивание блочного элемента
     * @param {object} Element
     * @param {string} vAlign вертикальное выраванивание top|bottom|center
     * @param {string} hAlign горизонтальное вырваниваие left|right|center
     * @param {object} Target элемент относительно которого выравнивается  элемент
     */
    AdjustPosition: function(Element,vAlign,hAlign,Target) {
	vAlign = vAlign !== 'top' && vAlign !== 'bottom' ? 'center' : vAlign;
	hAlign = hAlign !== 'right' && hAlign !== 'left' ? 'center' : hAlign;
	Target = Target === undefined ? window : Target;
	var top = 0;
	var left = 0;
	$(Element).css("position", "fixed");
	if($(Element).outerWidth() >= $(Target).width() ) {$(Element).width($(Target).width());}
	if(hAlign === 'center') {left = ($(Target).width()>>1) - ($(Element).outerWidth(true)>>1);}
	else {left = $(Target).width() - $(Element).outerWidth(true);}
	if(vAlign === 'bottom') {top = $(Target).height() - $(Element).height();} else if(vAlign === 'center') { top = ($(Target).height()>>1) - ($(Element).height()>>1); }
	$(Element).css({top: top+'px',left: left+'px'});
	return $(Element);
    }
};
var Widget = {
    Calendar: function() {
    },
    Date: function() {
    },
    DropDown: function() {
    },
    Accordion: function(){
    },
    Tabctrl: function() {
    }
};
var Dialog = {
    Modal: function(url,params){
	Dialog.Close();
	(params === undefined ? $.get(url):$.get(url,params)).success(function(dlg){
	    Wait.End();
	    var dlgContainer = document.createElement("DIV");
	    dlgContainer.className = 'ui-modal-dialog';
	    $(dlgContainer).html(dlg);
	    var dlgCaption = $(dlgContainer).find('.caption');
	    if(dlgCaption.length) {
		$(dlgContainer).draggable({handle: dlgCaption});
		$(dlgCaption).append('<a class="dialog-close" href="#close">×</a>');
	    }
	    //(modal !== undefined && modal === true) ? UI.Modal(false,dlgContainer) : document.body.appendChild(dlgContainer);
	    document.body.appendChild(dlgContainer);
	    Dialog.Resize(dlgContainer);
	    Ui.OnResize(function(){
		$('.ui-modal-dialog').each(function(){
		    Dialog.Resize(this);
		});
	    });
	});
    },
    Close: function(){
	$('.ui-modal-dialog:last').remove();
    },
    Resize: function(dlg){
	if ($(dlg).find('.content').hasClass('maximize')) {
	    Ui.AdjustPosition(
		    $(dlg).width($(window).width() - 20).height($(window).height() - 20).show(100), 'center');
	    var dContent = $(dlg).find('.content');
	    if (dContent.length) {
		var dFooter = $(dContent).next();
		if (dFooter.length) {
		    dContent.height(dContent.parent().innerHeight() - dFooter.outerHeight(true) - dContent.position().top - (dContent.innerHeight() - dContent.height()));
		}
		else {
		    dContent.height(dContent.parent().innerHeight() - dContent.position().top - (dContent.innerHeight() - dContent.height()));
		}
	    }
	}
	else {
	    Ui.AdjustPosition($(dlg).show(100), 'center');
	}
    }
};
var Wait = {
    Begin: function(Text,TimeOut,Class) {
	Text = Text!==undefined && Text!==null ? Text : 'Ожидание';
	TimeOut = TimeOut!==undefined && TimeOut!==null ? TimeOut : 0;

	if(Wait.waitContainer === undefined) {
	    (Wait.waitContainer = $('<div class="ui-wait '+Class+'">'+Text+'</div>').appendTo(document.body));
	}
	else {
	    Wait.waitContainer.html(Text);
	}

	Ui.AdjustPosition(Wait.waitContainer,'top');
	$(Wait.waitContainer).animate({
		opacity: 1,
		top: 0,
	      }, 300);

	TimeOut && setTimeout(function(){
	    $(Wait.waitContainer).animate({
		opacity: 0,
		top: -100
	      }, 300, function() {
		$(Wait.waitContainer).remove();
		Wait.waitContainer = undefined;
	      });
	},TimeOut*1000);
    },
    End: function() {
	(Wait.waitContainer !== undefined) && $(Wait.waitContainer).animate({
	    opacity: 0,
	    top: -100
	  }, 300, function() {
	    $(Wait.waitContainer).remove();
	    Wait.waitContainer = undefined;
	  });
    }
};
var Notify = {
    Supported: function() {
	try {
	    if(window.Notification.permission === undefined || window.Notification.permission.toLowerCase() !== 'granted') {
		window.Notification.requestPermission();
		return window.Notification.permission.toLowerCase() === 'granted';
	    }
	    return true;
	} catch (e) { }
	return false;
    },
    System: function(tag,title,text,icon,timeout) {
	if(Notify.Supported()) {
	    try {
	    var msg = new Notification(title, {
		    body : (text) ? text : "",
		    tag : (tag) ? tag : "",
		    icon : (icon) ? icon : ""
		});
	    } catch (e) {
		Notify.Show(tag,title,text,icon,timeout);
	    }
	}
	else {
	    Notify.Show(tag,title,text,icon,timeout);
	}

    },
    Show: function(tag,title,text,icon,timeout,callaftertimeout) {
	text = text!==undefined && text!==null ? text : false;
	title = title!==undefined && title!==null && title !=='' ? title : false;
	icon = icon!==undefined && icon!==null && icon !=='' ? icon : false;
	tag = tag!==undefined && tag!==null ? tag : 'default';
	timeout = timeout!==undefined && timeout!==null ? timeout : 0;

	(Notify.notifyContainer === undefined) && 
	    (Notify.notifyContainer = $('<ul class="ui-notify"></ul>').appendTo(document.body));

	var htmlIcon = icon ? 
	    (/^(?:http:\/\/.*?)?\/.*/i.test(icon) ? '<div class="notify-icon"><img src="'+icon+'" width="24"></div>' : '<div class="notify-icon"><i class="'+icon+'"></i></div>') : '';
	var htmlTitle = title ? '<div class="notify-title">'+title+'</div>' : '';
	var htmlText = text ? '<div class="notify-text">'+text+'</div>' : '';
	var notifyItem = $('<li class="notify-'+tag+'">'+htmlIcon+'<div class="notify-container">'+htmlTitle+htmlText+'</div></li>').appendTo(Notify.notifyContainer);
	$(notifyItem).find('.notify-title').append('<a class="notify-close" href="#close">×</a>').click(function(e){
	    e.preventDefault();
	    e.stopPropagation();
	    $(notifyItem).animate({
		opacity: 0,
		right: -300
	      }, 200, function() {
		$(notifyItem).remove();
	      });
	});
	Ui.AdjustPosition(Notify.notifyContainer,'top','right');

	$(notifyItem).animate({
		opacity: 1,
		right: 0,
	      }, 200, function() {
		$(notifyItem).show();
	      });

	timeout && setTimeout(function(){
	    $(notifyItem).animate({
		opacity: 0,
		right: -300
	      }, 200, function() {
		$(notifyItem).remove();
		callaftertimeout();
	      });
	},timeout*1000);
    }
};
Ui.On();