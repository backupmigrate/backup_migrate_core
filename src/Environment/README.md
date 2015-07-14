# The Environment Object

The environment object is essentially a Dependency Injection Container albeit an extremely simple one. This object should
contain a number of services that will allow Backup and Migrate to interact with the greater environment in which it is
run. That means (for example) to run Backup and Migrate Core inside the Backup and Migrate Drupal module, the module
must provide an implementation of the `EnvironmentInterface` which provides services to allow the core library to use
the Drupal caching system, mailer, logger etc. 

Currently a conforming environment object must provide the following services:

* A temporary file adapter.
* A cache.
* A state storage mechanism.
* A logger.
* A mail sending interface.

Plugins should then be able to call upon these services to cache data, save state, send an email etc. **All services must be provided**, however, it is perfectly acceptible to use null (ie: non-functioning) version of the following services:

* Cache
* State
* Logger
* Mailer

Any plugin that relies on these services should operate under the assumption that these services may be non-functioning. For example, a command line tool consuming the Backup and Migrate Core library might not provide a permenante state store or caching. A null version of each of these services is provided in Backup and Migrate Core and is used by default.

The temporary file manager must function since creating temporary files is necessary for performing backups and restores using Backup and Migrate core.

## The Services

### Temporary File Adapter
A temporary file adapter is a service which creates and manages temporary files. Any time Backup and Migrate needs a temporaray file to write to it will request one from the active adapter. Adapters must implement the `BackupMigrate\Core\File\TempFileAdapterInterface`. An adapter must be able to create a writable temp file with a given file extension, delete a temp file (specified by filename) and delete all files created since the adapter was instantiated.

On many systems the adapter will simpy create files within the operating system's /tmp directory. A simple adaptor is provided for this use case called `BackupMigrate\Core\File\TempFileAdapter`. This adapter takes a directory in it's constructor to which it will save all temporary files.

### Cache
A cache service should allow plugins to store data to a permament or semi-permanent data store for the purpose of caching. It should implement `BackupMigrate\Core\Environment\CacheInterface`.

If the consuming software does not have storage available it may provide the `BackupMigrate\Core\Environment\NullCache` service which discards caching information and always returns empty data.

### State Storage
A state service should allow plugins to store state information to persistent storage. This is a simple key-value store plugins will be responsible for ensuring that keys are unique. State includes data generated during the run of a backup or restore operation (such as last run time). It should not be used to store configuration. It is the responsibility of the consuming application to pass configuration into Backup and Migrate at runtime using a `\BackupMigrate\Core\Config\ConfigInterface` object.

State storage is also optional and a `BackupMigrate\Core\Environment\NullState` may be used if persistence is not available.

### Logging
The logging service should be a `\Psr\Log\LoggerInterface` logger. Backup and Migrate will log status and error conditions to this service but will not consume the logs themselves. This may be used by the consuming application to provide feedback to the user or to store the logs if desired. 

A `Psr\Log\NullLogger` object will be used to discard all logs if they are not requred by the consuming application.

### Mailer
A mailer service should be able to send an email. Plugins may use this to send status updates to the user if configured to do so. 

If no application specific mailer is provided a default mailer called `BackupMigrate\Core\Environment\Mailer` may be used which simply calls the PHP `mail` function to send mail.


