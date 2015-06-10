<?php

namespace Poma\BladeMenu;

use Closure;
use Countable;
use Illuminate\Config\Repository;
use Illuminate\View\Factory;

class Menu implements Countable
{
    /**
     * The menus collections.
     *
     * @var array
     */
    protected $menus = array();

    /**
     * Pointer to current root menu item
     *
     * @var MenuItem root
     */
    protected $root;

    /**
     * Stack of parent menu items
     *
     * @var \App\Services\Menu\MenuItem[]
     */
    protected $parentStack = array();

    /**
     * The constructor.
     *
     * @param Factory $views
     * @param Repository $config
     */
    public function __construct(Factory $views, Repository $config)
    {
        $this->views = $views;
        $this->config = $config;
    }

    /**
     * @param array $properties
     * @return static
     */
    protected function makeItem(array $properties)
    {
        $item = MenuItem::make($properties);
        $item->root = $this->root ?: $item;
        $item->level = count($this->parentStack);
        $item->parent = end($this->parentStack);
        return $item;
    }

    /**
     * Adds item to menu
     *
     * @param MenuItem $item
     * @return MenuItem
     */
    protected function addItem(MenuItem $item)
    {
        if ($item->level == 0) {
            // root item
            $this->menus[$item->name] = $item;
        } else {
            end($this->parentStack)->children[] = $item;
        }
        return $item;
    }

    /**
     * @param array $properties
     * @return MenuItem
     */
    protected function makeAndAdd(array $properties)
    {
        return $this->addItem($this->makeItem($properties));
    }

    /**
     *
     *
     * @param MenuItem $item
     * @param callable $callback
     * @return MenuItem
     */
    protected function addSubmenu(MenuItem $item, Closure $callback)
    {
        $this->addItem($item);
        $this->parentStack[] = $item;

        call_user_func($callback);

        array_pop($this->parentStack);
        return $item;
    }

    /**
     * Make new menu.
     *
     * @param  string $name
     * @return \App\Services\Menu\MenuItem
     */
    public function make($name, Closure $callback = null)
    {
        $this->root = null;
        $this->parentStack = array();

        $item = $this->makeItem(['name' => $name, 'type' => 'root']);
        $this->root = $item;

        $this->addSubmenu($item, $callback);

        return $item;
    }



    /**
     * Create new menu with dropdown.
     *
     * @param $title
     * @param array $attributes
     * @param callable $callback
     * @return $this
     */
    public function submenu($title, array $attributes, Closure $callback)
    {
        $item = $this->makeItem([
            'type' => 'submenu',
            'title' => $title,
            'attributes' => $attributes
        ]);

        $this->addSubmenu($item, $callback);

        return $item;
    }

    /**
     * Register new menu item using registered route.
     *
     * @param $route
     * @param $title
     * @param array $parameters
     * @param array $attributes
     * @return static
     */
    public function route($route, $title, $parameters = array(), $attributes = array())
    {
        return $this->makeAndAdd(array(
            'type' => 'item',
            'route' => array($route, $parameters),
            'title' => $title,
            'attributes' => $attributes
        ));
    }


    /**
     * Add new child menu.
     *
     * @param  array $attributes
     * @return \App\Services\Menu\MenuItem
     */
    public function add(array $attributes = array())
    {
        return $this->makeAndAdd($attributes);
    }

    /**
     * Register new menu item using url.
     *
     * @param $url
     * @param $title
     * @param array $attributes
     * @return static
     */
    public function url($url, $title, $attributes = array())
    {
        return $this->makeAndAdd(array(
            'type' => 'item',
            'url' => $url,
            'title' => $title,
            'attributes' => $attributes
        ));
    }

    /**
     * Check if the menu exists.
     *
     * @param  string $name
     * @return boolean
     */
    public function has($name)
    {
        return array_key_exists($name, $this->menus);
    }

    /**
     * Get instance of the given menu if exists.
     *
     * @param  string $name
     * @return string|null
     */
    public function get($name)
    {
        return $this->has($name) ? $this->menus[$name] : null;
    }

    /**
     * Get all menus.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->menus;
    }

    /**
     * Get count from all menus.
     *
     * @return int
     */
    public function count()
    {
        return count($this->menus);
    }
}
