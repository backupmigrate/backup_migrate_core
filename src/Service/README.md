# Services #

If a plugin needs to access the greater environment to write logs, store data, etc. it should rely on service objects which may be injected into the plugin at run time.

## Service Locator ##

The `ServiceLocatorInterface` defines a very simple service container which stores a keyed list of services available to plugins which need them. The built in `ServiceLocator` class implements this interface in the most basic way possible. An consuming application may choose to implement a locator using a more sophisticated dependency management and configuration solution such as [Pimple](http://pimple.sensiolabs.org/), [PHP-DI](http://php-di.org/) or [Symfony's DependencyInjection Component](http://symfony.com/doc/current/components/dependency_injection/introduction.html). The built in locator simply takes a list of already configured services and returns them when requested.

## Service Injection ##

It is not necessary to use automatic service injection. A consuming application can simply instantiate plugins and pass the necessary services directly to them. However, a simple service injection mechanism is provided by the plugin manager which can make dynamically creating plugins much simpler. To use automatic service injection, a service locator containing all of the necessary services must be created and passed to the plugin manager.

Plugins can request that a service be injected by defining a setter with called `setServiceName` where 'ServiceName' is replaced with the name of the given service. Here is an pseudo-code example:

	class MyPlugin implements PluginInterface {
		
		// Logger service setter
		public function setLogger(LoggerInterface $logger) {
			$this->logger = $logger;
		}
		
		// ...
	}
	
This plugin will have a logger injected if one is available:

	$services = new ServiceLocator();
	// The key 'Logger' must match 'setLogger'
	$services->add('Logger', new MyLogger());
	
	$plugins = new PluginManager($services);
	// The manager will inject the logger automatically.
	$plugins->add('myplugin', new MyPlugin());
	
	