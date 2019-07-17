function dealSuccess(form, data) {
    form.find('.global_error,.error').empty();
    form.append("<div class='success'>" + data.message + "</div>");
    form.find('input,textarea,select').prop('disabled',true);
}

$('.zone.contact_form').each(function () {
    $(this).find('form').each(function () {
        var form = $(this);

        form.on("submit", function (event) {

            event.preventDefault();

            let action = $(this).attr("action");
            let formData = new FormData(this);
            let nameForm = $(this).attr("name");

            form.find('input,textarea,select,button').prop('disabled',true);

            $.edc.send(action, "POST", formData, function (e) {
                if (e.success) {
                    dealSuccess(form, e);
                }
                else if (e.errors.length) {
                    form.find('input,textarea,select,button').removeAttr('disabled');
                    e.errors.forEach(function (key) {

                        let keyName = Object.keys(key);
                        let keyValue = Object.values(key)[0][0].message;
                        let input = nameForm + "_" + keyName;
                        if(form.find('#'+input).next('.error').length){
                            form.find('#'+input).next('.error').empty().append("<div class='error'>" + keyValue + "</div>");
                        }
                        else{
                            form.find("#" + input).after("<div class='error'>" + keyValue + "</div>");
                        }
                    })
                }
                else if (e.globalErrors.length){

                    form.find('input,textarea,select,button').removeAttr('disabled');
                    e.globalErrors.forEach(function (error) {

                        var globalErrors = form.find('.global_error');
                        let message = error.message;

                        if(globalErrors.length){
                            globalErrors.empty().append(message)
                        }
                        else{
                            form.append($('<div class="global_error"></div>').append(message));
                        }


                    })
                }

            });
        })
    })
})
