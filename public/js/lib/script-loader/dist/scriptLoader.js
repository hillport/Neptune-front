function loadScript(tab,index,call)
{
  if(typeof(index) == 'function')
  {

    call = index;index = 0;
  }
  else if(typeof(index) == typeof(undefined)) {index = 0;}

  if(typeof(tab) === 'string') tab = new Array(tab);
  if(typeof(tab[index]) === 'object')
  {
    loadScript(tab[index][0],0,tab[ index][1])
    var tmp = tab[index];
    tab[index] = tab[index][0];
    var tmp_callback = tab[index][1];
  }
  if(typeof(tab[index]) === 'function')
  {
    tab[index]();
    index++;
  }
  if(typeof(tab[index]) != typeof(undefined) )
  {

    var patt = new RegExp(/\.js/);
    var pattcss = new RegExp(/\.css/);
    var yt = new RegExp(/player_api/);
    var analitics = new RegExp(/gtag\/js/);

    
    if(index < tab.length)
    {

      if(patt.test(tab[index])|| yt.test(tab[index]) || analitics.test(tab[index]))
      {
        var element = document.createElement("script");
        element.src = tab[index];
        element.type = "text/javascript";
        element.defer = "defer";
        document.body.appendChild(element);

        element.onload = function()
        { 
          var ind = index +1;
          loadScript(tab,ind,call);
          if(typeof(tmp_callback) === "function")
            tmp_callback();

        }
      }
      else if (pattcss.test(tab[index]))
      {
        var lien   = document.createElement('link');
        lien.href   = tab[index];
        lien.rel = "stylesheet";
        lien.media = "all";;
        document.head.appendChild(lien);
        lien.onload = function()
        {
          var ind = index+1;
          loadScript(tab,ind,call);
          if(typeof(tmp_callback) === "function")
            tmp_callback();
        }
      }  
      else
      {
        var ind = index+1;
        loadScript(tab,ind,call);
      }
    }
  }
  else
  {
    if(typeof(call) === "function")
    {
      call();
    }
  }
}