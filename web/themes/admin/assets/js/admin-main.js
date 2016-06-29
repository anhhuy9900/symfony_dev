var AdminMain = window.AdminMain || {};

(function($){
    AdminMain.Func = {
        init: function () {
            AdminMain.Func.get_number_litmit_records();

        },

        get_number_litmit_records : function(){
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
        }
    };
})(jQuery);

$(document).ready(function(){
    AdminMain.Func.init();
});
