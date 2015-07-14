# Destinations

A destination in Backup and Migrate is the place where backup files are sent when they are created or where they are read from during a restore. The simplest example of a destination would be a directory on your web server.

An object implementing the `\BackupMigrate\Core\Destination\DestinationInterface` can be used as a destination and is responsible for persisting a file using the given id (generally the filename). It is also responsible for returning the same file given the same file id.

Destinations in Backup and Migrate are implemented as plugins and will have dependencies and configuration injected into them by the Plugin Manager.

A single Backup and Migrate instance can have more than 1 destination of a given type. Each destination will have a unique key that will be used to pass the congfiguration to the destination object as well as to identify the destination when running a `backup()` or `restore()` operation. Many destinations can be injected into the BackupMigrate object but only one will be used for a given `backup()` or `restore()` operation.

Like other plugins, destinations are passed to the Backup and Migrate object by the consuming application by calling the `add()` method on the plugin manager.

	$backup_migrate->plugins()->add(new MyDestinationPlugin(), 'destination1');