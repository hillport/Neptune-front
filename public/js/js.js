
'use strict';
var $ = jQuery.noConflict();


$('.go-top').on('click', function ()
{
	$('.go-top').removeClass('visible');
	$('body,html').animate({scrollTop: "0px"}, 1000);
});

$(window).on('scroll',function()
{
	var ws = $(window).scrollTop();
	$('.apparition').apparition()

	if ($(window).scrollTop() > 200)
	{
		$('.go-top').addClass('visible');
	}
	else
	{
		$('.go-top').removeClass('visible');
	}
});

valider();


function valider()
{
	var tmail = /^[a-zA-Z0-9]([-_.]?[a-zA-Z0-9])*@[a-zA-Z0-9]([-_.]?[a-zA-Z0-9])*\.([a-z]{2,4})$/;
	var ttel = /^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$/;
	var valid = true;
	$('#formulaire_client,#formulaire_dispo,#formulaire_evenement').on("submit", function (e)
	{
		e.preventDefault();
		var form = $(this);
		$(this).find(".elt-form").removeClass("error");
		valid = true;
		$(this).find(".required").each(function ()
		{
			var $this = $(this);
			if (valid)
			{ 
				if ($this.hasClass("mail") && !tmail.test($this.val()))
				{
					var error = $this.data("error");
					alert(error);
					$this.focus();
					valid = false;
				}
				else if ($this.hasClass("tel") && !ttel.test($this.val()))
				{
					var error = $this.data("error");
					alert(error);
					$this.focus();
					valid = false;
				}
			}
		});
		if(form.find('input[name="client_verif"]').length && form.find('input[name="client_mail"]').val() !== form.find('input[name="client_mail_verif"]').val())
		{
			alert(erreur_deux_mails);
			form.find('input[name="client_mail_verif"]').focus();
			valid = false;
		}
		if (valid)
		{
			var formData = new FormData(this);

			$('input[type="checkbox"]').each(function()
			{

				if($(this).is(':checked'))
				{
					formData.append($(this).attr('name'),1);
				}
				else
				{
					formData.append($(this).attr('name'),0);	
				}

			})

			$.ajax({
				url: 'include/contact.php',
				data: formData,
				type: 'POST',
				contentType: false,
				processData: false,
				success: function (data)
				{
					var json = JSON.parse(data);
					if(json['success'] == true)
					{
						$("#success-contact").empty().append(json['message']);
						$("#success-contact").show();
						form.find('.elt-form').each(function ()
						{
							$(this).find('*').addClass('disabled').prop('disabled', true);

						});
						$('#formulaire_client').find('.g-recaptcha').addClass('disabled');
						$('#formulaire_client').find('input[type=submit]').addClass('disabled').prop('disabled', true);
					}
					else
					{
						alert(json['error']);
					}
				}

			})
		}
	})
}