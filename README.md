## LaCrud

**The documentation is avalaible [in Spanish](https://github.com/leonardoDavid/LaCrud/blob/master/README-es.md) too.**

LaCrud is a tool that help you to create a Laravel's CRUD faster and without break desging patterns on your application, because it's a component that integrate and try mold in your development.

LaCurd is built for this work with tha new **Laravel 5** :)

In this moment, LaCrud is in a development version, but now, you can use something of his beneficies:

 * Indicate the name's table and LaCrud create all CRUD for you.
 * All proccess of CRUD working on few minutes.
 * Detection of native relations on your Database.
 * Capacity for create a fake relations by code.
 * Add "n to n" relation on your entity.
 * Capacity of denied operations or actions of CRUD.
 * Filter of content in the list view.
 * Show only some fields in diferents operations.
 * The texts of bottons and notifications use a package of languages.
 * Support for you can create or add your own theme.
 * Manager for upload files and images. You can crop the image too.

Maybe LaCrud is operaty in his totality, too has objetives for his first version:

 * Function for print and export data.
 * Callbacks for differents operations.
 * Masive delete for when exist registers with foreigns relations in your database.
 * Delele files when change the value on field type 'upload'.
 * Best manipulation with Images thanks to [Intervention Image](http://image.intervention.io/)

## Instalation

You can install LaCrud via Composer.

```
composer require devswert/lacrud dev-master
```

Or added on your composer.json and exec composer update

```
"devswert/lacrud": "dev-master"
```

Once time finished to instalation, you should add the ServiceProvider and Facade for LaCrud in your configuration (config/app.php)

 - ServiceProvider
```
'DevSwert\LaCrud\LaCrudServiceProvider'
```

- Facade
```
'LaCrud'	=> 'DevSwert\LaCrud\LaCrudFacade'
```

The last step is publish the assets and the theme for basic funcionality of LaCrud via:

```
php artisan vendor:publish
```

## Basic use

Once time instaled LaCrud, you can using directly in your routes' files:

```php
App::singleton('LaCrud_Routes', function(){
    return [
        'users',
        'multimedia'
    ];
});

LaCrud::RegisterCrud(app('LaCrud_Routes'));
]);
```

You should declare `LaCrud_Routes` via `App::singleton`, so, this form you can access to routes declared under LaCrud

Where every element in the array is a database's table. Now, you can access in the browser to:

```
http://project.app/users
```

In the case that you required change the theme for default of LaCrud, you should do:

```php
App::singleton('LaCrud_Routes', function(){
    return [
        'users',
        'multimedia'
    ];
});

LaCrud::theme('MyAwesomeTheme')
    ->RegisterCrud(app('LaCrud_Routes'));
```

The theme integrated by default in LaCrud show all tables in the sidebar menu, but in the case that you don't show a table in the menu, you should configured as follow:

```php
App::singleton('LaCrud_Routes', function(){
    return [
        'users',
        'posts' => [
            'showInMenu' => false
        ],
    ];
});

LaCrud::theme('MyAwesomeTheme')
    ->RegisterCrud(app('LaCrud_Routes'));
```

So, how in the array of routes you only register tha name's table and no a route, you can use the `prefix` method for indicated a comun path:

```php
App::singleton('LaCrud_Routes', function(){
    return [
        'users',
        'posts' => [
            'showInMenu' => false
        ],
    ];
});

LaCrud::theme('MyAwesomeTheme')
    ->prefix('admin')
    ->RegisterCrud(app('LaCrud_Routes'));
```
Now, you can access in your browser via:

```
http://project.app/admin/users
```

**THIS APPLY FOR ALL ROUTES, NOT A SINGLE BY TABLE**

In the case that you required add an alias for you table, add validations in the fiels, whatever, custom your CRUD in the table, you should indicate via controller on follow:

```php
App::singleton('LaCrud_Routes', function(){
    return [
        'users' => 'UsuariosController',
        'posts' => [
            'showInMenu' => false
        ],
    ];
});

LaCrud::theme('MyAwesomeTheme')
    ->RegisterCrud(app('LaCrud_Routes'));
```

**On Laravel 5 all work with namespaces**, LaCrud assume that the controller's namespace is `App\Http\Controllers`, in the case that you have a sub.namespace of your LaCrud's controllers, you should indicate in the value of array, example: `'users' => 'LaCrud\UsersController'`, this find the class in `App\Http\Controller\LaCrud\UsersController`

The next step is create a custom controller.


## Customing your entity

### Basic Controller

Once time asigned the controller's name in the route, only should create a file, in the case of Laravel 5 these found un `App/Http/Controllers/`. In the last example, we should create `UsersController.php`, and write the follow code:
```php
<?php namespace App\Http\Controllers;

use DevSwert\LaCrud\Configuration;
use DevSwert\LaCrud\Controller\LaCrudBaseController;
use DevSwert\LaCrud\Data\Manager\LaCrudManager;
use DevSwert\LaCrud\Data\Repository\LaCrudRepository;

class UsersController extends LaCrudBaseController {

	function __construct(LaCrudRepository $repository, LaCrudManager $manager, Configuration $config){
		$this->repository = $repository;
		$this->manager = $manager;
		$this->configuration = $config;

		$this->repository->table('users');

		$this->configuration->title('Users');
		$this->configuration->subtitle('User's Control);
	}

}
```

How you look see, the controller don't extends from *BaseController* of Laravel, this should do of *LaCrudBaseController*.

The constructir of own *UsersController* now should get 3 dependencies, an object *LaCrudRepository, LaCrudManager y Configuration*.

Via the objects *LaCrudRepository* and *LaCrudManager* is how you do and customs the configuration essencials according to each developer on his project. For example, the basic change is indicate the true table's name that LaCrud use how the data entity.

The configuration of title and sibtitle are optionals, and these used in the template system.

**All changes and configuratios in the repositories and managers should are do in the constructor, by this form aplly in al routes**

### Restrict of fields

Exits two types of restricts in the fieds, for that the user **can't see**, this apply in the complete list of register and the detail of each row, and the other type are the user **can't edit**, here we omit the fields for create or edit a register in the system.

For that the user don't see some fields. we should edit a propierty in own repository:

```php
$this->repository->fieldsNotSee = [
	'password',
	'remember_token'
];
```

And in the case we dont't want that user don't edit fields, we can sett a propierty in the manager:

```php
$this->manager->fieldsNotEdit = [
	'remember_token'
];
```

### Alias in fields

If we don't want that display the real field's name of database, we can use an alias:

```php
$this->repository->displayAs = [
	'created_at' => 'Created',
	'updated_at' => 'Updated',
	'password' => 'Secret'
];
```

### Fields type encrypted

If we have fields that need be 'encrypted' or use a Hash of Laravel, we should modify:

```php
$this->repository->isEncrypted = [
	'password'
];
```

This fields will be automatically setting in blank in the moment of the register's editing, and if will send a blank field in the moment to the edition, LaCrud will keep the value stored in the database,

### Foreigns Relations

If a field has a foreign key by database, this will be load automatically in a 'select' display by value the primary key of the "remote table", but if we want display another field to see we can:

```php
$this->repository->nameDisplayForeignsKeys = [
	'parent_id' => 'username'
];
```

Where *parent_id* is the local table's name that has the relation and *username* is a field of "remote table" that will be display in a select.
