var AdminMain = window.AdminMain || {};

(function($){
    AdminMain.Func = {
        init: function () {
            AdminMain.Func.admin_submit_limit_records();
            AdminMain.Func.admin_search_keyword_form();
            AdminMain.Func.admin_order_field_data();
            setTimeout(function(){
                $(".alert-success").slideUp();
            },5000);

            //to translate the daterange picker, please copy the "examples/daterange-fr.js" contents here before initialization
            $('input[name=date-range-picker]').daterangepicker({
                'applyClass' : 'btn-sm btn-success',
                'cancelClass' : 'btn-sm btn-default',
                locale: {
                    applyLabel: 'Apply',
                    cancelLabel: 'Cancel',
                }
            },
            function(start, end, label){
                var start_date = Date.parse(start) / 1000;
                var end_date = Date.parse(end) / 1000;
                //console.log(start.toLocaleString() + end.toLocaleString() + label);
                console.log("start_date " + start_date);
                console.log("end_date " + end_date);
                if(start_date > 0 && end_date > 0){
                    var value = start_date + '-' + end_date;
                    var redirect_url = AdminMain.Func.generate_url_hande_filter('date_range', value);
                    window.location.href = redirect_url;
                }

            });

        },

        admin_submit_limit_records : function(){
            $("#show_record_num").on('change', function(){
                var limit = $(this).val();
                var redirect_url = AdminMain.Func.generate_url_hande_filter('lm', limit);
                window.location.href = redirect_url;
            });
        },

        admin_search_keyword_form : function(){
            $("#search-keyword").keydown(function(e){
                var keyCode = e.which;
                if (keyCode == 13) {
                    var key = $(this).val();
                    var redirect_url = AdminMain.Func.generate_url_hande_filter('key', key);
                    window.location.href = redirect_url;
                }
            });
        },

        admin_order_field_data : function(){
            $(".admin_order_field").on('click',function(){
                var redirect_url = $(this).data('url');
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

        generate_url_hande_filter : function(str_type, value){
            var pathname = $(location).attr('href');
            var parameter_url, redirect_url;
            if(pathname.indexOf(str_type + '=') == -1){
                parameter_url = pathname.indexOf('?') > 0 ? '&' + str_type + '='+value : '?' + str_type + '=' + value;
                redirect_url = pathname + parameter_url;
            } else {
                var param = AdminMain.Func.getURLParameter(str_type, pathname);
                redirect_url = pathname.replace(str_type + '='+param, str_type + '=' + value);
            }

            return redirect_url;
        }

    };
})(jQuery);

$(document).ready(function(){
    AdminMain.Func.init();
});
