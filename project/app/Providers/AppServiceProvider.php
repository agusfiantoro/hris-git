<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider {

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events) {
        {
            if (env('APP_ENV') !== 'local') {
                $this->app['request']->server->set('HTTPS', true);
            }
        }

        Validator::replacer('distinct', function ($message, $attribute, $rule, $parameters) {
            if (str_contains($message, ':arrpos') && preg_match("/\.(\d+)\./", $attribute, $match)) {
                return str_replace(":arrpos", $match[1]+ 1, $message);
            }
    
            return $message;
        });

        // $proxy_schema = getenv('PROXY_SCHEMA');
        // if (!empty($proxy_schema)) {
        //     \URL::forceScheme($proxy_schema);
        // }
        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            $result = [];
            
            $event->menu->add([
                'text' => 'Change Module',
                'icon' => '--icon-change-responsibility fas fa-random',
                'url' => '#change-responsibility',
                'classes' => '--menu-btn-change-responsibility bg-white rounded mx-1 px-3 py-2',
            ]);
            

            $event->menu->add('MAIN NAVIGATION');
            $list_menu_branch = Session::get('app_menu');
            $collect_menu_branch = collect($list_menu_branch)->groupBy('parent')->toArray();
            if (isset($collect_menu_branch[0])) {
                foreach ($collect_menu_branch[0] as $key_0 => $value_0) {

                    $child_menu_branch = [];
                    if (isset($collect_menu_branch[$value_0->id_responsibility])) {
                        $collect_menu_branch_1 = collect($collect_menu_branch[$value_0->id_responsibility])->toArray();

                        foreach ($collect_menu_branch_1 as $key_1 => $value_1) {
                            $child_menu_branch_1 = [];

                            if (isset($collect_menu_branch[$value_1->id_responsibility])) {
                                $collect_menu_branch_2 = collect($collect_menu_branch[$value_1->id_responsibility])->toArray();
                                foreach ($collect_menu_branch_2 as $key_2 => $value_2) {
                                    $child_menu_branch_1[] = [
                                        'text' => $value_2->responsibility_name,
                                        'url' => $value_2->address_menu != "" || $value_2->address_menu != null ? $value_2->address_menu : "#",
                                    ];
                                }
                            }

                            $child_menu_branch[] = [
                                'text' => $value_1->responsibility_name,
                                'url' => $value_1->address_menu != "" || $value_1->address_menu != null ? $value_1->address_menu : "#",
                                'submenu' => count($child_menu_branch_1) > 0 ? $child_menu_branch_1 : null
                            ];
                        }
                    }
                    $event->menu->add([
                        'text' => $value_0->responsibility_name,
                        'url' => $value_0->address_menu != "" || $value_0->address_menu != null ? $value_0->address_menu : "#",
                        'icon' => 'nav-icon ' . $value_0->icon,
                        'submenu' => count($child_menu_branch) > 0 ? $child_menu_branch : null
                    ]);
                }
            }
        });

        Validator::extend('base64', function ($attribute, $value, $parameters, $validator) {
            if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $value)) {
                return true;
            } else {
                return false;
            }
        });

        Validator::extend('base64image', function ($attribute, $value, $parameters, $validator) {
            $explode = explode(',', $value);
            $allow = ['png', 'jpg', 'jpeg'];
            $format = str_replace(
                [
                    'data:image/',
                    ';',
                    'base64',
                ],
                [
                    '', '', '',
                ],
                $explode[0]
            );

            // check file format
            if (!in_array($format, $allow)) {
                return false;
            }

            // check base64 format
            if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
                return false;
            }

            return true;
        });
    }

}
