# Configuration

Backup and Migrate core is configured by the consuming software when the library is instantiated using a `\BackupMigrate\Core\Config\ConfigInterface` object. This object is a simple key-value store which should contain the configuration for each of the available plugins (sources, destinations and filters). Each plugin should have it's own entry in the config object which contains an array of all of the configuration for that item. The key for this entry must be the same as the key assigned to the plugin when it is added to the `BackupMigrate` object using `->plugins()->add()`.

Any object that implements the `\BackupMigrate\Core\Config\ConfigInterface` may be used to configure Backup and Migrate. For example, a consuming application may want to implement a class that directly accesses the application's persistence layer to retrieve configuration values. In many cases, however the simple default `\BackupMigrate\Core\Config\Config` will suffice.

## The Config Class
The built in `\BackupMigrate\Core\Config\Config` is a simple implementation of the configuration interface which can be instantiated using a PHP associative array:

	<?php
	
	use \BackupMigrate\Core\Config\Config;
	
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
	      	// Configure the compression filter.
	      	'compressor' => array(
	      		'compression' => 'gzip',
	      	),
	      	// Add more filter, source and destination configuration.
	  	)
	);
	
	// Create a new Backup and Migrate object with this configuration.
	$bam = new BackupMigrate(NULL, $config);
	
	// Add the database source. This will read the configuration with the same key ('database1')
	$bam->plugins()->add(
		new \BackupMigrate\Core\Source\MySQLiSource(),
		'database1'
	);
	// Add the compression plugin.
	$bam->plugins->add(
		new \BackupMigrate\Core\Filter\CompressionFilter(),
		'compressor'
	);
	// Add more filters and a destination.
	...
	
	$bam->backup('database1', 'somedestination');
	

