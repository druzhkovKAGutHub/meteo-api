<?php

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permission = [
            //users
            [
                'name' => 'users-list',
                'parent' => '0',
                'display_name' => 'Просмотр пользователей',
                'description' => ''
            ],
            [
                'name' => 'users-create',
                'parent' => '1',
                'display_name' => 'Создание новых пользователей',
                'description' => ''
            ],
            [
                'name' => 'users-edit',
                'parent' => '1',
                'display_name' => 'Изменение данных пользователей',
                'description' => ''
            ],
            [
                'name' => 'users-delete',
                'parent' => '1',
                'display_name' => 'Удаление пользователей',
                'description' => ''
            ],
            //devices
            [
                'name' => 'devices-list',
                'parent' => '4',
                'display_name' => 'Просмотр устройств',
                'description' => ''
            ],
            [
                'name' => 'devices-create',
                'parent' => '4',
                'display_name' => 'Создание новых устройств',
                'description' => ''
            ],
            [
                'name' => 'devices-edit',
                'parent' => '4',
                'display_name' => 'Изменение данных устройств',
                'description' => ''
            ],
            [
                'name' => 'devices-delete',
                'parent' => '4',
                'display_name' => 'Удаление устройств',
                'description' => ''
            ],
        ];
        foreach ($permission as $key => $value) {
            Permission::create($value);
        }
    }
}