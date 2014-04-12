#Sketch

Sketch is a tiny framework for creating well-structured MVC applications in Wordpress. Right now Sketch is focused on making it easier to create menu pages where site admins can work with data, since that's the interface through which most of an application's heavy lifting is done. We'll add support for more features (custom post types, taxonomies, metaboxes, etc) as we go along.

##What Makes Sketch Unique?

Sketch takes an Object-Oriented approach to interacting with Wordpress. While it's not the first attempt at bringing an MVC style to Wordpress development, it is (to my knowledge) the first to take advantage of Composer autoloading and [Laravel's IoC container](http://laravel.com/docs/ioc). Creating loosely coupled, testable Wordpress applications is incredibly easy with Sketch.

Sketch has a very small footprint. It's intended to work well in environments lacking command-line access, which only means that Sketch's default production dependencies are minimal enough that you don't need to sweat having them under version control. In a way, Sketch is less a framework and more a suggestion for how to structure applications that need to work with Wordpress.

##Getting Started

This Sketch sample app is not on Packagist yet, but the [framework code](http://www.github.com/sketchwp/sketch) is. So for now if you want to use it:

* Clone this repo somewhere into your Wordpress project
* Run `composer update`
* `require "/path/to/Sketch/index.php"` in your plugin / app / functions.php / whateverplace.

Take a look at the controllers, menus, views and routes in the sample app. Feels almost like a proper MVC configuration, doesn't it?

##Unit Testing

One of the main goals of Sketch is to enable Wordpress developers to more easily build testable applications.

Unit testing in Wordpress has always been a huge pain, because you can't use any Wordpress function without instantiating the entire Wordpress application. With Sketch, if any of your classes needs to use a Wordpress function, pass that class an instance of `\Sketch\WpApiWrapper`. That class contains precisely one function, `__call($method, $arguments)`, which simply calls the method passed to it. So instead of using `get_post_meta($id, 'meta_key', true);` in your class, you'd use `$this->wp->get_post_meta($id, 'meta_key', true);`.

That little layer of abstraction is all you need to be able to mock nearly the entire Wordpress application in your unit tests.

##Menus

Normally when you create a Wordpress menu, you use the `add_menu_page()` function and pass a callback that defines everything the menu should display. With Sketch, you create a menu by extending the `\Sketch\WpMenuAbstract` or `\Sketch\WpSubmenuAbstract` class. Define the menu title, slug and permissions etc as properties of the class, and Sketch will take care of the rest. Sketch's menu classes have a `\Sketch\QueryStringRouter` class as a dependency, and `QueryStringRouter->resolve()` is the callback passed to Wordpress when the menu is created at runtime.

If you need to add any actions associated with the menu (i.e., enqueueing public assets), override the menu's `addActions()` method, and add those actions there using the menu's `\Sketch\WpApiWrapper` instance. For example:

    protected function addActions()
    {
        $this->wp->wp_enqueue_script('my_script', 'path/to/my/script.js');
    }

Define your menu classes like you see in the `app/menus` folder, and instantiate them in `index.php` by calling `$app->make('\MyMenu')`;

##Routes

Sketch's router is only intended to create navigation in the Wordpress admin backend. For now, it's not interested in creating front-end routes or "hijacking" Wordpress' native routing system. Instead, it's designed to play nicely with what's already there.

Wordpress's backend admin navigation is almost entirely based on the contents of the query string, so Sketch's router is configured by passing in an associative array of the query string variables that need to be matched. In addition, you will also pass the name of the controller and method that should handle requests matching the route.

E.g., `$router->get(array('page' => 'my_menu_slug'), 'home@index');`

The above example will cause Sketch to look for the class `HomeController`, and run its `index()` method.  Currently this is the only valid syntax for designating a controller. You can also use the methods `$router->post()`, or `$router->any()` to handle GET and POST requests.

Right now, the router is very simple. It can only match routes having identical strings, or `{int}` variables. So if you wanted to edit a particular "foo" item, your route might look something like this:

`$router->get(array('page' => 'my_foo_menu_slug', 'action' => 'edit', 'id' => '{int}'), 'foo@edit');`

Define your routes in `app/routes.php`, and be sure to put your routes between the comments that say "START ROUTES!" and "END ROUTES!". Try not to touch the other stuff unless you really know what you're doing. Since the first given matching route will be selected, define your most specific routes first and your least specific routes last.

##Controllers

Controllers come with an instance of the [Plates](http://www.platesphp.com) template system and the [Symfony Request](http://symfony.com/doc/current/components/http_foundation/introduction.html) object by default. For any other dependencies, use constructor injection and the IoC container will pass them in automatically. Of course, if you are passing in an Interface as a dependency, be sure to use `$app->bind()` in your index.php file to specify which concrete class should be used.

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

A few variables automatically get passed to every view: `nonce_name`, `nonce_action`, `message`, and `errors`. In addition, Sketch comes with a few simple Plates extensions, most notably the `wp()` function, which provides mockable access to all Wordpress' globally namespaced functions. Pass the name of the function as the first argument, and an array of your parameters as the second.

See the [Plates](http://www.platesphp.com) documentation to learn more about what you can do with views.

##Models

The base model provides three ways of interacting with Wordpress data: `\Sketch\WpApiWrapper`,  `\Sketch\WpQueryFactory`, and `\Sketch\WpDbWrapper`. That way, you can fetch your posts and database objects however you like, whether with normal Wordpress functions (e.g., `$this->wp->get_posts()`), by creating a new WP_Query object (`$this->wp_query->make($args)`), or by using `$this->wpdb->get_results($prepared_sql)`.

##Validation

By default, Sketch uses the [Valitron](http://github.com/vlucas/valitron) validation class. You can use Valitron directly in any class, and Sketch also provides a `\Sketch\ValidatorFactory` class so that you can more easily inject validator instances or set up validation as a service.