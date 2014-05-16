#Sketch

Sketch is a tiny framework for creating well-structured MVC applications in Wordpress. Right now Sketch is focused on making it easier to create menu pages where site admins can work with data, as well as custom post types, metaboxes, and taxonomies:

    $app = require_once 'app/bootstrap.php';

    // Add our sample menus and submenus
    $app->make('Hello\Hello');
    $app->make('Hello\Submenus\HelloSubmenu');

    // Add our sample post type, with a metabox and a taxonomy
    $app->make('HelloPostType')
        ->addMetabox($app->make('HelloMetabox'))
        ->addTaxonomy($app->make('HelloTaxonomy'));

We'll add support for more features as we go along.

 * [What Makes Sketch Unique?](#what-makes-sketch-unique)
 * [Getting Started](#getting-started)
 * [Unit Testing](#unit-testing)
 * [Menus](#menus)
     * [Menu Routes](#menu-routes)
 * [Custom Post Types](#custom-post-types)
 * [Metaboxes](#metaboxes)
 * [Taxonomies](#taxonomies)
 * [Controllers](#controllers)
 * [Views](#views)
 * [Models](#models)
 * [Validation](#validation)

##What Makes Sketch Unique?

Sketch takes an Object-Oriented approach to interacting with Wordpress. While it's not the first attempt at bringing an MVC style to Wordpress development, it is (to my knowledge) the first to take advantage of Composer autoloading and [Laravel's IoC container](http://laravel.com/docs/ioc). Creating loosely coupled, testable Wordpress applications is incredibly easy with Sketch.

Sketch has a very small footprint. It's intended to work well in environments lacking command-line access, which only means that Sketch's default production dependencies are minimal enough that you don't need to sweat having them under version control if need be.

##Getting Started

The best way to install Sketch is with [Composer](http://www.getcomposer.org). In your terminal, navigate to the root of your Wordpress project, and install Sketch with one command:

* `composer create-project sketch/app -s dev your-app-name-goes-here --prefer-dist`

Then, from inside your theme's `functions.php` file, or your plugin, or wherever you wish to instantiate Sketch, just require `path/to/sketch/index.php` and start building.

##Unit Testing

One of the main goals of Sketch is to enable Wordpress developers to more easily build testable applications.

Unit testing in Wordpress has always been a huge pain, because you can't use any Wordpress function without instantiating the entire Wordpress application. With Sketch, if any of your classes needs to use a Wordpress function, pass that class an instance of `\Sketch\WpApiWrapper`. That class contains precisely one function, `__call($method, $arguments)`, which simply calls and returns the method passed to it. So instead of using `get_post_meta($id, 'meta_key', true);` in your class, you'd use `$this->wp->get_post_meta($id, 'meta_key', true);`.

It takes a bit of discipline, but that little layer of abstraction is all you need to be able to mock any of Wordpress's "globally namespaced" functions in your unit tests.

##Menus

Normally when you create a Wordpress menu, you use the `add_menu_page()` function and pass a callback that defines everything the menu should display. With Sketch, you create a menu by extending the `\Sketch\Menu\WpMenuAbstract` or `\Sketch\Menu\WpSubmenuAbstract` class. Define the menu title, slug and permissions etc as properties of the class, and Sketch will take care of the rest. Sketch's menu classes have a routing class as a dependency, and `this->router->resolve()` is the callback passed to Wordpress when the menu is created at runtime.

If you need to add any actions associated with the menu (i.e., enqueueing public assets), override the menu's `addActions()` method, and add those actions there using the menu's `\Sketch\WpApiWrapper` instance. For example:

    protected function addActions()
    {
        $this->wp->wp_enqueue_script('my_script', 'path/to/my/script.js');
    }

Define your menu classes like you see in the `app/menus` folder, and instantiate them in `index.php` by calling `$app->make('MyMenu')`;

###Menu Routes

Sketch's router is primarily intended to create navigation for menu pages in the Wordpress admin backend. For now, it's not interested in creating front-end routes or "hijacking" Wordpress' native routing system. Instead, it's designed to play nicely with what's already there.

Wordpress's admin menu navigation is largely based on the contents of the query string, so Sketch's router is configured by passing in either an associative array of the query string variables that need to be matched, or an actual query string. In addition, you will also pass the name of the controller and method that should handle requests matching the route.

These examples are all the same:

* `$app['router']->get('?page=my_menu_slug&action=index', 'home@index');`
* `$app['router']->get('action=index&page=my_menu_slug', 'home@index');`
* `$app['router']->get(array('page' => 'my_menu_slug', 'action' => 'index'), 'home@index');`

The above examples will cause Sketch to look for the class `HomeController`, and run its `index()` method. You may also pass in a callback function instead of a controller reference. The router has methods `$router->post()`, or `$router->any()` to handle GET and POST requests. For methods other than GET and POST, use `$router->register('METHOD', $params, $controller)`. Note that, whether you pass a query string or an array, the order of the variables passed does not matter. Also, when passing a query string, you can include or exclude the '?'.

Right now, the router is very simple. It can only match identical strings, or `{int}` variables. So if you wanted to edit a particular "foo" item, your route might look something like this:

`$router->get(array('page' => 'my_foo_menu_slug', 'action' => 'edit', 'id' => '{int}'), 'foo@edit');`

Define your menu routes in `app/menus/routes.php`. Since the first given matching route will be selected, define your most specific routes first and your least specific routes last.

##Custom Post Types

Similar to menus, you can create a new custom post type by extending the `\Sketch\CustomPostType\BaseCustomPostType` class. Again, define the classes' arguments as properties on the class. You can define `$args`, `$labels`, and `$rewrite` variables as their own separate arrays. Sketch will add the `$labels` and `$rewrite` parameters to the `$args` array automatically when creating the post type at run-time. This arrangement does distort the normal "Wordpress way" a bit - but it seemed like the most readable solution.

Add metaboxes and taxonomies to your Custom Post Type when you instantiate it in `index.php` (see the first code sample at the top of the page).

##Metaboxes

Sketch metaboxes are a lot like custom post types and menus. Create a metabox by extending the `\Sketch\Metabox\BaseMetabox` class. Define the metabox arguments as parameters on the class.

But wait - that's not all! In addition to defining standard metabox arguments as class parameters, you must also specify a controller and method to handle metabox's business logic.

Here is code for the basic metabox that ships with the sample Sketch app:

    class HelloMetabox extends BaseMetabox {
        protected
            $id = 'hello_metabox',
            $post_type = 'hello_post_type',
            $callback_controller = 'hello@metabox'
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

There are two ways to instantiate this metabox in your application. The first is illustrated above - by calling `->addMetabox($app->make('HelloMetabox'))` on the post type that the metabox should be added to. Note that, if you use this method, you do not need to set a `$post_type` parameter on the metabox itself. It will be set automatically when you add it to the post type object.

The second method is to call the metabox's `->manuallyAddAction()` function after you've instantiated it in your Sketch app's `index.php` file. For example:

     $app->make('HelloMetabox')->manuallyAddAction();

 If you use this method, then the `$post_type` parameter must be set. Otherwise, nothing will display!

##Taxonomies

To create a taxonomy, extend the `\Sketch\Taxonomy\BaseTaxonomy` class, and add the taxonomy's parameters in the same way as for a custom post type. In addition to the `$args`, `$labels` and `$rewrite` arrays, taxonomies can also have a `$capabilities` array.

##Controllers

Controllers come with an instance of the [Plates](http://www.platesphp.com) template system and the [Symfony Request](http://symfony.com/doc/current/components/http_foundation/introduction.html) object by default. For any other dependencies, use type-hinting and constructor injection - the IoC container will pass them in automatically. Of course, if you are type-hinting an Interface as a dependency, be sure to use `$app->bind()` in your index.php file to specify which concrete class should be used.

Say you want to make a controller that grabs `page` from the query string (i.e., the menu slug) and passes it to the view. Here's how you would do that:

    Class HomeController extends \Sketch\WpBaseController {

        public function index()
        {
            $data = array(
                'page' => $this->request->query->get('page')
            );

            $this->render('home', $data);
        }
    }

##Views

For a view corresponding to the above controller example, create a file called `app/views/home.php`. To output the `page` variable, use `<?= $this->page ?>` anywhere in your template.

A few variables automatically get passed to every view: `nonce_name`, `nonce_action`, `message`, and `errors`. In addition, Sketch comes with a few simple Plates extensions, most notably the `wp()` function, which provides access to `\Sketch\WpApiWrapper`. Pass the name of the function as the first argument, and an array of your parameters as the second.

See the [Plates](http://www.platesphp.com) documentation to learn more about what you can do with views.

##Models

The base model provides three ways of interacting with Wordpress data: `\Sketch\WpApiWrapper`,  `\Sketch\WpQueryFactory`, and `\Sketch\WpDbWrapper`. That way, you can fetch your posts and database objects however you like, whether with normal Wordpress functions (e.g., `$this->wp->get_posts()`), by creating a new WP_Query object (`$this->wp_query->make($args)`), or by using `$this->wpdb->get_results($prepared_sql)`.

If that's too simplistic for your project, remember you are in no way required to use Sketch's base model. You can easily write your own base model class or even use something like the [Eloquent ORM](http://www.edzynda.com/use-laravels-eloquent-orm-outside-of-laravel/) if keeping a small footprint isn't a high priority.

##Validation

By default, Sketch uses the [Valitron](http://github.com/vlucas/valitron) validation class. You can use Valitron directly in any class, and Sketch also provides a `\Sketch\ValidatorFactory` class so that you can more easily inject validator instances or set up validation as a service.

Sketch was created at [ArcStone](http://www.arcstone.com), a web development and design agency in Minneapolis.