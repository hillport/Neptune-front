document.addEventListener('DOMContentLoaded',function(){
    lazyImages = [].slice.call(document.querySelectorAll('.photo:not(.loaded),.lazy'));

    let active = true;
    if('IntersectionObserver' in window){
        ImageObserver = new IntersectionObserver(function(entries,observer){
            entries.forEach(function(entry){
                if(entry.isIntersecting){
                    let lazyImage = entry.target;
                    chargePhoto(lazyImage);
                    ImageObserver.unobserve(lazyImage);
                }
            })
        });
        lazyImages.forEach(function(lazyImage){
            ImageObserver.observe(lazyImage);
        });

    }else{
        const lazyLoad = function () {
            if(active === false)
                active = true;
            setTimeout(function () {
                lazyImages.forEach(function (lazyImage) {
                    if ((lazyImage.getBoundingClientRect().top <= window.innerHeight && lazyImage.getBoundingClientRect().bottom >= 0) && getComputedStyle(lazyImage).display !== "none") {
                        chargePhoto(lazyImage);
                        lazyImages = lazyImages.filter(function (image) {
                            return image !== lazyImage;
                        });

                        if(lazyImages.length === 0){
                            document.removeEventListener('scroll',lazyLoad);
                            window.removeEventListener('resize',lazyLoad);
                            window.removeEventListener('orientationchange',lazyLoad);
                        }
                    }
                })

            },200)
        };
        lazyLoad();
        document.addEventListener("scroll", lazyLoad);
        window.addEventListener("resize", lazyLoad);
        window.addEventListener("orientationchange", lazyLoad);

    }

});


function chargePhoto(photo){

    var id = photo.getAttribute('data-id');
    var number = new RegExp(/([0-9])+/);

    if(typeof(id) != 'undefined' && id !== null && number.test(id)){

        var w = parseInt(photo.offsetWidth);
        var h = parseInt(photo.offsetHeight);
        var multiplicator =   (photo.getAttribute('data-multiplicator') !== null) ? parseInt(photo.getAttribute('data-multiplicator')) : null ;
        var photoName = photo.getAttribute('data-name');
        var monochrome = photo.getAttribute('data-monochrome');
        var truncate = photo.getAttribute('data-truncate');
        var url = ((typeof(root) != 'undefined') ? root : '/') +'photo/'+id+'/'+w;

        if(h > 0  ||( multiplicator !== null  && number.test(multiplicator)) || truncate !== null || photoName !== null){
            url += '/'+h;
        }
        if(multiplicator !== null && number.test(multiplicator) || truncate !== null || photoName !== null){
            if(multiplicator === null){
                multiplicator = 100;
            }
            url += '/'+multiplicator;
        }
        if(truncate !== null){
            url += "/1";
        }
        else if(photoName !== null || monochrome !== null){
            url += '/0';
        }
        var preg = new RegExp(/[a-zA-Z0-9]{6}-[a-fA-F0-9]{6}/);

        if(monochrome !== null && preg.test(momochrome)){

            url += '/' + monochrome;
        }

        if(photoName !== null){
            url += '/'+photoName;
        }
        var img = photo.getElementsByTagName('img');
        photo.classList.add('loaded');
        if(photo.classList.contains('paralax')){
            photo.style.background = 'url("'+url+'")';
        }
        else if(img.length){
            img[0].src = url;
        }
    }
    else{
        var src = photo.getAttribute('data-src');
        photo.classList.remove('lazy');
        if(src === null) return;

        if(photo.classList.contains('paralax')){
            photo.style.background = 'url("'+src+'")';
        }
        else{
            photo.src = src;
        }
    }

}