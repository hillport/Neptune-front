# ScriptLoader

ScriptLoader is a Javascript library for async script loading with dependency tree.

## Installation

Use the package manager [bower](https://bower.io/) to install ScriptLoader.

```bash
bower install santanas2b/script-loader
```

## Usage

### Load the javascript in your html 
```html
<script src="path_to_script_loader_folter/dist/scriptLoader.min.js" type="text/javascript"></script>
```
### Initialize scriptLoader object
```javascript
var scriptLoader = new ScriptLoader();

```

### Add yours scripts
```javascript
// add one script
scriptLoader.add({
    src: "https://ajax.googleapis.com/ajax/libs/d3js/5.12.0/d3.min.js"
});
// add many scripts
scriptLoader.add([
    {
        src: "js/custom.js",
        callback: function(){
            // Callback has called when script is loaded
        }
    },
    {
        src: "css/custom.css"
    },
    {
        src: "js/toto.js"
    }
]);
```
### You can add script with requirements
When you add requirements , theys sources loaded before your script.
```javascript
    scriptLoader.add({
        src: "js/toto.js",
        require: [
            {
                name: "owlCarousel",
                sources: [
                    {
                        src: "js/owl.js",
                        require: [
                            {
                                name: "jQuery",
                                sources: [
                                    {
                                        src:"https://ajax.googleapis.com/ajax/libs/d3js/5.12.0/d3.min.js",
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        src: "css/owl.css",
                    }
                ]
            }
        ],
        callback: function(){
            // callback when script loaded
        }
    })
```

### Last step : Launch the load 
The Script Loader does a check not to add the same script multiple times.
The loader script does not load the same script twice, even if you run the load function multiple times.

The loader script checks that a load is not running when launching the load function, so we can launch it after adding scripts, it will not impact performance.

```javascript
    scriptLoader.load();
```

### You can tell if the script already exists in the scriptLoader
```javascript
    // This function return boolean
    scriptLoader.has({
        src: "toto.js"
    });
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)
