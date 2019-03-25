<?php

namespace App\Providers;

use Storage;
use League\Flysystem\Filesystem;
use Dropbox\Client as DropboxClient;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Client;
use League\Flysystem\Dropbox\DropboxAdapter;

class DropboxFilesystemServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Storage::extend('dropbox', function ($app, $config) {
            $client = new DropboxClient($config['accessToken'], $config['appSecret']);

            return new Filesystem(new DropboxAdapter($client));
        });
    }

    public function register()
    {
        //
    }
}