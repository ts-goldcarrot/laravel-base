<?php


namespace GoldcarrotLaravel;

use GoldcarrotLaravel\Console\DomainMakeCommand;
use GoldcarrotLaravel\Console\EnumsMakeCommand;
use GoldcarrotLaravel\Console\PresenterMakeCommand;
use GoldcarrotLaravel\Console\RepositoryMakeCommand;
use GoldcarrotLaravel\Console\ServiceMakeCommand;
use GoldcarrotLaravel\Console\ValidatorMakeCommand;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->commands([
            DomainMakeCommand::class,
            EnumsMakeCommand::class,
            PresenterMakeCommand::class,
            RepositoryMakeCommand::class,
            ServiceMakeCommand::class,
            ValidatorMakeCommand::class,
        ]);
    }
}