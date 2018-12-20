jQuery(document).ready(function($){

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
    $("#campaign_create_datetime").datetimepicker({
        format: "yyyy-mm-dd hh:ii",
        autoclose: true,
        todayBtn: true,
        pickerPosition: "bottom-left"
    });

    $("#wp_mcm_campaign_datetime").datetimepicker({
        format: "yyyy-mm-dd hh:ii",
        autoclose: true,
        todayBtn: true,
        pickerPosition: "bottom-left",
        fontAwesome:false
    });

    $('.campaign_create_option').on('click',function(){
        var value = $(this).val();
        $('#campaign_create_datetime').hide();
        if(value == 'specific_datetime'){
             $('#campaign_create_datetime').show();
        }
    });

     $('#publish_campaign').click(function(e){

         e.preventDefault();
         var dataObject = formFieldsToObject($('form#post').serializeArray());
         var errors = 0;

         $('#wp_mcm_templatefield').removeClass('error');
         $('#wp_mcm_listfield').removeClass('error');
         $('#wp_mcm_from_name').removeClass('error');
         $('#wp_mcm_reply_to').removeClass('error');
         $('#wp_mcm_subject_line').removeClass('error');
         $('#wp_mcm_campaign_title').removeClass('error');

         if(dataObject.wp_mcm_templatefield == ""){
             $('#wp_mcm_templatefield').addClass('error');
             errors++;
         }

         if(dataObject.wp_mcm_listfield == ""){
             $('#wp_mcm_listfield').addClass('error');
             errors++;
         }

         if(dataObject.wp_mcm_automatic_send == 'yes'){
             if(dataObject.wp_mcm_from_name == ""){
                 $('#wp_mcm_from_name').addClass('error');
                 errors++;
             }

             if(dataObject.wp_mcm_reply_to == ""){
                 $('#wp_mcm_reply_to').addClass('error');
                 errors++;
             }

             if(dataObject.wp_mcm_subject_line == ""){
                 $('#wp_mcm_subject_line').addClass('error');
                 errors++;
             }

             if(dataObject.wp_mcm_campaign_title == ""){
                 $('#wp_mcm_campaign_title').addClass('error');
                 errors++;
             }
         }

         if(errors > 0){
            alert('*Please check all required fields!');
         } else {
             var r = true;
             if(dataObject.wp_mcm_automatic_send == 'yes'){
                 var r = confirm("Are you sure you want to automatically send the campaign?");
             }


             if(r == true){
                 var data = {
                     action: 'create_campaign',
                     data: dataObject
                 }

                 $('#wp_mcm_box_id').find('.spinner').addClass('is-active');

                 if(dataObject.mc_action == 'update'){
                     var campaign_template = $('#template_html').val();
                     var template_content = $('<div />',{html:campaign_template});
                     var contents = "";

                     if($('#wp-template_content-wrap').hasClass('html-active')){
                           contents = jQuery('#template_content').val();
                           template_content.find('table:first').html(jQuery('#template_content').val());
                     } else {
                         var activeEditor = tinyMCE.get('template_content');
                         contents = tinyMCE.get('template_content').getContent();
                         if(activeEditor!==null){
                           template_content.find('table:first').html( tinyMCE.get('template_content').getContent());
                         }
                     }

                     dataObject.template_content = contents;
                 }

                 $.post(ajaxurl,data, function(response){
                     $('#wp_mcm_box_id').find('.spinner').removeClass('is-active');
                     var btn_text = dataObject.wp_mcm_overrridecontent == 'yes' &&  dataObject.wp_mcm_automatic_send == 'yes' ? 'Send Campaign' : 'Update Content';

                     if(response.mc_action == 'update'){
                         $('#mc_content_editor').show();
                         $('input#campaign_id').val(response.campaigns);
                         $('input#mc_action').val(response.mc_action);

                         var template_html = response.html;
                         var contents = $(template_html).find('table')[0];
                         var inner_text = contents.innerHTML;

                         $('#template_html').val(template_html);

                         if($('#wp-template_content-wrap').hasClass('html-active')){
                             $('#template_content').val("<table>"+inner_text+"</table>");
                         } else {
                             var activeEditor = tinyMCE.get('template_content');
                             if(activeEditor!==null){
                                 activeEditor.setContent("<table>"+inner_text+"</table>");
                             }
                         }

                         $('#publish_campaign').attr('value', btn_text);

                     } else {
                         if(response.id){
                             $('#mc_content_editor').hide();
                            alert('Campaign has been created successfully!');
                          } else {
                            alert('An error occurred: Please try again!');
                          }
                     }



                 });
             }
         }


     });

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

});

