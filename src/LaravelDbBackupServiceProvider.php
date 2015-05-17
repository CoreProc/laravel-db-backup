<?php namespace Coreproc\LaravelDbBackup;

use Illuminate\Support\ServiceProvider;

class LaravelDbBackupServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('coreproc/laravel-db-backup',null, __DIR__);

        $this->app->register('Aws\Laravel\AwsServiceProvider');

        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('AWS', 'Aws\Laravel\AwsFacade');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $databaseBuilder = new DatabaseBuilder();

        $this->app['db.backup'] = $this->app->share(function ($app) use ($databaseBuilder) {
            return new Commands\BackupCommand($databaseBuilder);
        });

        $this->app['db.restore'] = $this->app->share(function ($app) use ($databaseBuilder) {
            return new Commands\RestoreCommand($databaseBuilder);
        });

        $this->commands(
            'db.backup',
            'db.restore'
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}
