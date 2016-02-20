# Laravel Simple User

Package quản lý User cho Laravel Application
###**Đơn giản:** _không quản lý user group_

## Install

* Cài đặt package **vinkla/hashids**, cấu hình connection **user** như sau:
```php
'user' => [
	'salt'     => config('app.key') . 'user',
	'length'   => 3,
	'alphabet' => '1234567890abcdefghijklmnopqrstuvwxyz',
],
```

* **Thêm vào file composer.json của app**
```json
	"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/minhbang/laravel-simple-user"
        }
    ],
    "require": {
        "minhbang/laravel-simple-user": "dev-master"
    }
```
``` bash
$ composer update
```

* **Thêm vào file config/app.php => 'providers'**
```php
	Minhbang\User\UserServiceProvider::class,
```

* **Publish config và database migrations**
```bash
$ php artisan vendor:publish
$ php artisan migrate
```

* **Sữa file config/auth.php**
```php
//Thay
'model' => App\User::class,
//Bằng
'model' => Minhbang\User\User::class,
```

* **Thêm vào file app/Http/Kernel.php => $routeMiddleware** (đứng đầu)
```php
protected $routeMiddleware = [
	'role' => \Minhbang\User\Middleware\Role::class,
	//...
];
```

* **Database Seeder**
```php
<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Minhbang\User\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        // admin
        User::create(
            [
                'name'     => 'Administrator',
                'username' => 'admin',
                'email'    => 'admin@domain.com',
                'password' => 'admin',
            ]
        );
        Model::reguard();
    }
}
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
