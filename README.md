ViewAutoload for CakePHP
========================

Load .js files with views automatically!

## Loading view-related .js files ##
This plugin lets you create corresponding .js files for your views and include 
them automatically in the body (the 'script' block) of the current page:

**directory structure**
* app
    * View
        * Article
            * view.ctp
            * view.js
            * basic.js

The .js files should be in the same directory as the view file.

**controller code**

```php
class ArticleController extends AppController {
    public $components = array(
        'ViewAutoload.JsAutoload'
    );

    public function view($id){
        $this->set('article', $this->Article->read(null, $id));
    }
}
```

**view.ctp**

        <h1><?php echo $article['Atricle']['title'] ?></h1>

**view.js**
```js
alert("You are about to read something amazing.");
```

**layout.ctp**
```html
<head>
...
<?php echo $this->fetch('script'); //fetches the script block, where the view.js is included ?>
...
```

The rendered HTML should now look like:

```html
<html>
<head>
...
<script type="text/javascript">
alert("You are about to read something amazing.");
</script>
...
<body>
...
<h1>An article to be amazed</h1>
...
```

##advantages##

* Easy setup - just define the component, write the {view_name}.js file and enjoy.
* Ease of maintenance - each view has it's corresponding .js next to it in the views folder, making development easier and organized.
* Code safety - the code is included into the page, rather than linked to it from a webroot file, which is great if you do not wish to publicly reveal the structure of your app to nosey people (i.e. admin pages)
* Dynamic js files - you can use PHP within your .js file and access the current viewVars for the currently rendered view.
* Light - you can attach the component only for controllers which use it, stop automatic view detection and load one or multiple .js files manually from the controller action.

##disadvantages##

* Not-entirely conventional - .js files are not really supposed to be in the View folder, but it makes sense to me to put them there for small pieces of view-related code.
* Size and Caching - if you have big .js files and need them to be cached by the browser, you should link to a .js file from the webroot and not include it in the page.
* Embedded script block - pitfalls are many.

##Options##
The JsAutoload component takes 3 options in the settings array:
* 'block' ('script') - which view block should the file be written to
* 'eval' (false) - setting eval to true will parse any PHP your .js file has and pass the set viewVars to it
* 'auto' (true) - if auto is false, you need to load the js manually, as the component will not check if the .js file exists.

Each of these settings can be overriden in the `JsAutoload::loadFile($name, $options)` options array.

```php
class ArticleController extends AppController {
    public $components = array(
        'ViewAutoload.JsAutoload' => array(
            'block' => 'pagejs', // default 'script'
            'eval' => false,     // default false
            'auto' => false      // default true
        )
    );
    public function view($id){
        $this->set('article', $this->Article->read(null, $id));
        //"auto" option is set to false, we need to load the .js manually:
            //the file will be evaluated, $article view var will be available
            $options = array( 'eval' => true );
            //you can use 'view' OR 'view.js' as the first argument
            $this->JsAutoload->loadFile('view', $options);
    }
```

**view.js**

```php
var article_id = <?php echo $article['Article']['id']; ?>;
console.log('Article id: ' + article_id);
<?php if ($article['Article']['flagged']): //article is flagged for language ?>
    if (!confirm('<?php echo $article['Article']['id']; ?> uses strong language. Do you wish to continue?')){
        window.location = '<?php echo Router::url(array('controller' => 'articles'));?>';
    }
<?php endif; ?>
```

If `autoload` is set to true and `eval` to false, you can still force evaluation by loading the file
manually before action ends.

```php
public function view($id) {
    // this evaluates the view.js as a php file, 
    // regardless of the component settings
    $this->JsAutoload->loadFile('view', array('eval' => true));
}

##Setup##
        cd MyProject/app/Plugin/
        git clone https://github.com/dizyart/cakephp-viewautoload.git
        // add this to bootstrap.php:
        CakePlugin::load('ViewAutoload');

See above how to attach the component to your controller class.
