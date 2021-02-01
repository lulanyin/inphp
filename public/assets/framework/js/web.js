function updateUI(){
    $('.ui.dropdown').dropdown();
    $('.ui.accordion').accordion();
    $('.ui.checkbox,.ui.radio').checkbox();
}

function formSubmit(form, type, data, url) {
    Ajax.ajax({
        data : data,
        url : url,
        type : type,
        loadingText : form.attr("data-loading") || "正在提交表单",
        callback : function (json) {
            let call_back_result = null;
            if(typeof _ajax_form_callback=='function'){
                call_back_result = _ajax_form_callback(json);
                if(call_back_result){
                    return;
                }
            }
            if(json.error){
                Modal.alert(json.message);
            }else{
                Loader.done(json.message);
                var callback_url = json.data.callback_url || '';
                if(callback_url=='none' || callback_url=='here' || callback_url=='no_callback'){

                }
                else if(callback_url !== ''){
                    window.location.href = urlParse(callback_url);
                }else{
                    window.location.reload();
                }
            }
        }
    });
}

function getQueryVariable(variable)
{
    let query = window.location.search.substring(1);
    let vars = query.split("&");
    for (let i=0;i<vars.length;i++) {
        let pair = vars[i].split("=");
        if(pair[0] === variable){ return pair[1]; }
    }
    return null;
}

$(function (){
    //信息框关闭
    $(document).on('click', ".ui.message i.close", function(){
        $(this).parent().animate({opacity:0},function(){
            $(this).remove();
        });
    });
    /**
     * 需要点击后显示加载动画的链接
     */
    $(document).on("click", "a[data-loading]", function () {
        if(TW){
            TW.showLoading = true;
            Loader.loading('正在加载');
        }
    });
    //加载UI
    updateUI();
    //异步提交的表单
    var ajaxForm = $("form[data-ajax='true']");
    ajaxForm.on("submit", function () {
        var form = $(this);
        var data = form.serializeArray();
        var url = form.attr('action') || null;
        if(url===null){
            url = window.location.href;
        }
        var type = form.attr("method") || "POST";
        var confirmText = form.attr('data-confirm');
        confirmText = confirmText || null;
        if(confirmText){
            Modal.confirm(confirmText, function () {
                formSubmit(form, type, data, url);
            });
        }else{
            formSubmit(form, type, data, url);
        }
        return false;
    });

    function AjaxGET(url, reload){
        Ajax.get(urlParse(url), function (json) {
            if(json.error){
                Modal.alert(json.message);
            }else{
                Loader.done(json.message);
                if(reload==='true'){
                    window.location.reload();
                }else{
                    if(json.data.callback_url!==undefined){
                        window.location.href = urlParse(json.data.callback_url);
                    }
                }
            }
        });
    }

    //AJAX操作，无需再编写多余代码
    $("button[data-ajax], a[data-ajax]").click(function () {
        let self = $(this),
            url = self.attr('data-ajax'),
            confirmText = self.attr('data-confirm'),
            reload = self.attr('data-reload');
        confirmText = confirmText || null;
        if(confirmText===null){
            AjaxGET(url, reload);
        }else{
            Modal.confirm(confirmText, function () {
                AjaxGET(url, reload);
            });
        }
    });

    $(".radio-choose .item").click(function () {
        $(this).addClass('active').siblings().removeClass('active');
        let ipt = $(this).parent().attr('data-input');
        $(ipt).val($(this).attr('data-value'));
    });
});