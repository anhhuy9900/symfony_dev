var AdminMain = window.AdminMain || {};

(function($){
    AdminMain.Func = {
        init: function () {
            AdminMain.Func.admin_submit_limit_records();
            AdminMain.Func.admin_search_keyword_form();
            setTimeout(function(){
                $(".alert-success").slideUp();
            },5000);

        },

        admin_submit_limit_records : function(){
            $("#show_record_num").on('change', function(){
                var limit = $(this).val();
                var pathname = $(location).attr('href');
                var parameter_url, redirect_url;
                if(pathname.indexOf('lm=') == -1){
                    parameter_url = pathname.indexOf('?') > 0 ? '&lm='+limit : '?lm='+limit;
                    redirect_url = pathname + parameter_url;
                } else {
                    //parameter_url = pathname.indexOf('?') > 0 ? '&lm='+limit : '?lm='+limit;
                    var param = AdminMain.Func.getURLParameter('lm', pathname);
                    redirect_url = pathname.replace('lm='+param,'lm='+limit);
                }
                window.location.href = redirect_url;
            });
        },

        getURLParameter : function (name, url) {
            if (!url) url = location.href;
            name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
            var regexS = "[\\?&]"+name+"=([^&#]*)";
            var regex = new RegExp( regexS );
            var results = regex.exec( url );
            return results == null ? null : results[1];
        },

        admin_search_keyword_form : function(){
            $("#search-keyword").keydown(function(e){
                var keyCode = e.which;
                if (keyCode == 13) {
                    var key = $(this).val();
                    var pathname = $(location).attr('href');
                    var parameter_url, redirect_url;
                    if(pathname.indexOf('key=') == -1){
                        parameter_url = pathname.indexOf('?') > 0 ? '&key='+key : '?key='+key;
                        redirect_url = pathname + parameter_url;
                    } else {
                        var param = AdminMain.Func.getURLParameter('key', pathname);
                        redirect_url = pathname.replace('key='+param,'key='+key);
                    }

                    window.location.href = redirect_url;
                }
            });

        }
    };
})(jQuery);

$(document).ready(function(){
    AdminMain.Func.init();
});
