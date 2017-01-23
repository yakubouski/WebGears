var Ui = {
    Window: {
        Resize: function(call){
            $(window).on('resize',call);
            call();
        },
        /**
        * Выравнивание блочного элемента
        * @param {object} Element
        * @param {string} vAlign вертикальное выраванивание top|bottom|center
        * @param {string} hAlign горизонтальное вырваниваие left|right|center
        * @param {object} Target элемент относительно которого выравнивается  элемент
        * @param {string} Position css стиль свойства position fixed|absolute
        */
       AdjustPosition: function(Element,vAlign,hAlign,Target,Position) {
           vAlign = vAlign !== 'top' && vAlign !== 'bottom' ? 'center' : vAlign;
           hAlign = hAlign !== 'right' && hAlign !== 'left' ? 'center' : hAlign;
           Position = Position === undefined ? 'fixed' : Position;
           Target = Target === undefined ? window : Target;
           var top = 0;
           var left = 0;
           $(Element).css("position", Position);
           if(hAlign === 'center') {left = ($(Target).width()>>1) - ($(Element).outerWidth(true)>>1);}
           else if(vAlign === 'right') {left = $(Target).width() - $(Element).outerWidth(true);}
           if(vAlign === 'bottom') {top = $(Target).height() - $(Element).height();} else if(vAlign === 'center') { top = ($(Target).height()>>1) - ($(Element).outerHeight(true)>>1); }
           $(Element).css({top: top+'px',left: left+'px'});
           return $(Element);
       }
    },
    Template: function(template) {
        return {
            Exec: function (args) {
                var result = template;
                var match;
                while ((match = /\{\$(\w+)\}/.exec(result))) {
                    result = result.replace(match[0], args[match[1]] !== undefined ? (typeof args[match[1]] === 'function' ? args[match[1]]() : args[match[1]]) : '');
                }
                return result;
            }
        };
    },
    OpenDialog: function() {
        var el = $(this);
        $('.ui-modal').remove();
        $.get(el.attr('href')).success(function(html){
            Ui.ModalDialog(html);
        });
    },
    ModalDialog: function(Template,Args,Class) {
        var dialogModalContainer = document.createElement("DIV");
        dialogModalContainer.className = 'ui-modal' + (Class !== undefined ? (' '+Class):'');
        $(dialogModalContainer).html('<div class="prompt">'+Template+'</div>');
        $(document.body).append(dialogModalContainer);
        $('.ui-modal button.cancel').click(function(){
            $('.ui-modal').remove();
        });
        Ui.Window.AdjustPosition($(dialogModalContainer).find('.prompt'),'center', 'left',undefined,'relative');
        Ui.Window.Resize(function(){
            $('.ui-modal>.prompt').each(function(){
                Ui.Window.AdjustPosition($(this),'center', 'left',undefined,'relative');
            });
        });
    }
};
$(function(){
    $(document).on('click','.dialog',function(e){
        e.preventDefault();
        Ui.OpenDialog.call(this,e);
    }).on('click','input[type=text].file-name',function(){
        var el=$(this);
        el.next('input[type=file]').trigger('click').change(function(){
            console.log(this.files[0]);
            (this.files.length) && (el.val(this.files[0].name));
            return true;
        });
    });
    $(document).on('change','.toggle-checked-childs',function(e){
        $(this).prop('checked') ? $($(this).attr('childs')).show() : $($(this).attr('childs')).hide();
    });
    $('.toggle-checked-childs').trigger('change');
});