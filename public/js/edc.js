/* 
Plugins jquery (utilisables depuis $(selector).function() )

* chargePhoto()
mailto()
resize_img()
apparition()

// Functions raccourcies utiles : 
$.edc.send() // Requête AJAX avec paramettres
(url,type="GET",data="",function(){})
Ou function = success

$.fancy() avec en paramettres
(data,type="inline",close = false,functio(){})
Où , data peut être un lien vers (image , video ) ou du HTML.
Pour type -> Se référer a doc fancy3.
La fonction charge fancybox s'il n'est pas chargé.
Function() est le callback , une fois que la fancy est affichée.

//////////////////////////////////

Chargement de scripts de façon automatique :

-Font-awesome PRO si les classes suivantes sont trouvées :
.fa,.fab,.far,.fal

-Fancybox si la classe suivante est trouvée : .fancy

- Masonry si .masonry es trouvée

- Superfish si .superfish est trouvé

- Google recaptcha si #g-recaptcha est trouvé




*/


if(typeof(root) === typeof(undefined))
    var root = '/';
/* DEFINITION DES PLUGINS JQUERY */
$.fn.chargePhoto = function(param){
    this.each(function(){
        var $this = $(this);
        var w = parseInt($this.width());
        var h = parseInt($this.height());
        var id = $this.data('id');
        let regex = new RegExp(/([0-9])+/);
        $this.addClass('photo_ok');
        if(typeof(id) != 'undefined' && id != ''){
            if(regex.test(id)){

                var nom = $this.attr('data-nom');
                // Est-ce que il y a un data-nom ?
                var und = (typeof(nom) != 'undefined' && nom.trim() != '');
                /* Redéfinition de l'URL pour l'envoyer , se référer au htaccess*/
                var url = root+"photos/"+id+'/'+w;
                if(h > 0) {url += '/'+h; }
                else if((h == 0 || h == '' ) && $this.hasClass('noratio') || und) {url += '/0'; }

                if($this.hasClass('noratio')) {url += '/1'; }
                else if($this.hasClass('saphir') || und) {url += '/0'; }
                if($this.hasClass('saphir')) {url += '/1'; }
                else if (und)
                    url += '/0';
                // Pour savoir si il y a bel et bien un data-nom
                if(und)
                    url += '/'+$this.attr('data-nom');
                if ($this.hasClass("paralax"))
                {
                    $this.css("background-image", "url(" + url+  ")");
                    $this.css("opacity", 1);
                }
                else{
                    $this.find('img').css('opacity','0');
                    $this.find("img").attr("src", url);
                    $this.find("img").on('load', function ()
                    {
                        if($this.parents('.masonry').length)
                        {
                            $('.masonry').masonry('layout');
                        }


                        $this.find("img").css({"opacity": "1"});
                    });
                }
            }
            else{
                //^photos/([a-zA-Z]{1,4})/([a-zA-Z0-9-_]*)/([\d+]{0,4})(/([\d+]{0,4}))?(/([0-1]?))?(/[a-zA-Z0-9-]+)?$ /ajax/generer_photo_normale.php?id=$2&w=$3&h=$5&noratio=$7&ext=$1 [T=image/jpg]

                let exp = id.split('.');

                let ext = exp[exp.length -1];
                let und = (typeof(nom) != 'undefined' && nom.trim() != '');
                if(typeof(ext) != 'undefined' && ext != ''){
                    let url = root+'photos/'+ext+'/';
                    for(let i =0;i < exp.length -1 ;i++){
                        url += exp[i];
                    }
                    url += '/'+w;

                    if(h > 0) {url += '/'+h; }
                    else if((h == 0 || h == '' ) && $this.hasClass('noratio') || und) {url += '/0'; }
                    if($this.hasClass('noratio')) {url += '/1'; }
                    else if(und){
                        url += '/0';
                    }

                    if(und)
                        url += '/'+$this.attr('data-nom');

                    if ($this.hasClass("paralax"))
                    {
                        $this.css("background-image", "url(" + url+  ")");
                        $this.css("opacity", 1);
                    }
                    else{
                        $this.find('img').css('opacity','0');
                        $this.find("img").attr("src", url);
                        $this.find("img").on('load', function ()
                        {
                            if($this.parents('.masonry').length)
                            {
                                $('.masonry').masonry('layout');
                            }


                            $this.find("img").css({"opacity": "1"});
                        });
                    }
                }
            }
        }
    });
return this;
};
$.fn.mailto = function(mail){
    var tmail = /^[a-zA-Z0-9]([-_.]?[a-zA-Z0-9])*@[a-zA-Z0-9]([-_.]?[a-zA-Z0-9])*\.([a-z]{2,4})$/;

    if(tmail.test(mail)){
        this.on('click',function(e){
            e.preventDefault();
            window.location.href = 'mailto:'+mail;
        });
    }
}
$.fn.resize_img = function(params){
    this.each(function(){
        var img = $(this).find('> img');
        img.removeClass('img_portrait').removeClass('img_paysage');
        var ratio_zone = $(this).width() / $(this).height();
        var ratio_img = img.width() / img.height();
        if(ratio_zone >= ratio_img){
            img.addClass('img_paysage');
        }
        else{
            img.addClass('img_portrait');
        }
    });
    return this;
};
$.fn.apparition = function(params){
    var ws = $(window).scrollTop();
    this.each(function(){
        var ot = $(this).offset().top;
        if($(this).data('offset') != null && $(window).width() > 599){
            var offset = $(this).data('offset');
            if(offset > 100 && $(window).height() < 900){
                offset = 100;

            }
            ot += parseInt(offset);
        }
        if(ws > ot && !$(this).hasClass('show')){
            $(this).addClass('show');
        }
        else if(ws < ot && $(this).hasClass('show') && !$(this).hasClass("nodispear")){
            $(this).removeClass('show');
        }
    });
    return this;
}

