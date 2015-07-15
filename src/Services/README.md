# The Backup and Migrate Service

The `\BackupMigrate\Core\Services\BackupMigrate` service is the object that exposes the operation of Backup and Migrate
to the consuming application. By itself it does nothing, it relies on the consuming application to inject all of the 
necessary plugins, configuration and environment services to perform it's work.

## Instantiating the Service

Before it can be called, the Backup and Migrate service must be instantiated, configured and all necessary plugins must
be added by the consuming application. This puts the burden of configuring and discovering plugins on the consuming 
application but keeps the library simple, allows greater flexibility and preserves the goal of dependency inversion.

The service is instantiated with by creating a new `BackupMigrate` object:

    use BackupMigrate\Core\Services\BackupMigrate;
    
    $bam = new BackupMigrate();

### Providing the Environment

If the consuming application needs to use any plugins that must talk to the greater environment (saving state, emailing 
users, creating temporary files) it must provide services to Backup and Migrate that allow it to do so. These services
are contained in an object called the environment. A new environment object should be created and passed to the service
constructor. If you do not pass an environment then a basic one will be created which should work in the simplest 
environments.

Providing an environment.

    use BackupMigrate\Core\Services\BackupMigrate; 
    use MyAPP\Environment\MyEnvironment;
    
    // Create a custom environment with whatever services or configuration are needed for the application
    $env = new MyEnvironment(...);

    // Pass the environment to the service
    $bam = new BackupMigrate($env);

See: [Environment](https://github.com/backupmigrate/backup_migrate_core/tree/master/src/Environment)

### Configuring the Object

A new `BackupMigrate` service object can be configured when it is instantiated. This is done by passing a 
`ConfigInterface` object containing the necessary configuration for each plugin that will be needed:

    use BackupMigrate\Core\Services\BackupMigrate; 
    use BackupMigrate\Core\Config\Config;
    use MyAPP\Environment\MyEnvironment;
    
    // Create a custom environment with whatever services or configuration are needed for the application.
    $env = new MyEnvironment(...);

    // Create a configuration object, pass in an array of configuration.
    $conf = new Config(array(...));

    // Pass the environment to the service
    $bam = new BackupMigrate($env, $conf);


See: [Configuration](https://github.com/backupmigrate/backup_migrate_core/tree/master/src/Config)

### Adding Plugins

Destinations, Sources and Filter plugins are all added the same way, after the service has been instantiated. Each plugin
that is needed must be added to the plugin manager which is available by calling the `plugins()` method. The `add()`
method can then be used to add the plugin. Each added plugin must be given a unique ID when added. This ID will be used
to configure the plugin and to specify which source and destination are used during the operation.

    
    // ...

    // Create a Backup and Migrate Service object
    $bam = new BackupMigrate($env, $conf);

    // Add a source:
    $bam->plugins()->add(new MySQLiSource(), 'db1');
    
    // Add some destinations
    $bam->plugins()->add(new BrowserDownloadDestination(), 'download');
    $bam->plugins()->add(new DirectoryDestination(), 'mydirectory');
    
    // Add some filters
    $bam->plugins()->add(new CompressionFilter(), 'compress');
    $bam->plugins()->add(new FileNamer(), 'namer');


See: [Plugins](https://github.com/backupmigrate/backup_migrate_core/tree/master/src/Plugin)

## Operations
The Backup and Migrate service provides two main operations:

* `backup($source_id, $destination_id)`
* `restore($source_id, $destination_id, $file_id)`

### The Backup Operation

The `backup()` operation creates a backup file from the specified source, post-processes the file with all installed 
filters and saves the file to the specified destination. The parameters for this operation are:

* **$source_id** ***(string)*** - The id of the source as specified when it is added to the plugin manager.
* **$destination_id** ***(string)*** - The id of the destination as specified when it is added to the plugin manager.

There is no return value but it may throw an exception if there is an error.

    // ...

    // Create a Backup and Migrate Service object
    $bam = new BackupMigrate($env, $conf);

    // Add plugins ...
    
    // Run the backup.
    $bam->backup('db1', 'mydirectory');


### The Restore Operation

The `restore()` operation loads the specified file from the specified destination, pre-processes the file with all 
installed filters and restores the data to the specified source. The parameters are:

* **$source_id** ***(string)*** - The id of the source as specified when it is added to the plugin manager.
* **$destination_id** ***(string)*** - The id of the destination as specified when it is added to the plugin manager.
* **$file_id** ***(string)*** - The id of the file within the destination. This is usually the file name but can be any 
unique string specified by the destination.


    // ...

    // Create a Backup and Migrate Service object
    $bam = new BackupMigrate($env, $conf);

    // Add plugins ...
    
    // Run the restore.
    $bam->restore('db1', 'mydirectory', 'backup.mysql.gz');
