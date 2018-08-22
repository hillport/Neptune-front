$('.btn_menu').on('click',function () {
    $('header nav').toggleClass('show');
    $(this).toggleClass('active');
});