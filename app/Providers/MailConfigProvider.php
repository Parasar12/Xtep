<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class MailConfigProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
//        $mail_config = DB::table('email_config_settings')->first();
//
//        Config::set('mail.driver', 'smtp');
//        Config::set('mail.host', $mail_config->mail_host);
//        Config::set('mail.port', $mail_config->port);
//        Config::set('mail.encryption', $mail_config->encryption);
//        Config::set('mail.username', $mail_config->username);
//        Config::set('mail.password', $mail_config->password);
//        Config::set('mail.from.address', $mail_config->email_from);
//        Config::set('mail.from.name', getSiteTitle());
    }
}
