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

Calendar = {
    RU: {
        FullMonths: {'01':'Январь','02':'Февраль','03':'Март','04':'Апрель','05':'Май','06':'Июнь',
        '07':'Июль','08':'Август','09':'Сентябрь','10':'Октябрь','11':'Ноябрь','12':'Декабрь'},
        FullMonthsDays: {'01':'Января','02':'Февраля','03':'Марта','04':'Апреля','05':'Мая','06':'Июня',
        '07':'Июля','08':'Августа','09':'Сентября','10':'Октября','11':'Ноября','12':'Декабря'},
        ShortMonths: {'01':'янв','02':'фев','03':'мар','04':'апр','05':'май','06':'июн',
        '07':'июл','08':'авг','09':'сен','10':'окт','11':'ноя','12':'дек'},
        FullWeekDays: {1:'Понедельник',2:'Вторник',3:'Среда',4:'Четверг',5:'Пятница',6:'Суббота',7:'Воскресенье'},
        ShortWeekDays: {1:'Пн',2:'Вт',3:'Ср',4:'Чт',5:'Пт',6:'Сб',7:'Вс'}
    },
    HourRange: function(StartHour,EndHour,Interval) {
        Interval = parseInt(Interval === undefined ? 5 : Interval);
        StartHour = parseInt(StartHour === undefined ? 0 : StartHour);
        EndHour = parseInt(EndHour === undefined ? 0 : EndHour);
        var HourRange = [];
        for(h=StartHour;h<EndHour;h+=Interval) {
            HourRange[HourRange.length] = (("0"+h).slice(-2));
        }
        return HourRange;
    },
    MinuteRange: function(StartMinute,EndMinute,Interval) {
        Interval = parseInt(Interval === undefined ? 5 : Interval);
        StartMinute = parseInt(StartMinute === undefined ? 0 : StartMinute);
        EndMinute = parseInt(EndMinute === undefined ? 0 : EndMinute);
        var MinuteRange = [];
        for(i=StartMinute;i<EndMinute;i+=Interval) {
            MinuteRange[MinuteRange.length] = (("0"+i).slice(-2));
        }
        return MinuteRange;
    },
    TimeRange: function(StartTime,EndTime,Interval){
        Interval = parseInt(Interval === undefined ? 10 : Interval);
        StartTime = StartTime === undefined ? '00:00' : StartTime;
        EndTime = EndTime === undefined ? '23:50' : EndTime;
        var hmStart = StartTime.split(':',2); hmStart[0] = parseInt(hmStart[0]); hmStart[1] = parseInt(hmStart[1]);
        var hmEnd = EndTime.split(':',2); hmEnd[0] = parseInt(hmEnd[0]); hmEnd[1] = parseInt(hmEnd[1]);
        var TimeRange = [];
        var i = hmStart[1];
        for(h=hmStart[0];h<=hmEnd[0];h++) {
            if(i>=60) {i -= 60;}
            for(;i<(h!==hmEnd[0] ? 60 : hmEnd[1]);i+=Interval) {
                TimeRange[TimeRange.length] = (("0"+h).slice(-2))+":"+(("0"+i).slice(-2))+":00";
            }
        }
        return TimeRange;
    }
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
	}).on('change', '[data-ui-switch]', function(e) {
	    if($(this).prop('checked')) {
                $($(this).data('ui-switch-hide')).hide();
                $($(this).data('ui-switch')).show();
            }
	}).on('click','.wait',function(){
	    Wait.Begin($(this).attr('wait'));
	}).on('click','div.ui-info>.button-close',function(){
	    $(this).closest('div.ui-info').hide(200,function(){
		$(this).remove();
	    });
	}).on('change keyup','.ui-date>select',function(){
            var ctrl = $(this).closest('.ui-date');
            if(ctrl.hasClass('time')) {
                var val = $(ctrl).find('input:hidden');
                var hh = $(ctrl).find('.time-hh');
                var mm = $(ctrl).find('.time-mm');
                $(val[0]).val(hh.val()+':'+mm.val());
                if(val.length===2) {
                    var hh = $(ctrl).find('.duration-hh');
                    var mm = $(ctrl).find('.duration-mm');
                    $(val[1]).val(hh.val()+':'+mm.val());
                }
            }
            else {
                var val = $(ctrl).find('input:hidden');
                var day = $(ctrl).find('.date-day');
                var month = $(ctrl).find('.date-month');
                var year = $(ctrl).find('.date-year');
                var date = new DateTime(year.val()+'-'+month.val()+'-'+day.val());
                day.val(date.Format('d'));
                month.val(date.Format('m'));
                year.val(date.Format('Y'));
                val.val(date.Format('Y-m-d'));
                $(ctrl).find('.date-dow').text(Calendar.RU.FullWeekDays[date.N]);
            }
        }).ready(function(){
            $('[data-ui-switch][checked]').trigger('change');
            $('.ui-date>select').trigger('change');
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
    Widget: function(element,WidgetClass) {
        if(element.data('$WIDGET') === undefined) {
            (WidgetClass === undefined || WidgetClass === null ? (WidgetClass = {}) : null);
            typeof WidgetClass.__init === 'function' && WidgetClass.__init.call(WidgetClass); 
            element.data('$WIDGET',WidgetClass);
        }
        return element.data('$WIDGET');
    },
    Dynamic: function(element){
        return Ui.Widget($(element),{
            Element: $(element),
            AutoLoad: false,
            Url: '',
            OnSuccess: false,
            __init: function (){
                this.AutoLoad = $(this.Element).attr('autoload') !== undefined;
                this.Url = $(this.Element).attr('url');
                this.AutoLoad && this.Reload();
            },
            Reload: function(data) {
                var params = $(this.Element).data();
                var This = this;
                delete params['$WIDGET'];
                (data!==undefined && data !== null) && ($.extend( params, data ));
                $.ajax({
                    method: "POST",
                    url: this.Url,
                    data: params,
                    cache: false,
                    dataType: "html"
                }).done(function (html) {
                    if (typeof This.OnSuccess === 'function') {
                        This.OnSuccess.call(This.Element,html);
                    }
                    else {
                        $(This.Element).html(html);
                    }
                });
            },
            Success: function(callback) {
                this.OnSuccess = callback;
            }
        });
    },
    ActiveMenu: function(element){
        return Ui.Widget($(element),{
            Element: $(element),
            Elements: $(element).children(),
            __init: function (){
                var Menu = this;
                $(this.Elements).each(function(){
                    if($(this).attr('selected') === undefined && $(this).data('route') !== undefined) {
                        var re = new RegExp($(this).data('route'),'ig');
                        re.test(window.location.pathname) && $(this).attr('selected','');
                    }
                }).click(function(e){
                    var item = $(this);
                    $(Menu.Elements).removeAttr('selected');
                    item.attr('selected','');
                    e.preventDefault();
                    var el = item.data('scroll');
                    item.parent().animate({scrollLeft: el.offset + (el.index?-40:0)}, 300,function(){
                        window.location = item.find('a:first-child').attr('href');
                    });
                });
                Menu.__invalidate();
                $(window).on('resize',function(){
                    Menu.__invalidate();
                });
            },
            scrollTo: function(element) {
                if(element === 'selected') {
                   var el = $(this.Elements).filter('[selected]').data('scroll');
                   $(this.Element).scrollLeft(el.offset + (el.index?-40:0));
                }
            },
            __invalidate: function(){
                this.Element.scrollLeft(0);
                var offset = this.Element.offset().left;
                $.each(this.Elements,function(i){
                    $(this).data('scroll',{index: i,offset:$(this).offset().left-offset});
                });
                this.scrollTo('selected');
            }
        });
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
    AdjustPosition: function(Element,vAlign,hAlign,Target,position) {
	vAlign = vAlign !== 'top' && vAlign !== 'bottom' ? 'center' : vAlign;
	hAlign = hAlign !== 'right' && hAlign !== 'left' ? 'center' : hAlign;
        position = position === undefined ? 'fixed' : position;
	Target = Target === undefined ? window : Target;
	var top = 0;
	var left = 0;
	$(Element).css("position", position);
	//if($(Element).outerWidth() >= $(Target).width() ) {$(Element).width($(Target).width());}
	if(hAlign === 'center') {left = ($(Target).width()>>1) - ($(Element).outerWidth(true)>>1);}
	else if(vAlign === 'right') {left = $(Target).width() - $(Element).outerWidth(true);}
	if(vAlign === 'bottom') {top = $(Target).height() - $(Element).height();} else if(vAlign === 'center') { top = ($(Target).height()>>1) - ($(Element).outerHeight(true)>>1); }
	$(Element).css({top: top+'px',left: left+'px'});
	return $(Element);
    },
    Dialog: function(Url,Data,Class) {
        $.get(Url,Data).success(function(dlg){
            var dialogModalContainer = document.createElement("DIV");
            dialogModalContainer.className = 'ui-modal' + (Class !== undefined ? (' '+Class):'');
            $(dialogModalContainer).html('<div class="prompt">'+dlg+'</div>');
            document.body.appendChild(dialogModalContainer);
            $('.ui-modal button.cancel').click(function(){
                $('.ui-modal').remove();
            });
            Ui.AdjustPosition($(dialogModalContainer).find('.prompt'),'center', 'left',undefined,'relative');
            Ui.OnResize(function(){
                $('.ui-modal>.prompt').each(function(){
                    Ui.AdjustPosition($(this),'center', 'left',undefined,'relative');
                });
            });
        });
    },
    /**
     * @param {array} FieldsList список полей в форме
     * @param {string} Title Заголовок формы
     * @param {string} Desc Дополнительное описание формы
     * @param {string} Action Значение поля <form action="Action" ...
     * @param {string} SubmitButton Название кнопки ["Сохранить"]
     * @param {string} Class Название класса формы
     */ 
    Prompt: function (FieldsList,Title,Desc,Action,SubmitButton,Class) {
        var promptModalContainer = document.createElement("DIV");
        promptModalContainer.className = 'ui-modal' + (Class !== undefined ? (' '+Class):'');
        SubmitButton = SubmitButton === undefined ? 'Сохранить' : SubmitButton;
        Action = (Action!==undefined?Action:'');
        var attr = function(val,name) {
            return val===undefined || val === null ? '' : (' '+name+'="'+val+'"');
        };
        var fields = [];
        var actions = [];
        $.each(FieldsList,function(){
            var ctrl = '';
            switch(this.type) {
                case 'action':
                    actions[actions.length] = '<button '+attr(this.name,'name') + attr(this.class,'class') +'>'+this.value+'</button>';
                break;
                case 'hidden':
                    ctrl = '<input type="hidden"'+attr(this.name,'name') + attr(this.id,'id') + attr(this.value,'value') + '>';
                    break;
                case 'text':
                    ctrl = '<input type="text"' + attr(this.required,'required') + attr(this.list,'list') + attr(this.class,'class') + attr(this.name,'name') + attr(this.pattern,'pattern') +
                            attr(this.id,'id') + attr(this.placeholder,'placeholder') + attr(this.value,'value') + '>';
                    break;
                case 'textarea':
                    ctrl = '<textarea' + attr(this.required,'required') + attr(this.class,'class') + attr(this.name,'name') + attr(this.id,'id') + 
                            attr(this.placeholder,'placeholder') + '>'+this.value+'</textarea>';
                    break;
                case 'custom':
                    ctrl = this.control.call(this);
                    break;    
                case 'checkbox':case 'radio':
                    ctrl = '<label><input type="'+this.type+'"' + attr(this.required,'required') + attr(this.name,'name') + 
                            attr(this.id,'id') + attr(this.checked===undefined || this.checked===false ? null : '','checked') + attr(this.value,'value') + '> '+(this.label!==undefined ? this.label : '')+'</label>';
                    break;
                case 'date':
                    var now = DateTime(this.value !== undefined && this.value !== '' ? this.value : new Date());
                    ctrl = '<div><div class="ui-date"><input type="hidden"' + attr(this.name,'name') + attr(this.id,'id') +
                            attr(this.required,'required') + attr(now.Format('Y-m-d'),'value') + '><span class="date-dow">'+(Calendar.RU.FullWeekDays[now.N])+'</span>,<select class="date-day">';
                    for(d=1;d<=31;d++) {ctrl += (d!==now.d ? '<option>':'<option selected>')+('0'+d).slice(-2)+'</option>';}
                    ctrl += '</select><select class="date-month">';
                    for(m=1;m<=12;m++) {
                        var mn = ('0'+m).slice(-2);
                        ctrl += (m!==now.m ? '<option ':'<option selected ')+(' value="'+mn+'">')+Calendar.RU.FullMonthsDays[mn]+'</option>';
                    }
                    ctrl += '</select><select class="date-year">';
                    for(y=now.Y+(this['year-range']===undefined?-5:(-parseInt(this['year-range'])));y<=now.Y+1;y++) {ctrl += (y!==now.Y ? '<option>':'<option selected>')+y+'</option>';}
                    ctrl += '</select></div></div>';
                break;
                case 'time':
                    var now = (this.value !== undefined && this.value !== '' ? this.value : '08:00:00').split(':',3);
                    var hh=parseInt(now[0]);
                    var mm=parseInt(now[1]);
                    ctrl = '<div><div class="ui-date time"><input type="hidden"' + attr(this.name + (this.duration !== undefined?'[time]':''),'name') + attr(this.id,'id') +
                            attr(this.required,'required') + attr(this.value,'value') + '><select class="time-hh">';
                    for(h=0;h<=23;h++) {ctrl += (h!==hh ? '<option>':'<option selected>')+('0'+h).slice(-2)+'</option>';}
                    ctrl += '</select>&nbsp;&colon;&nbsp;<select class="time-mm">';
                    for(m=0;m<=55;m+=5) {
                        var mn = ('0'+m).slice(-2);
                        ctrl += (m!==mm ? '<option ':'<option selected ')+(' value="'+mn+':00">')+mn+'</option>';
                    }
                    ctrl += '</select>';
                    if(this.duration !== undefined) {
                        var dTime = (this.duration !== undefined && this.duration !== '' ? this.duration : '00:05:00').split(':',3);
                        var hh=parseInt(dTime[0]);
                        var mm=parseInt(dTime[1]);
                        ctrl += '<span class="date-dow">&nbsp;длительность&nbsp;</span><input type="hidden"' + attr(this.name + '[duration]','name') + attr(this.required,'required') + attr(this.duration,'value') + '><select class="duration-hh">';
                        for(h=0;h<=8;h++) {ctrl += (h!==hh ? '<option>':'<option selected>')+('0'+h).slice(-2)+'</option>';}
                        ctrl += '</select>&nbsp;&colon;&nbsp;<select class="duration-mm">';
                        for(m=0;m<=55;m+=5) {
                            var mn = ('0'+m).slice(-2);
                            ctrl += (m!==mm ? '<option ':'<option selected ')+(' value="'+mn+':00">')+mn+'</option>';
                        }
                        ctrl += '</select>';
                    }
                    ctrl += '</div></div>';
                break;
                case 'select':
                    ctrl = '<select '+attr(this.required,'required') + attr(this.class,'class') + attr(this.name,'name') + attr(this.id,'id') + '>';
                    var val = this.value;
                    $.each(this.options,function(key, value){
                        ctrl += '<option value="'+key+'" '+(val===key ? 'selected':'')+' >'+value+'</option>';
                    });
                    ctrl += '</select>';
                    break;
            }
            if(ctrl !== '') {
                if(this.type !== 'hidden') {
                    fields[fields.length] = '<div class="field">'+(this.field!==undefined ? this.field :'')+ ctrl + '</div>';
                }
                else {
                    fields[fields.length] = ctrl;
                }
            }
        });
        $(promptModalContainer).html('<div class="prompt"><div><form method="post" action="'+Action+'">'+(Title!=='' && Title !== undefined ? ('<div class="caption">'+Title+'</div>'):'')+(Desc!=='' && Desc !== undefined ? ('<div class="desc">'+Desc+'</div>'):'')+'<fieldset>'+fields.join('')+'</fieldset><div class="actions">'+(actions.join('&nbsp;')+(actions.length?'&nbsp;':''))+'<button class="ui-button-success">'+SubmitButton+'</button>&nbsp;<button class="ui-button-default cancel" type="button">Отменить</button></div></form></div></div>');
        document.body.appendChild(promptModalContainer);
        $('.ui-modal>.prompt button.cancel').click(function(){
            $('.ui-modal').remove();
        });
        Ui.AdjustPosition($(promptModalContainer).find('.prompt'),'center', 'left',undefined,'relative');
        Ui.OnResize(function(){
            $('.ui-modal>.prompt').each(function(){
                Ui.AdjustPosition($(this),'center', 'left',undefined,'relative');
            });
        });
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
