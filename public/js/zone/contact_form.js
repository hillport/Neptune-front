function dealSuccess(form, data) {
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
                else if (e.errors.length > 0) {
                    form.find('input,textarea,select,button').prop('disabled',false);
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

            });
        });
    });
});