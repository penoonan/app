#Sketch

Sketch is a tiny framework for creating well-structured MVC applications in Wordpress. It provides a set of tools and a suggested architecture for building custom Wordpress functionality in a testable, loosely-coupled way.

Sketch is primarily focused on making it easier to create menu pages where site admins can work with data, as well as custom post types, metaboxes, and taxonomies:

    $app = require_once 'app/bootstrap.php';

    // Add our sample menus and submenus
    $app->make('Hello\Hello');
    $app->make('Hello\Submenus\HelloSubmenu');

    // Add our sample post type, with a metabox and a taxonomy
    $app->make('HelloPostType')
        ->addMetabox($app->make('HelloMetabox'))
        ->addTaxonomy($app->make('HelloTaxonomy'));

##Table of Contents

 1. [What Makes Sketch Unique?](#what-makes-sketch-unique)
     * [When Should I Use Sketch](#when-should-i-use-sketch)
 2. [Getting Started](#getting-started)
 3. [Unit Testing](#unit-testing)
 4. [Controllers](#controllers)
 5. [Views](#views)
 6. [Models](#models)
 7. [The Wp Wrapper](#the-wp-wrapper)
 8. [Menus](#menus)
     * [Menu Routes](#menu-routes)
 9. [Custom Post Types](#custom-post-types)
 10. [Metaboxes](#metaboxes)
 11. [Taxonomies](#taxonomies)
 12. [Validation](#validation)
 13. [Service Providers](#service-providers)
 14. [Breaking Changes and Backwards Compatibility](#breaking-changes-and-backwards-compatibility)
 15. [Mad Props](#mad-props)

##What Makes Sketch Unique?

Sketch takes an Object-Oriented approach to interacting with Wordpress. While it's not the first attempt at bringing an MVC style to Wordpress development, it is (to my knowledge) the first to take advantage of Composer autoloading and [Laravel's IoC container](http://laravel.com/docs/ioc).

Sketch has a very small footprint. It's intended to be able to work well in environments lacking command-line access (e.g., shared cloud hosting), which only means that Sketch's default production dependencies are minimal enough that you don't need to sweat having them under version control if need be.

###When Should I Use Sketch?

Sketch is inspired by full stack PHP frameworks like [Laravel](http://laravel.com) and [Silex](http://silex.sensiolabs.org/), but it is in no way meant to serve as a replacement for them. In fact, it's quite rudimentary in comparison! The goal is simply to provide a better way of working with Wordpress, whenever it makes sense to work with Wordpress.

So instead of asking, "when should I use Sketch?", the better question might be, "when should I use Wordpress?" Wordpress has many benefits, but it's foolish to ignore its limitations. Sketch can expand what you can do well with Wordpress, but it can't expand what Wordpress can do well.

That being said - does your site include more than a couple custom post types, metaboxes, taxonomies and/or settings pages? Does it interact with 3rd party APIs? Give Sketch a try!

##Getting Started

The best way to install Sketch is with [Composer](http://www.getcomposer.org). In your terminal, navigate to the root of your Wordpress project, and install Sketch with one command:

* `composer create-project sketch/app -s dev your-app-name-goes-here --prefer-dist`

Then, from inside your theme's `functions.php` file, or your plugin, or wherever you wish to instantiate Sketch, just require `path/to/sketch/index.php` and start building.

##Unit Testing

One of the main goals of Sketch is to enable Wordpress developers to more easily test Wordpress functionality.

Unit testing in Wordpress has always been a huge pain, because you can't use any Wordpress function without instantiating the entire Wordpress application. With Sketch, if any of your classes needs to use a Wordpress function, pass that class an instance of `\Sketch\WpApiWrapper`. That class contains precisely one function, `__call($method, $arguments)`, which simply calls and returns the method passed to it. So instead of using `get_post_meta($id, 'meta_key', true);` in your class, you'd use `$this->wp->get_post_meta($id, 'meta_key', true);`.

It takes a bit of discipline, but that little layer of abstraction is all you need to be able to mock any of Wordpress's "globally namespaced" functions in your unit tests.

##Controllers

Sketch's default controllers come populated with an instance of the template system (Plates, by default) and the [Symfony Request](http://symfony.com/doc/current/components/http_foundation/introduction.html) object. For any other dependencies, use type-hinting and constructor injection - the IoC container will pass them in automatically. Of course, if you are type-hinting an Interface as a dependency, be sure to use `$app->bind('FooInterface', 'FooConcrete')` in your index.php file to tell the IoC container which concrete class it should inject.

Say you want to make a controller that grabs `page` from the query string (i.e., the menu slug) and passes it to the view. Here's how you would do that using Sketch's default controllers:

    Class HomeController extends \Sketch\WpBaseController {

        public function index()
        {
            $data = array(
                'page' => $this->request->query->get('page')
            );

            $this->render('home', $data);
        }
    }

If you do not wish to use Sketch's default controllers, you don't have to! You can register a different controller type using a [service provider](#service-providers). Feel free to ask for help with this! The docs for that have not yet been written.

##Views

For a view corresponding to the above controller example, create a file called `app/views/home.php`. To output the `page` variable, use `<?= $this->page ?>` anywhere in your template.

A few variables automatically get passed to every view: `nonce_name`, `nonce_action`, `message`, and `errors`. In addition, Sketch comes with a few simple Plates extensions, most notably the `wp()` function, which provides access to `\Sketch\WpApiWrapper`. Pass the name of the function as the first argument, and an array of your parameters as the second.

See the [Plates](http://www.platesphp.com) documentation to learn more about what you can do with default views.

Like Sketch's default controller, Plates is registered as a [service provider](#service-providers). It is not too difficult to swap it out for the templating engine of your choice. Feel free to ask for help with this! The docs for that have not yet been written.

##Models

The base model provides three ways of interacting with Wordpress data: `\Sketch\WpApiWrapper`,  `\Sketch\WpQueryFactory`, and `\Sketch\WpDbWrapper`. That way, you can fetch your posts and database objects however you like, whether with normal Wordpress functions (e.g., `$this->wp->get_posts()`), by creating a new WP_Query object (`$this->wp_query->make($args)`), or by using `$this->wpdb->get_results($prepared_sql)`.

If that's too simplistic for your project, remember you are in no way required to use Sketch's base model. You can easily write your own base model class or even use something like the [Eloquent ORM](http://www.edzynda.com/use-laravels-eloquent-orm-outside-of-laravel/) if keeping a small footprint isn't a high priority.

##Menus

Normally when you create a Wordpress menu, you use the `add_menu_page()` function and pass a callback that defines everything the menu should display. With Sketch, you create a menu by extending the `\Sketch\Menu\WpMenuAbstract` or `\Sketch\Menu\WpSubmenuAbstract` class. Define the menu title, slug and permissions etc as properties of the class, and Sketch will take care of the rest. Sketch's menu classes have a routing class as a dependency, and `this->router->resolve()` is the callback passed to Wordpress when the menu is created at runtime.

If you need to add any actions associated with the menu (i.e., enqueueing public assets), override the menu's `addActions()` method, and add those actions there using the menu's `\Sketch\WpApiWrapper` instance. For example:

    protected function addActions()
    {
        $this->wp->wp_enqueue_script('my_script', 'path/to/my/script.js');
    }

Define your menu classes like you see in the `app/menus` folder, and instantiate them in `index.php` by calling `$app->make('MyMenu')`;

###Menu Routes

Sketch's menu router is primarily intended to create navigation for menu pages in the Wordpress admin backend. For now, it's not interested in creating front-end routes or "hijacking" Wordpress' native routing system. Instead, it's designed to play nicely with what's already there.

Wordpress's admin menu navigation is largely based on the contents of the query string, so Sketch's router is configured by passing in either an associative array of the query string variables that need to be matched, or an actual query string. In addition, you will also pass the name of the controller and method that should handle requests matching the route.

These examples are all the same:

* `$app['router']->get('?page=my_menu_slug&action=index', 'HomeController@index');`
* `$app['router']->get('action=index&page=my_menu_slug', 'HomeController@index');`
* `$app['router']->get(['page' => 'my_menu_slug', 'action' => 'index'], 'HomeController@index');`

The above examples will cause Sketch to look for the class `HomeController`, and run its `index()` method. You may also pass in a callback function instead of a controller reference. The router has methods `post()`, or `any()` to handle GET and POST requests. For methods other than GET and POST, use `register('METHOD', $params, $controller)`. Note that, whether you pass a query string or an array, the order of the variables passed does not matter. Also, when passing a query string, you can include or exclude the '?'.

Right now, the router is very simple. It can only match identical strings, or `{int}` variables. So if you wanted to edit a particular "foo" item, your route might look something like this:

`$app['router']->get(array('page' => 'my_foo_menu_slug', 'action' => 'edit', 'id' => '{int}'), 'FooController@edit');`

Define your menu routes in `app/menus/routes.php`. Since the first matching route will be selected, define your most specific routes first and your least specific routes last.

##Custom Post Types

Similar to menus, you can create a new custom post type by extending the `\Sketch\CustomPostType\BaseCustomPostType` class. Again, define the post type's arguments as properties on the class. You can define `$args`, `$labels`, and `$rewrite` variables as their own separate arrays. Sketch will add the `$labels` and `$rewrite` parameters to the `$args` array automatically when creating the post type at run-time. This arrangement does distort the normal "Wordpress way" a bit - but it seemed like the most readable solution.

Add metaboxes and taxonomies to your Custom Post Type when you instantiate it in `index.php` (see the first code sample at the top of the page).

##Metaboxes

Sketch metaboxes are a lot like custom post types and menus. Create a metabox by extending the `\Sketch\Metabox\BaseMetabox` class. Define the metabox arguments as parameters on the class.

But wait - that's not all! In addition to defining standard metabox arguments as class parameters, you must also specify a controller and method to handle the metabox's business logic.

Here is code for the basic metabox that ships with the sample Sketch app:

    class HelloMetabox extends BaseMetabox {
        protected
            $id = 'hello_metabox',
            $post_type = 'hello_post_type',
            $callback_controller = 'HelloController@metabox'
        ;
    }

Just like with menu routes, the metabox's controller is resolved out of the IoC container and has access to both the Symfony Request object and the Plates template system. In addition, (like all WordPress metaboxes) it will also be passed information about the currently displayed post, and the metabox itself. Here is our sample metabox controller:

    public function metabox($post, $metabox)
    {
        $data = [
            'post' => $post,
            'metabox' => $metabox
        ];
        $this->render('hello::metabox', $data);
    }

There are two ways to instantiate this metabox in your application. The first is illustrated at the top of this page - by calling `->addMetabox($app->make('HelloMetabox'))` on the post type that the metabox should be added to. Note that, if you use this method, you do not need to set a `$post_type` parameter on the metabox itself. It will be set automatically when you add it to the post type object.

The second method is to call the metabox's `->manuallyAddAction()` function after you've instantiated it in your Sketch app's `index.php` file. For example:

     $app->make('HelloMetabox')->manuallyAddAction();

 If you use this method, then the `$post_type` parameter must be set. Otherwise, it won't display anywhere.

##Taxonomies

To create a taxonomy, extend the `\Sketch\Taxonomy\BaseTaxonomy` class, and add the taxonomy's parameters in the same way as for a custom post type. In addition to the `$args`, `$labels` and `$rewrite` arrays, taxonomies can also have a `$capabilities` array.

##Validation

Sketch ships with the [Valitron](http://github.com/vlucas/valitron) validation class by default. You can use Valitron directly in any class, and Sketch also provides a `\Sketch\ValidatorFactory` class so that you can more easily inject validator instances or set up validation as a service.

##Service Providers

Service providers are a great place to put bootstrap code for third party services and register them on the Sketch Application. To create a service provider, first create a new class that implements `\Sketch\ServiceProviderInterface`.

Sketch service providers only need to implement one method: `register(\Sketch\Application $app)`. That method is a good place to register bindings on the application. For example:

    use \Sketch\Application;
    use \Sketch\ServiceProviderInterface;

    class MyProvider implements ServiceProviderInterface {

        public function register(Application $app) {
            $app->bind('FooInterface', function() {
                $config = array('foo' => 'bar');
                return new FooClass($config);
            });
        }
    }

You may also pass in an array of configuration values as the second parameter of the register method:

    public function register(Application $app, $config) {
        $app->bind('FooInterface', function() use($config) {
            return new FooClass($config);
        });
    }

Register your service providers in `Sketch\index.php`, right next to where you instantiate menus, custom post types, etc. This is also where you would pass in those configuration values, if the situation called for it:

    $app = require_once 'app/bootstrap.php';

    $config = array('foo' => 'bar');
    $app->register(new MyProvider(), $config);

To see more service provider examples, look at the `app/bootstrap.php` file where the Plates template system and Sketch's default controller factory is registered.

##Breaking Changes and Backwards Compatibility

Sketch is in some sort of alpha or beta stage, and some changes to the core framework will necessitate changes to the application.

If you are using Sketch (which you are not, according to packagist), and running `composer update` causes your application to break (please never update production dependencies without testing locally first), here are a few things you can do:

 1. Copy the code from [the most current Sketch bootstrap file](https://github.com/sketchwp/app/blob/master/app/bootstrap.php) into your application's `app/bootstrap.php` file.
 2. Update controller references in [metaboxes](#metaboxes) and in `app/menus/routes.php`. The `Sketch\ControllerDispatcher` class was recently changed so that you must now pass the controller's full class name to the router. I.e., change `hello@index` to `HelloController@index`.
 3. If #1 and #2 don't work, create a github issue or, if it's urgent, [contact me directly](https://github.com/pnoonan). I'm happy to help!

##Mad Props

Sketch was created at [ArcStone](http://www.arcstone.com), a web development, design and marketing agency located in Minneapolis, Minnesota.