$.fn.extend({
    setCustomValidity : function(message){
        this[0].setCustomValidity(message);
        return this;
    },
    checkValidity : function(){
        return this[0].checkValidity();
    }
});
/* FIN Definition des plugins Jquery*/


/* Définition du namespace*/
$.edc ={
    /* Variables */
    fin_charge_photo : false,
    compteur_script_ajax: 0,
    lang : (typeof(lang_get) == "undefined") ? 'fr' : lang_get,
    fichiers_fancy:[root+"js/lib/fancybox/dist/jquery.fancybox.min.css",
    root+"js/lib/fancybox/dist/jquery.fancybox.min.js"],
    /* FIN VARIABLES */
    /* Functions */
    send:function(url,type='GET',data='',fn = function(e){}){

        var content = !(typeof(data) == 'object');

        if(typeof(data) == 'function')
        {
            fn = data;
            data = '';
        }
        if(typeof(type) == 'function'){
            fn = type;
            type = 'GET';
        }
        $.ajax(
        {
            type: type,
            url: url,
            data: data,
            contentType: content,
            processData: content,
            success:function(e){
                fn(e);
            }
        });
    },
    fancy: function(data,type='inline',close = false,fn = function(e){}){
        var $this = $.edc;
        /* Variable fonction*/
        if(typeof(close) == 'function'){
            fn = close;
            close = false;
        }
        if(typeof(type) == 'function'){
            fn = type;
            type = 'inline';
        }
        var fancy = function(){
            if(close == true)
                $.fancybox.close();
            $.fancybox.open([{
                src: data,
                type: 'inline',
                opts: {
                    afterShow: function(instance , current){
                        fn(data);
                    }
                }
            }]);
        };

        if($.fn.fancybox)
            fancy();
        else
            $this.loadScript($this.fichiers_fancy,fancy);

    }

};

if(typeof(loadScript) === typeof('function'))
    $.edc.loadScript = loadScript;
else
    $.edc.loadScript = function (e,t,n){if("function"==typeof t?(n=t,t=0):void 0===t&&(t=0),"string"==typeof e&&(e=new Array(e)),"object"==typeof e[t]&&($.edc.loadScript(e[t][0],0,e[t][1]),t++),"function"==typeof e[t]&&(e[t](),t++),void 0!==e[t]){var o=new RegExp(/\.js/),i=new RegExp(/\.css/),a=new RegExp(/player_api/);if(t<e.length)if(o.test(e[t])||a.test(e[t])){var c=document.createElement("script");c.src=e[t],c.type="text/javascript",c.defer="defer",document.body.appendChild(c),c.onload=function(){$.edc.loadScript(e,t+1,n)}}else if(i.test(e[t])){var d=document.createElement("link");d.href=e[t],d.rel="stylesheet",d.media="all",document.head.appendChild(d),d.onload=function(){$.edc.loadScript(e,t+1,n)}}else $.edc.loadScript(e,t+1,n)}else"function"==typeof n&&n()};

/* FIN DE DEFINITION DU NAMESPACE*/


/*Lancement  des fonctions de base */


/* Debut du chargement des scirpts */
var scripts = new Array() ;


if($('.fa,.fab,.far,.fal').length)
{
    scripts.push(root+"js/lib/fontawesome-pro/css/all.min.css");
}

if($('.fancy').length)
{
    scripts = scripts.concat([
     root+"js/lib/fancybox/dist/jquery.fancybox.min.css",
     root+"js/lib/fancybox/dist/jquery.fancybox.min.js"
     ,function(){
      $('.fancy').fancybox();
  }]);
}
if($('.masonry').length)
    scripts = scripts.concat([root+"js/lib/masonry-layout/dist/masonry.pkgd.min.js",function(){
        $('.masonry').masonry();
    }]);


if($('.superfish').length)
{
    scripts = scripts.concat([
        root+"js/lib/superfish/dist/css/superfish.css",
        [root+"js/lib/superfish/dist/js/superfish.min.js",
        function(){
            $('.superfish').superfish();
        }]
        ]);
}

if($('#g-recaptcha').length ||$('.g-recaptcha').length)
{
    scripts.push("https://www.google.com/recaptcha/api.js?hl="+lang_get)
}

$.edc.loadScript(scripts);






