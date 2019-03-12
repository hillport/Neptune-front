$('[name="contact_form"]').on('submit',function(e)
{
    e.preventDefault();
    let form = $(this);
    var formData = new FormData(this);
    let action = $(this).attr('action');
    let nameForm = $(this).attr("name");

    $.edc.send(action,'POST',formData,function(e){

        if (e.errors.length > 0 || e.globalErrors.length > 0) {

            let action = function(key,global){
                if(typeof(global) == 'undefined'){
                    global = false;
                }
                let keyName = Object.keys(key);

                let keyValue = (global == false) ? Object.values(key)[0][0].message : key.message;
                let input = nameForm + "_" + keyName;
                if(form.find('#'+input).next('.error').length){
                    form.find('#'+input).next('.error').empty().append("<div class='error'>" + keyValue + "</div>");
                }
                else{
                    form.find("#" + input).after("<div class='error'>" + keyValue + "</div>");
                }
            };
            e.errors.forEach(function (key) {
                action(key);
            });
            e.globalErrors.forEach(function (key) {
                action(key,true);
            });
        }
        else if (e.success) {
            $('#contact_form_submit').attr('disabled','disabled');
            form.append("<div class='success'>" + e.success_message + "</div>");
            form.prop('disabled',true);
            form.find('input,textarea,select').prop('disabled',true);
        }

    });
});