## This component is still not ready for use, I will update it in a few days

## Blade menu
I believe that building menus should be as simple as defining routes in your laravel application. Also it easier to customize blade template than php code that generates menu HTML.

## Getting Started
Menus can be defined in `app/routes.php` or any other place you wish as long as it is auto loaded when a request hits your application.

Here is a basic usage:

```php
Menu::make('main', function()
{
	Menu::route('home', 'Home page');
	Menu::route('about', 'About');
	Menu::submenu('Submenu', [], function()
	{
		Menu::url('/section/item1', 'Item 1');
		Menu::url('/section/item2', 'Item 2');
	});
});
```

**Rendering**
To render menu just include blade template with menu instance as argument

```php
@include('parts.menu', ['menu' => Menu::get('main')])
```

## Installation
In the `require` key of `composer.json` file add `"poma/blade-menu": "dev-master"`:

```
...
"require": {
	"laravel/framework": "5.1.*",
	"poma/blade-menu": "dev-master"
}
```

Run the composer update command:

```bash
composer update
```

Now append Laravel Menu service provider to  `providers` array in `config/app.php`.

```php
'providers' => [
    Illuminate\Foundation\Providers\ArtisanServiceProvider::class,
    Illuminate\Auth\AuthServiceProvider::class,
    ...
    Poma\BladeMenu\MenuServiceProvider::class,
],
```

At the end of `config/app.php` add `'Menu'    => Poma\BladeMenu\MenuFacade::class` to the `aliases` array:

```php
'aliases' => [
    'App'        => Illuminate\Support\Facades\App::class,
    'Artisan'    => Illuminate\Support\Facades\Artisan::class,
    ...
    'Menu'       => Poma\BladeMenu\MenuFacade::class,
],
```

This registers the package with Laravel and creates an alias called `Menu`.
