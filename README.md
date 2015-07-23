# Backup and Migrate Core

The core functionality for Backup and Migrate.

Backup and Migrate Core is a PHP-based library which manages the backing up and restoring of resources such as databases and file directories. It is primarily intended for backing up content managed web sites and was originally written as [a Drupal module](https://www.drupal.org/project/backup_migrate).

This library represents a ground up refactoring and abstraction which allows the core functionality to be used in plugins for other content management systems or for uses beyond CMS-managed websites.

## Usage

The following is a simplified version of how to call the library to perform a backup:

	<?php
	// Create the configuration object from a hardcoded PHP array.
	$config = new Config(
		array(
			// Add configuration for the 'db' source.
			'database1' => array(
				'host' => '127.0.0.1',
				'database' => 'mydb',
				'user' => 'myuser',
				'password' => 'mypass',
				'port' => '8889',
	      	),
	      	// Configure the destination.
	      	'mybackups' => array(
	      		'directory' => '~/mybackups',
	      	),
	      	// Configure the compression filter.
	      	'compressor' => array(
	      		'compression' => 'gzip',
	      	),
	      	// Configure the file namer.
	      	'name' => array(
		      'filename' => 'backup',
		      'timestamp' => true,
		    ),
	  	)
	);
	
	// Create a new Backup and Migrate object with this configuration.
	$bam = new BackupMigrate(NULL, $config);
	
	// Add the database source. This will read the configuration with the same key 	
	$bam->plugins()->add(new MySQLiSource(), 'database1');
	// Add the destination.
	$bam->plugins()->add(new DirectoryDestination(), 'mybackups');

	// Add the filters.
	$bam->plugins()->add(new CompressionFilter(), 'compression');
	$bam->plugins()->add(new FileNamer(), 'name');

	// Backup from the 'database1' db to the 'mybackups' directory.
	$bam->backup('databse1', 'mybackups');
	
## Reference Implementation
[Backup and Migrate CLI](https://github.com/backupmigrate/backup_migrate_cli) is a simple command-line tool which consumes the Backup and Migrate Core library. It serves as a simple reference implementation.	

## Concepts

### Dependency Inversion
As much as possible, Backup and Migrate tries to embrace the [Dependency Inversion Principal](https://en.wikipedia.org/wiki/Dependency_inversion_principle). This means that Backup and Migrate Core relies on the consuming application to pass to it all of the pieces it needs to run. This allows the library to run in a wide variety of environments without requiring a lot of hacky internal business logic. This philosophy is balanced against the desire for a pleasant developer experience so that consuming the library does not an excess of tedious boilerplate glue code.

### The BackupMigrate Service Object
This `\BackupMigrate\Core\Services\BackupMigrate` object is the main task-runner of the library. It is the primary object that a consuming application interacts with. It contains two primary operation methods: `backup()` and `restore()` which do exactly what you expect them to. The consuming application is responsible for injecting to this object the following:

* All plugins (sources, destinations, filters) required to run.
* (Optional) The environment dependency injection container.
* (Optional) All necessary configuration.

See: [Backup and Migrate Service](https://github.com/backupmigrate/backup_migrate_core/tree/master/src/Services)

### Plugins
Plugins are the meat of the library. All of the actual work is done by plugins. Plugins come in three types:

* **Sources** - Items which can be backed up and restored. (e.g: A MySQL database)
* **Destinations** - Places where backup files can be stored. (e.g: A directory on your server)
* **Filters** - Actions that can be performed on backup files after backup or before restore. (e.g: Gzip compression)

While these three types of plugin are conceptually separate they are technically identical.

See: [Plugins](https://github.com/backupmigrate/backup_migrate_core/tree/master/src/Plugin)

##### Sources
Each backup and restore operation works on a single source. For simplicity more than one source may be added to the BackupMigrate object. The source to be backed up is identified by id when `backup()` or `restore()` is called.

See: [Sources](https://github.com/backupmigrate/backup_migrate_core/tree/master/src/Source)

##### Destinations
Destinations act the same way as sources. These are the places where the backup files are sent (during `backup()`) or from which they are loaded (during `restore()`).

See: [Destinations](https://github.com/backupmigrate/backup_migrate_core/tree/master/src/Destination)

##### Filters
Filters can alter backup files before `restore()` or after `backup()`. Unlike sources and destinations there can be many filters run per operation.

#### Plugin Manager
The plugin manager maintains a list if injected plugins and configures them as needed. Consuming software interacts with the plugin manager by calling `plugins()` on the BackupMigrate object. This is the method used to inject plugins into the controller:

	// Create a new BackupMigrate controller.
	$backup_migrate = new BackupMigrate();
	
	// Add a new custom plugin with the id 'mycustomplugin'
	$backup_migrate->plugins()->add(new CustomPlugin(), 'mycustomplugin');

### Configuration
Backup and Migrate Core has very little configuration management built in. It is the responsibility to inject the necessary configuration into the library as a `ConfigInterface` object. If no configuration object is provided then each plugin will use it's configuration defaults.

See: [Configuration](https://github.com/backupmigrate/backup_migrate_core/tree/master/src/Config)

### Environment
The environment is an object (a simple dependency injection container) which contains services that allows Backup and Migrate plugins to access the broader environment. Access to caching, state, logging, email sending and temporary file creation is provided by the consuming application using the environment object. Creating and injecting this object is optional. If the consuming application does not provide an environment object then a default environment will be created with non-functioning versions of the optional services.

See: [Environment](https://github.com/backupmigrate/backup_migrate_core/tree/master/src/Environment) 
