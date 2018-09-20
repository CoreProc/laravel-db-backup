Laravel DB Backup
=================

[![Join the chat at https://gitter.im/CoreProc/laravel-db-backup](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/CoreProc/laravel-db-backup?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Artisan command to backup your database. Built for Laravel 4.2. Originally forked from [schickling/laravel-backup](https://github.com/schickling/laravel-backup) but modified to push in more features like data retention from Amazon S3 and Slack notifications.

## Quick start

### Required setup

In the `require` key of `composer.json` file add the following

    "coreproc/laravel-db-backup": "0.*"

Run the Composer update comand

    $ composer update

In your `app/config/app.php` add `'Coreproc\LaravelDbBackup\LaravelDbBackupServiceProvider'` to the end of the `$providers` array

    'providers' => array(

        'Illuminate\Foundation\Providers\ArtisanServiceProvider',
        'Illuminate\Auth\AuthServiceProvider',
        ...
        'EllipseSynergie\ApiResponse\Laravel\ResponseServiceProvider',
        'Coreproc\LaravelDbBackup\LaravelDbBackupServiceProvider',

    ),

## Usage

### Basic Usage

You can quickly backup your database using the command line below

`php artisan db:backup`

This command will backup the database that your Laravel application is connected to. This means that the `default` configuration from `app/config/database.php` will be used.

By default, the file will be saved in the `app/storage/dumps` path and will be named like this: `{database_name}_{unix_timestamp}.sql`.

If you want to use another database, just add another configuration to the `connection` value. A sample configuration in `app/config/database.php` would look like this:

    'connections' => array(

        'dbconnection1'  => array(
            'driver'         => 'mysql',
            'host'           => 'localhost',
            'database'       => 'db1',
            'username'       => 'user',
            'password'       => 'password',
            'charset'        => 'utf8',
            'collation'      => 'utf8_unicode_ci',
            'prefix'         => '',
            'slackWebhookPath'  => 'T00000000/B00000000/XXXXXXXXXXXXXXXXXXXXXXXX'
        ),
        
        'dbconnection2'  => array(
            'driver'            => 'mysql',
            'host'              => 'localhost',
            'database'          => 'db1',
            'username'          => 'user',
            'password'          => 'password',
            'charset'           => 'utf8',
            'collation'         => 'utf8_unicode_ci',
            'prefix'            => '',
            'slackWebhookPath'  => 'T00000000/B00000000/XXXXXXXXXXXXXXXXXXXXXXXX'
        ),

    ),

So if you want to back up, let's say, dbconnection2, you would add the `--database` option to our artisan command.

`php artisan db:backup --database=dbconnection2`

### Slack Integration

[Slack](https://slack.com) is a platform/service for team communication. Using the [Incoming Webhooks](https://api.slack.com/incoming-webhooks) integration you can send real-time messages to any channel on your team's Slack.

If you'll notice, we have added an extra variable to the database configuration (`slackWebhookPath`). To begin receiving your database backup notifications you first have to add the [Incoming Webhooks integration](https://my.slack.com/services/new/incoming-webhook/) for your Slack team. Once set up you will be given a unique `Webhook URL` which will look something like this: *`https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXXXXXXXXXXXXXX`*. Just copy your unique webhook path (`T00000000/B00000000/XXXXXXXXXXXXXXXXXXXXXXXX`) and add it to your database configuration.

To disable the Slack integrations, either leave the value blank or you can disable Slack notifications manually by using the `--disable-slack` option. Here is an example:

`php artisan db:backup --database=dbconnection2 --disable-slack`

### Upload to Amazon S3

To upload to you Amazon S3 bucket, you will need to put in your AWS settings first. By default, this package is shipped with the [aws/aws-sdk-php-laravel](https://github.com/aws/aws-sdk-php-laravel) package so we'll modify the settings from there.

To bring out the config file of the AWS library, you'll need to generate it by using the command below:

`php artisan config:publish aws/aws-sdk-php-laravel`

You will find the AWS config file in the following path: `app/config/packages/aws/aws-sdk-php-laravel/config.php`. Fill in the appropriate values.

To upload the database backup to S3, use the `--upload-s3` option:

`php artisan db:backup --database=dbconnection2 --upload-s3=s3_bucket_name` 

Change the value of the `--upload-s3` to the name of the bucket you want to upload to.

If you want to keep only S3 copy of your backups, set `--s3-only`, which will delete local copy of backup when S3 upload is enabled.

`php artisan db:backup --database=dbconnection2 --upload-s3=s3_bucket_name --s3-only=true`

### Database Retention (S3 only for now)

If you want to only keep a certain number of copies of your database, you can set the `--data-retention-s3` option to the number of days you want to retain your data. Here is an example:

`php artisan db:backup --database=dbconnection2 --data-retention=30` 

### Change filename

You can change the name of the backup file with the `--filename` option. All filenames will be appended with the unix timestamp.

`php artisan db:backup --database=dbconnection2 --filename=test` 

### Save as ZIP archive

To create zip archive instead of raw .sql file, set `--archive` value.

`php artisan db:backup --database=dbconnection2 --data-retention=30 --archive=true` 
