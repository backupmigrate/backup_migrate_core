# Sources

A source in Backup and Migrate is a thing that can be backed up. This could be a database or a file directory. An object that implements the `\BackupMigrate\Core\Source\SourceInterface` is responsible for creating a single backup file that represents the specified source. It is also repsonsible for restoring the to that source from a backup file.

Sources in Backup and Migrate are implemented as plugins and will have dependencies and configuration injected into them by the Plugin Manager.

A single Backup and Migrate instance can have more than 1 source of a given type. Each source will have a unquique key that will be used to pass the congfiguration to the source object as well as to identify the source when running a `backup()` or `restore()` operation.

Like other plugins, sources are passed to the Backup and Migrate object by the consuming application by calling the `add()` method on the plugin manager.

	$backup_migrate->plugins()->add(new MySourcePlugin(), 'source1');