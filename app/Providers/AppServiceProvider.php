<?php

namespace App\Providers;

use App\Models\admin\ContactModel;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }

        Paginator::useBootstrap();

        View::composer('admin.blocks.sidebar', function ($view) {
            $contactModel = new ContactModel();
            $unreadData = $contactModel->countContactsUnread();

            $view->with('unreadCount', $unreadData['countUnread']);
            $view->with('unreadContacts', $unreadData['contacts']);
        });
    }
}