<?php

use App\Console\Commands\SeedShowcaseCommand;
use App\Console\Commands\ShowcaseSyncCommand;
use Illuminate\Support\Facades\Artisan;

Artisan::command('showcase:seed', function () {
    $cmd = new SeedShowcaseCommand();
    $cmd->setLaravel($this->laravel);
    $cmd->setOutput($this->output);
    $cmd->handle();
})->purpose('Seed showcase demo data into SQLite / MySQL');

Artisan::command('visitlogs:sync', function () {
    $cmd = new ShowcaseSyncCommand();
    $cmd->setLaravel($this->laravel);
    $cmd->setOutput($this->output);
    $cmd->handle();
})->purpose('Synchronize pending visit logs from staging table to production');
