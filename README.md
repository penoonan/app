#Sketch

Sketch is a teeny-tiny framework for creating well-structured MVC applications in Wordpress. It's not on Packagist yet, so for now if you want to use it, just clone this repo somewhere into your project. Then `require "/path/to/Sketch/index.php"` in your plugin / app / functions.php / whateverplace and run `composer update`.

Right now Sketch is focused on making it easier to create menu pages where site admins can work with data. We'll add support for more features (custom post types, taxonomies, metaboxes, etc) as we go along.

Sketch takes an Object-Oriented approach to interacting with Wordpress. It makes heavy use of [Laravel's IoC](http://laravel.com/docs/ioc) container, [Symfony's Request](http://symfony.com/doc/current/components/http_foundation/introduction.html) object, and the [Plates](http://platesphp.com/) templating system.

Sketch is meant to work well in environments lacking command-line access. Essentially all this means is that Sketch's default production dependencies are minimal enough that you don't need to sweat having them under version control.

##Getting Started

Take a look at the controllers, menus, views and routes in the sample app. Feels almost like a proper MVC configuration, doesn't it?

##Menus

Normally when you create a Wordpress menu, you use the `add_menu_page()` function and pass a callback that defines everything the menu should display. With Sketch, you create a menu by extending the `\Sketch\WpMenuAbstract` or `\Sketch\WpSubmenuAbstract` class. Define the menu title, slug and permissions etc as properties of the class. Sketch's menu classes have a `\Sketch\QueryStringRouter` class as a dependency, and `QueryStringRouter->resolve()` is the callback passed to Wordpress when the menu is created at runtime.

So define your menu classes like you see in the sample, and instantiate them in `index.php` by calling `$app->make('\MyMenu')`;

##Routes
Define your routes in `app/routes.php` (Sketch is heavily inspired by Laravel, you may have noticed). Make sure to put your routes between the comments that say "START ROUTES!" and "END ROUTES!". It's not the prettiest thing, but it's easy and it works. Fair enough?

Wordpress's backend admin navigation is almost entirely based on the contents of the query string, so Sketch's router is configured by passing in an associative array of the query string variables that need to be matched. In addition, you will also pass the name of the controller and method that should handle requests matching the route.

E.g., `$router->get(array('page' => 'my_menu_slug', 'action' => 'create'), 'home@create');`

The above example will cause Sketch to look for the class `HomeController`, and run its `index()` method.  Currently this is the only valid syntax for designating a controller. You can also use the methods `$router->post()`, or `$router->any()` to handle GET and POST requests.

Right now, the router is very simple. It can only match routes having identical strings, or `{int}` variables. So if you wanted to edit a particular "foo" item, your route might look something like this:

`$router->get(array('page' => 'my_foo_menu_slug', 'action' => 'edit', 'id' => '{int}'), 'foo@edit');`

Since the first given matching route will be selected, be sure to define your least specific routes last.

##Unit Testing

The purpose of Sketch is to enable Wordpress developers to more easily build testable applications. Ironically, as of this writing, Sketch itself has 0% code coverage (working on that!).

Unit testing in Wordpress has always been a huge pain, because you can't use any Wordpress function without instantiating the entire Wordpress application. With Sketch, if any of your classes needs to use a Wordpress function, just pass that class an instance of `\Sketch\WpApiWrapper`. That class contains precisely one function, `__call($method, $arguments)`, which simply calls the method passed to it. So instead of using `get_post_meta($id, 'meta_key', true);` in your class, you'd use `$this->wp->get_post_meta($id, 'meta_key', true);`.

That simple layer of abstraction is all you need to be able to mock the entire Wordpress application in your unit tests.

##Controllers

Controllers are instantiated with an instance of the Plates template system and the Symfony request object by default. For any other dependencies, use constructor injection and the IoC container will pass them in automatically. Of course, if you are passing in an Interface as a dependency, be sure to use `$app->bind()` in your index.php file to specify which concrete class should be used.

##Views

See the [Plates](http://www.platesphp.com) documentation to learn about how to use the views.

A few variables automatically get passed to every view: `nonce_name`, `nonce_action`, `message`, and `errors`. In addition, Sketch comes with a few simple Plates extensions, most notably the `wp()` function, which provides mockable access to all Wordpress' globally namespaced functions. Pass the name of the function as the first argument, and an array of your parameters as the second.

##Models

The base model has access to the `\Sketch\WpApiWrapper` class as well as `\Sketch\WpDbWrapper`. That way, you can fetch your posts and database objects however you like, whether with normal Wordpress functions (e.g., `$this->wp->get_posts()`)or using something like `$this->wpdb->get_results($your_well_prepared_query)`.



