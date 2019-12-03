(function(){
  
  /* Script Pattern */
  const scriptPatern = {
    package: null,
    src: null,
    callback : null,
    loaded : false,
    require: [],
    parent : false,
    preload: false
  }
  /* ScriptLoader pattern */
  const defaults = {
    scripts: [],
  }
    
  /* ScriptLoader __constructor*/
  this.ScriptLoader = function(opts) {
    
    this.inProgress = false
    this.scripts = new Array();
   
    var config = {};
    
    /*Add script in ScriptLoader object*/
    this.add = function(script){
      var scriptLoader = this;
      // if script is array , forEach and call add
      if(typeof(script.push) !== 'undefined'){
        script.forEach(function(object){
          scriptLoader.add(object);
        });
        return;
      }
      
      if(typeof(script) !== 'object')
        return;
      // Define defaults
      setDefaults(scriptPatern,script);
      
      // if object don't have script and script.src is not null
      if(script.src !== null && !this.has(script)){
        // If script has requirements
        if(script.require.length > 0){
          // Load requirements
          script.require.forEach(function(require){
            require.sources.forEach(function(source){
              source.parent = true;
              scriptLoader.add(source);
            }) 
          })
        }
        // Add script
        this.scripts.push(script);
      }
 
    }
    
    
   
    /* Object has script */
    this.has = function(script){
      /* Object comparaison */

      if(typeof(script) !== 'object')
        return false;
      
      result = false;
      this.scripts.forEach(function(object){
        if(object.src === script.src) {
          result = true;
        }
      });

      return result;
    }
    /* load Alls scripts not loaded */
    this.load = function(){


      if(this.scripts.length === 0 || this.inProgress === true)
        return;
      this.inProgress = true;

      
      loadScript.call(this);
    }
    
    if(typeof(opts) !== 'object')
      return;
    
    setDefaults.call(this,defaults,opts);
    configure.call(this,opts);
    
  }
  
  
  /* Load ScriptLoader.scripts[offset] script */
  function loadScript(offset){
    var scriptLoader = this;
    if(offset === undefined)
      offset = 0;
    var script = this.scripts[offset];
    if(script === undefined){
      this.inProgress = false;
      return;      
    }
  
    if(script.loaded === true){
      loadScript.call(scriptLoader,offset + 1);
      return
    }

    script.loaded = true;
    const patterns = {
      css: [
        new RegExp(/\.css/),
        new RegExp(/fonts.googleapis.com/)
      ],
      js: [
          new RegExp(/\.js/),
          new RegExp(/player_api/),
          new RegExp(/gtag\/js/)
      ]
    };
    var css = false;
    var js = false;
    patterns.css.forEach(function(pattern){
      if(pattern.test(script.src)){
        css = true;
        return;
      }
    });
    
    if(css !== true){
      patterns.js.forEach(function(pattern){
        if(pattern.test(script.src)){
          js = true;
          return;
        }
      });
    }
    if(css === false && js === false){
      loadScript.call(this,offset +1);
      return;
    }
    if(script.preload){
      var preload = document.createElement('link');
        preload.rel = "preload";
        preload.as = (css === true) ? "style" : 'script';
        preload.href = script.src;
        document.head.appendChild(preload);
    }
    if(css === true){
      var element = document.createElement('link');
      element.href = script.src;
      element.rel = 'stylesheet';
      element.media = 'all';

      document.head.appendChild(element);
      
    }else{
      var element = document.createElement('script');
      element.src = script.src;
      element.type = "text/javascript";
      element.async = true;
      
      document.body.appendChild(element);     
    }
    element.onload = function(){

        if(typeof(script.callback) === 'function')
            script.callback();
        if(script.parent === true)
          loadScript.call(scriptLoader,offset + 1);
    }
    if(script.parent === false){
      loadScript.call(scriptLoader,offset + 1);
      return;
    }

  }
    /* Define opts by defaults */
    function setDefaults(defaults,opts){
      for(var key in defaults){
        if(!has.call(opts,key)){
          opts[key] = defaults[key]
        }
      }
    }
    /* Configure object with options */
    function configure(opts){ 
      for(var key in opts){
        if(has.call(defaults,key)){
          if(key === 'scripts'){
          
            this.add(opts[key]);
          } else{
            this[key] = opts[key]                  
          }
        }
      }
    }
  
  /* object send has key */
  function has(key){

    for(var _key in this){
      if(_key === key)
        return true;
    }
    return false;
  }
})();