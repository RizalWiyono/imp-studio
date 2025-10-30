<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run()
    {
        Menu::create([
            'type' => 'PARENT',
            'title' => 'Dashboard',
            'icon' => 'bx bx-home',
            'route' => 'dashboard'
        ]);

        Menu::create([
            'type' => 'HEADER',
            'title' => 'Data Master'
        ]);

        Menu::create([
            'type' => 'PARENT',
            'header' => 'Data Master',
            'title' => 'Articles',
            'icon' => 'bx bx-building',
            'route' => 'articles.index'
        ]);

        Menu::create([
            'type' => 'HEADER',
            'title' => 'User Management'
        ]);

        Menu::create([
            'type' => 'PARENT',
            'header' => 'User',
            'title' => 'Users',
            'icon' => 'bx bx-user',
            'route' => 'users.index'
        ]);

        Menu::create([
            'type' => 'PARENT',
            'header' => 'User Management',
            'title' => 'Roles',
            'icon' => 'bx bx-lock',
            'route' => 'roles.index'
        ]);

        Menu::create([
            'type' => 'PARENT',
            'header' => 'User Management',
            'title' => 'Menus',
            'icon' => 'bx bx-menu',
            'route' => 'menus.index'
        ]);

        Menu::create([
            'type' => 'PARENT',
            'header' => 'User Management',
            'title' => 'Access Control',
            'icon' => 'bx bx-check-shield',
            'route' => 'access-control.index'
        ]);


        Menu::create([
            'type' => 'HEADER',
            'title' => 'System'
        ]);

        Menu::create([
            'type' => 'PARENT',
            'header' => 'System',
            'title' => 'System Settings',
            'icon' => 'bx bx-key',
            'route' => 'settings.index'
        ]);

        Menu::create([
            'type' => 'PARENT',
            'header' => 'System',
            'title' => 'Activity Log',
            'icon' => 'bx bx-list-ul',
            'route' => 'activity-log.index'
        ]);

    }
}
