$('.btn_menu').on('click',function () {
    $('header nav').toggleClass('show');
    $(this).toggleClass('active');
    if($(this).hasClass('active')){
        $('header .title').text('FERMER');
    }
    else{
        $('header .title').text('MENU');
    }
});