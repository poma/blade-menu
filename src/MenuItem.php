<?php

namespace Poma\BladeMenu;

use Closure;
use Illuminate\Contracts\Support\Arrayable as ArrayableContract;
use Illuminate\Support\Facades\Request;
use Route;

class MenuItem implements ArrayableContract
{
	/**
	 * Pointer to root menu item
	 *
	 * @var MenuItem root
	 */
	public $root;

	/**
	 * Pointer to parent item
	 *
	 * @var MenuItem prent
	 */
	public $parent;

	/**
	 * Current menu depth
	 *
	 * @var int level
	 */
	public $level;

    /**
     * Array properties.
     *
     * @var array
     */
    public $properties;

    /**
     * The child collections for current menu item.
     *
     * @var MenuItem[]
     */
    public $children = array();

    /**
     * Constructor.
     *
     * @param array $properties
     */
    public function __construct($properties = array())
    {
        $this->properties = $properties;
        $this->fill($properties);
    }

    /**
     * Create new static instance.
     *
     * @param array $properties
     * @return static
     */
    public static function make(array $properties)
    {
        return new static($properties);
    }

    /**
     * Fill the attributes.
     *
     * @param  array $attributes
     * @return void
     */
    public function fill($attributes)
    {
        foreach ($attributes as $key => $value) {
	        $this->{$key} = $value;
        }
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl()
    {
	    if (!empty($this->route)) {
		    return route($this->route[0]);
	    } elseif (!empty($this->url)) {
		    return url($this->url);
	    } else {
		    return '#';
	    }
    }

    /**
     * Get request url.
     *
     * @return string
     */
    public function getRequest()
    {
        return ltrim(str_replace(url(), '', $this->getUrl()), '/');
    }

    /**
     * Get icon.
     *
     * @param  null|string $default
     * @return string
     */
    public function getIcon($default = null)
    {
	    $icon = array_get($this->attributes, 'icon');
        return ! is_null($icon) ? '<i class="' . $icon . '"></i>' : $default;
    }

    /**
     * Get properties.
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Same with hasSubMenu.
     *
     * @return bool
     */
    public function hasChildren()
    {
        return ! empty($this->children);
    }

    /**
     * Check the active state for current menu.
     *
     * @return mixed
     */
    public function hasActiveChild()
    {
	    foreach ($this->children as $child) {
		    if ($child->isActive()) {
			    return true;
		    }
	    }

	    return false;
    }

    /**
     * Get disabled state.
     *
     * @return boolean
     */
    public function isDisabled()
    {
        $disabled = array_get($this->attributes, 'disabled');

        if (is_bool($disabled)) {
            return $disabled;
        }

        if ($disabled instanceof Closure) {
            return call_user_func($disabled);
        }

        return false;
    }

	/**
	 * Get active state for current item.
	 *
	 * @param bool $includingChildren
	 * @return mixed
	 */
    public function isActive($includingChildren = false)
    {
        if ($this->isDisabled()) {
            return false;
        }

	    if ($includingChildren && $this->hasActiveChild()) {
		    return true;
	    }

        $active = array_get($this->attributes, 'active');

        if (is_bool($active)) {
            return $active;
        }

        if ($active instanceof Closure) {
            return call_user_func($active);
        }

        if ($this->hasRoute()) {
            return $this->getActiveStateFromRoute();
        } else {
            return $this->getActiveStateFromUrl();
        }
    }

	public function filter(Closure $predicate)
	{
		$this->children = array_filter($this->children, $predicate);

		foreach ($this->children as $item) {
			$item->filter($predicate);
		}
	}

	public function getAction()
	{
		$routes = Route::getRoutes();
		if (empty($this->route) || !$routes->hasNamedRoute($this->route[0])) {
			return null;
		} else {
			return $routes->getByName($this->route[0])->getAction();
		}
	}

    /**
     * Determine the current item using route.
     *
     * @return bool
     */
    protected function hasRoute()
    {
        return ! empty($this->route);
    }

    /**
     * Get active status using route.
     *
     * @return bool
     */
    protected function getActiveStateFromRoute()
    {
	    return Route::currentRouteName() == $this->route[0];
    }

    /**
     * Get active status using request url.
     *
     * @return bool
     */
    protected function getActiveStateFromUrl()
    {
        return Request::is($this->url);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getProperties();
    }

    /**
     * Get property.
     *
     * @param  string $key
     * @return string|null
     */
    public function __get($key)
    {
        return isset($this->$key) ? $this->$key : null;
    }
}
