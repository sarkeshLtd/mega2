<?php
/*
 * this traint is use for sigleton design patterns
 * you can use this traint in your design with use from this traint
 * for create an object from your class use this pattern  $object = your_class::singleton();
 */
namespace Mega\Cls\Patterns;

trait Singleton{
	/*
	 * @var object static, from owner object
	 */
    protected static $instance;
    
    /*
     * create singleton object from owner
     * @return object from owner
     */
    final public static function singleton()
    {
        return isset(static::$instance)
            ? static::$instance
            : static::$instance = new static;
    }
    
    /*
     * construct
     */
    final private function __construct() {
        $this->init();
    }
    
    protected function init() {}
    final private function __wakeup() {}
    final private function __clone() {}    
}
?>
