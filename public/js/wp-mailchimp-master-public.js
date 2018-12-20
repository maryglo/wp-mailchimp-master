(function( $ ) {
	'use strict';

    $(function(){

            var cookie = getCookie('never_show');
            var popup_option = wc_mcm_object.subscription_form_option;
            var time = wc_mcm_object.milliseconds;

            if (cookie == "" && popup_option == 'specific_time') {
                setTimeout(function(){
                    showDialog();
                },time);
            } else if(cookie == "" && popup_option == 'automatic'){
                    showDialog();
            } else {
                $('.wp_mcm_subscription').click(function(e){
                    e.preventDefault();
                    showDialog();
                });
            }

        $('form.mailchimp-form').validate({
            rules:{
                firstName: "required",
                lastName: "required",
                'mailchimp-email': {
                    required: true,
                    email: true
                },
                'mailchimp_list': "required"
            },
            messages: {
               'mailchimp-email': "*Please enter a valid email address",
               'mailchimp_list': "*Please select a list"
            },
            errorPlacement: function(error, element) {
                error.insertAfter(element);

                if (element.attr("name") == "firstName") {
                    error.insertAfter(".firstName_label");
                }

                if (element.attr("name") == "lastName") {
                    error.insertAfter(".lastName_label");
                }

                if (element.attr("name") == "mailchimp-email") {
                    error.insertAfter(".mailchimp-email_label");
                }

            },
            submitHandler : function(form) {
                var dataObject = formFieldsToObject($('form.mailchimp-form').serializeArray());

                var data = {
                    action: 'subscribe_to_list',
                    data: dataObject
                }

                $.ajax({
                    type: "post",
                    url: wc_mcm_object.ajax_url,
                    data: data,
                    success: function(response){
                        $('#dialog').dialog('close');
                        if(response.id){
                            $( "#success_dialog").find('.text').text('You have been subscribed successfully!');
                        } else {
                            $( "#success_dialog").find('.text').text('An error occurred: Please try again!');
                        }

                        $( "#success_dialog" ).dialog({
                            modal: true,
                            dialogClass: 'noTitle'
                        });
                    }
                });
            }
        });
    });

    function showDialog() {
        $( "#dialog" ).dialog({
            modal: true,
            width: 500,
            title: "Subscribe",
            dialogClass: 'noTitle',
            beforeClose: function( event, ui ) {
                /*var expires = parseInt(2);
                document.cookie =
                    'never_show=true' +
                        '; expires=' + expires +
                        '; path=/';*/

                createCookie('never_show','true',1);
            }
        });
    }

    function createCookie(cname, cvalue,  exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+ d.toUTCString();
        console.log(cname + "=" + cvalue + ";" + expires + ";path=/");
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i <ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    function formFieldsToObject( fields ) {
        var data_fields = {};

        for( var i = 0; i < fields.length; i++ ) {
            var field = fields[ i ];
            if( ! data_fields.hasOwnProperty( field.name ) && !(/\[\]/.test(field.name))) {
                data_fields[ field.name ] = field.value;
            }
            else {
                var new_field_name = field.name.replace("[]", "");
                if( data_fields[ new_field_name ] instanceof Array ){
                    data_fields[ new_field_name ].push( field.value );
                } else {
                    data_fields[ new_field_name] = [ field.value ];
                }
            }
        }

        return data_fields;
    }
})( jQuery );
