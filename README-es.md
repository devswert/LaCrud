## LaCrud

LaCrud es una herramienta que te ayudará a crear CRUDs en Laravel de manera rápida y sin romper patrones de diseño dentro de tu aplicación, ya que solo es un componente que se integra y trata de amoldar a tu desarrollo.

LaCrud está construido para que funcione con la nueva **versión 5 de Laravel** :)

En estos momentos, LaCrud se encuentra en una versión de desarrollo, pero ya se pueden aprovechar algunos de sus beneficios que son:

 * Indica el nombre de la tabla y LaCrud realizará el trabajo por ti.
 * Todo el proceso de un CRUD funcionado en solo unos minutos.
 * Detección de relaciones nativas de tu Base de Datos
 * Capacidad de crear relaciones foráneas falsas por código.
 * Lograr agregar relaciones "n a n" en tu entidad.
 * Capacidad de denegar operaciones del CRUD.
 * Filtro de contenido en la vista.
 * Visualización de solo algunos campos en las distintas operaciones.
 * Los textos de botones y notificaciones estan bajo paquetes de idiomas.
 * Soporte para que agregues tu propio tema de LaCrud.
 * Helper para carga de imágenes y manipulación de estas.

Aunque LaCrud ya está operativo en su gran medida, también tiene objetivos para su versión 1, entre ellos:

 * Funciones de imprimir y exportar datos.
 * Callbacks para las diferentes operaciones.
 * Eliminación masiva para cuando existan registros relacionados por claves foraneas en tu Base de Datos
 * Eliminar archivos estaticos al momento que cambian los valores de los campos tipo uploads.
 * Mejor manipulación de imaganes a traves de [Intervention Image](http://image.intervention.io/)

## Instalación

Puedes instalar LaCrud via Composer

```
composer require devswert/lacrud dev-master
```

O agregarlo en tu composer.json y ejecutar composer update

```
"devswert/lacrud": "dev-master"
```

Una vez terminada la instalación, debes agregar el ServiceProvider y Facade de LaCrud en tu configuración (config/app.php)

 - ServiceProvider
```
'DevSwert\LaCrud\LaCrudServiceProvider'
```

- Facade
```
'LaCrud'	=> 'DevSwert\LaCrud\LaCrudFacade'
```

El último paso es publicar los assets y el tema básico para el funcionamiento de LaCrud mediante:

```
php artisan vendor:publish
```

## Uso Básico

Una vez instalado LaCrud, ya puedes usarlo directamente en tu archivo de rutas de la siguiente manera:

```php
App::singleton('LaCrud_Routes', function(){
    return [
	    'users',
	    'multimedia'
	];
});

LaCrud::RegisterCrud(app('LaCrud_Routes'));
```
Se debe declara *LaCrud_Routes*, mediante esta instancia en la App, asi, cualquier request a la aplicación puede tener acceso a cuales son las rutas declaradas bajo LaCrud.

Donde cada elemento del array es una tabla en la Base de Datos. Ahora, se podría ingresar desde el navegador a:

```
http://proyecto.app/users
```

En el caso que se requiera cambiar el tema por defecto de LaCrud, se debe realizar de la siguiente manera:

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
El tema integrado por defecto en LaCrud listará todas las tablas en un menu lateral, pero en el caso que no se quiera agregar cierto indice en el menú, se debe indicar de la siguiente manera:

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

Ya que mediante el array solo se registra el nombre de la tabla y no una ruta hasta ella, se puede agregar el metodo `prefix` para indicar un path común.

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

De esta manera, ahora se debe acceder a él mediante:

```
http://proyecto.app/admin/users
```

**ESTO APLICA PARA TODAS LAS RUTAS, NO ES INDIVIDUAL**

En el caso que se quieran agregar un alias a la tabla, agregar validadores a los campos, en si, personalizar tu CRUD a una tabla, debes indicarlo mediante un controlador de la siguiente manera (también se agregó el cómo quedaría si no se quiere mostrar en el menu):

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

**Ya que Laravel 5 trabaja en base a namespaces**, LaCrud asume que el namespace de tus controladores se encuentra en `App\Http\Controllers`, en el caso que se tenga un sub-namespace de los controladores para LaCrud, deben enunciarse en el valor del array, por ej: `'usuarios' => 'LaCrud\UsuariosController'`, esto buscara la clase `App\Http\Controller\LaCrud\UsuariosController`

El próximo paso es crear un controlador como se ve en la sección que sigue.

## Personaliza tu entidad

### Controlador Básico

Una vez asignado el nombre del controlador en la ruta, lo que resta es crear el archivo, en el caso de Laravel 5 estos se encuentran en `App/Http/Controllers/`. En el caso del ejemplo anterior, debemos crear `UsersController.php`, el cual debería lucir así:

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

		$this->configuration->title('Usuarios');
		$this->configuration->subtitle('Control de Usuarios');

	}

}
```

Como se logra apreciar, nuestro controlador ya no extiende el *BaseController* que ofrece Laravel, debe hacerlo de *LaCrudBaseController*.

El constructor de nuestro *UsersController* ahora debe recibir 3 dependencias, un objecto *LaCrudRepository, LaCrudManager* y *Configuration*.

Mediantes los objetos *LaCrudRepository* y *LaCrudManager* es como se realizarán y ajustarán las configuraciones necesarias según cada desarrollador en su proyecto. Por ejemplo, el cambio básico es indicar el verdadero nombre de la tabla que se usará como Entidad de datos.

La configuración de Título y Subtítulo son opcionales, y se utilizan en el Template del sistema.

**Todos los cambios y configuraciones en los repositorios y manager se deben realizar en el constructor, para que así aplique en todas las rutas**.

### Restricción de campos

Existen dos tipos de restricciones de campos, unos para que el usuario **no los pueda visualizar**, estos aplican en la lista completa de los registros y el detalle de cada uno, y el otro tipo son los que el usuario **no puede editar**, aquí se omitirán los campos para agregar un nuevo registro o actualizarlo.

Para que un usuario no pueda ver ciertos campos, debemos editar una propiedad en nuestro repositorio:

```php
$this->repository->fieldsNotSee = [
	'password',
	'remember_token'
];
```

Y en el caso de que no queremos que edite otros campos debemos modificar el manager:

```php
$this->manager->fieldsNotEdit = [
	'remember_token'
];
```

### Alias de campos

Si no queremos que se despliegue el nombre original de nuestro campo de la Base de Datos, podemos establecer alias:

```php
$this->repository->displayAs = [
	'created_at' => 'Creado',
	'updated_at' => 'Actualizado',
	'password' => 'Contraseña'
];
```

### Campos tipo encrypted

Si tenemos campos que necesitamos sean `encriptados` o se les aplique un Hash de Laravel, debemos modificar:

```php
$this->repository->isEncrypted = [
	'password'
];
```

Estos campos seran automaticamente seteados en blanco al momento de la edición de un registro, y si se envian en blanco al momento de la edición se mantendra el valor de la Base de Datos.

### Relaciones foráneas nativas

Si un campo tiene una clave foránea establecida por Base de Datos esta se cargará automáticamente en un `select` mostrando por valor la clave primaria de la "tabla remota", pero si queremos desplegar otro dato para visualizar podemos:

```php
$this->repository->nativeForeignsKeys = [
	'parent_id' => 'username'
];
```

Donde *parent_id* en el nombre del campo de la tabla local que posee la relación y *username* en un campo de la "tabla remota" que se quiere desplegar en el select,

Esta opción también acepta como parametro un array donde podemos indicar una sentencia where(Solo una) de la siguiente manera:
```php
$this->repository->nativeForeignsKeys = [
	'parent_id' => [
		'alias' => 'username',
		'where' => ['username','<>','administrator']
	]
];
```

### Relaciones foráneas falsas

Muchas veces, por razones que nadie entiende, la relaciones no están establecidas por Base de Datos. LaCrud ofrece una configuración para que crees tus relaciones por código, las cuales tendrán el mismo efecto que una relación nativa:

```php
$this->repository->fakeRelation = [
	'fake_user' => [
		'table' => 'users',
		'field' => 'id',
		'alias' => 'username',//Optional
	],
];
```

Donde *fake_user* es el nombre del campo en nuestra tabla local que deseamos relacionar con los datos indicados en su array de opciones, *field* seria lo similar a la primary key foranea y *alias* es el nombre que se mostrará "amigable" para el usuario final, este último es totalmente opcional.

### Relaciones muchos a muchos

Si la tabla tiene relaciones *muchos a muchos* dentro de su sistema, LaCrud posee una configuración especial para que se anclen estas relaciones.

```php
$this->repository->manyRelations = [
	'post_de_usuarios' => [
		'pivot' => [
			'table' => 'pivot',
			'local_key'  => 'user_id',
			'remote_key' => 'post_id',
			'order' => 'order' //Optional
		],
		'remote' => [
			'key' => 'id', // By default it's id
			'table' => 'posts',
			'display' => 'title'
		],
		'local_key' => 'id', //Optional
	]
];
```

Donde `post_de_usuario` es el nombre que se desplegara en el formulario, LaCrud toma este nombre y remplaza los "_" por " " (espacios), posterior a eso aplica *camelcase* al nombre dividido.

Entre las opciones que recibe estan `pivot`, `remote` y `local_key`. En `pivot` se almacenan los datos de la tabla pivote o intermediaria entre ambas tablas principales, de esta, la llave `order` es totalmente opcional. En la clave `remote` se almacena la información de la tabla externa, de ella es importante identificar el campo `display`, que es el nombre amigable que se deplegara en el select multiple. Y la llava `local_key` se setea cuando la llave primaria de la tabla es diferente de *id*.

### Validaciones

Es muy importante mantener siempre estar validando lo que ingrese nuestro usuario final al sistema, por lo que LaCrud aprovecha el sistema de [validaciones de Laravel](http://laravel.com/docs/5.0/validation) para resolverlo, las validaciones aquí indicadas aplican para **la creación y edición de los registros** y estas deben ser aplicadas de la siguiente manera:

```php
$this->manager->rules = [
	'password' => 'required'
];
```
Aunque, por algún extraño motivo del universo, las validaciones al momento de editar un registro pueden ser diferentes que las de crear el mismo registro, para ello LaCrud dispone de `$this->manager->rulesCreate` y `$this->manager->rulesEdit`, ambas propiedades deben ser un array al igual que *rules*, y tienen prioridad por sobre `$this->manager->rules`.

### Deshabilitar opciones del CRUD

En el caso que se requiera deshabilitar ciertas caracteristicas del CRUD se puede usar:

- Para deshabilitar la edición

```php
$this->unsetEdit();
```
- Para deshabilitar que pueda ver un registro

```php
$this->unsetRead();
```
- Para deshabilitar que pueda agregar un nuevo registro

```php
$this->unsetAdd();
```
- Para deshabilitar que se tenga acceso a eliminar registros.

```php
$this->unsetDelete();
```
> Cuando se trata de acceder a estos recursos que fueron *bloqueados* LaCrud realiza el render de la vista `403.blade.php` ubicada en *partials*.

### Filtros al listar

En el caso que en la lista general se necesite mostrar un filtro de registros, LaCrud tiene métodos para ayudar a esa tarea. Para aplicar el filtro, debemos sobreescribir el metodo `index` del controlador. Un ejemplo quedaría similar a:

```php
	public function index(){
		$this->repository->orderBy('created_at','desc');
		$this->repository->where('username','=','devswert');
		return $this->render();
	}
```

Entre los métodos disponibles para filtrar, se puede acceder a:

- Like

```php
$this->repository->like($field,$value);
```
- Where

```php
$this->repository->where($field,$operator,$value);
```
- Limit

```php
$this->repository->limit(5);
```
- Ordenar Por

```php
$this->repository->orderBy($field,$method);
```
- Or Like

```php
$this->repository->orLike($field,$value);
```
- Or Where

```php
$this->repository->orWhere($field,$operator,$value);
```

### Manejo de Fechas

Por defecto, LaCrud omite la edición de los campos `created_at`, `updated_at` y `deleted_at`. Pero en el caso que se necesiten editar, se puede acceder a las funciones:

```php
$this->showUpdatedAt();
$this->showCreatedAt();
$this->showDeletedAt();
```
> Recuerda que estan funciones y se deben agregar en el constructor del controlador.

## Carga de Archivos y Fotos

Sabemos que los desarrollos no simplemente son CRUDs a datos de una tabla, muchas veces el usuario necesita cargar docuementos, subir fotos, que estas fotos al cagar se redimensionen, etc., los clientes tienen bastante imaginación para pensar en lo imposible. Pero dentro de lo posible LaCrud trae incorporado un sistema de carga de archivos y fotos, el cual también debe setearse en el constructor del controlador, la configuración quedaría un tanto similar a:

```php
$this->repository->uploads = [
	'word' => 'public/path/words',
    'excel' => [
		'private' => 'private/path',
		'public' => 'public/path'
	]
    'pdf' => [
		'public' => [
			'path' => 'uploads/js'
		]
	],
	'imagen' => [
		'public' => [
			'path'  => 'uploads/img',
			'resizes' => [
    			'lg-' => [1920,300],
    			'md-' => [120,540],
    			'sm-' => [10,30]
			]
		],
        'private' => 'private/path',
		'isImage' => true
	]
];
```

En el ejemplo de código anterior se logra apreciar todas las opciones que se pueden configurar en el sistema de carga para campos. Es importante destacar que LaCrud permite que el archivo cargado quede en un **un directorio público, privado o ambas.**

> Cuando se habla de **directorio público** se considera como ruta base el resultado de la  función [public_path() de Laravel](http://laravel.com/docs/5.0/helpers#paths).

> Cuando se habla de **directorio privado** se considera como ruta base el resultado de la función [base_path() de Laravel](http://laravel.com/docs/5.0/helpers#paths).

En el primer ejemplo de la llave `word`, el valor es la ruta pública donde se almacenará el archivo.

El segundo caso de la llave `excel`, este recibe un array con las rutas donde se almacenará el archivo de manera privada y pública.

El tercer ejemplo de la llave `pdf` es solo otra forma en la cual funciona el uso de `paths`, esto funciona, pero es recomendable la estructura para el siguiente ejemplo.

El cuarto ejemplo aplica a cuando tenemos un campo que para el usuario sera carga de una imagen. Para ello se debe indicar con una llave `isImage` como `true`. Las otras son llaves aceptadas son `public` y `private`. En ellas se puede indicar mediante un string la ruta de destino,  o, con un array indicar la llave `path`, que es la ruta de destino, y la llave `resizes`, que corresponde a las redimensiones que se realizaran a la imagen cuando se guarde o actualice el registro. El formato de este es: Como llave recibe un prefijo y como valor un array con maximo 4 valores númericos, estos valores se utilizan para ejecutar la función [crop de Intervention Image](http://image.intervention.io/api/crop).

Cuando se declara un campo de *upload* que sera una imagen, al archivo final se le agrega un prefijo de 10 caracteres entre números y letras antes del nombre original del archivo, si se realiza un resize de una imagen, primero agrega el prefijo del resize y luego el del nombre de la imagen, por ejemplo `lg-a5eg49FdeP-nombrereal.jpg`.

> **LaCrud tiene pensado agregar más características de [Intervention Image](http://image.intervention.io) en su versión 1 estable del proyecto.**

## Creación de Templates

LaCrud viene con un tema por defecto basado en [AdmiLTE](https://almsaeedstudio.com/themes/AdminLTE/index2.html).

Pero no es obligación quedarse con este tema, quizás tu proyecto ya tiene assets predefinidos y quieres usarlos en tu proyecto, para ello LaCrud consta con un sistema de Template básico para funcionar y amoldarse al desarrollo final del producto.

Para aplicar un tema al proyecto, en la rutas se debe indicar de la siguiente manera:

```php
LaCrud::theme('MyAwesomeTheme')
    ->RegisterCrud(app('LaCrud_Routes'));
```
> En estos momentos se está trabajando para tener un repositorio o fuente de temas para LaCrud.

### Estructura de Directorios

La estructura de un proyecto con LaCrud integrado luce de la siguiente manera:

```
├── ProjectName/
│   ├── (Laravel Directories)
│   ├── resources
│   │   ├── views
│   │   │   ├── partials
│   │	│	│	├── header.blade.php
│   │	│	│	├── footer.blade.php
│   │   │   ├── vendor
│   │	│	│	├── LaCrud
|	│   │	│	│	├── ThemeName
│   │	│	├── layout.blade.php
```

Y la estructura de un tema es la siguiente:
```
├── ThemeName
|	├── forms
│	│	├── checkbox.blade.php
│	│	├── date.blade.php
│	│	├── datetime.blade.php
│	│	├── hardDelete.blade.php
│	│	├── image.blade.php
│	│	├── input.blade.php
│	│	├── integer.blade.php
│	│	├── multiple-select.blade.php
│	│	├── password.blade.php
│	│	├── select-foreign.blade.php
│	│	├── select.blade.php
│	│	├── textarea.blade.php
│	│	├── textedit.blade.php
│	│	├── upload.blade.php
│	├── pages
│	│	├── create.blade.php
│	│	├── edit.blade.php
│	│	├── index.blade.php
│	│	├── show.blade.php
│	├── partials
│	│	├── 403.blade.php
│	│	├── footer.blade.php
│	│	├── header.blade.php
│	├── layout.blade.php
```
Es importante destacar que:

- Si dentro de views existe un directorio llamado `partials` con los archivos `header.blade.php`, `footer.blade.php` o `403.blade.php`, estos *pisarán* a los partials dentro dentro del tema de LaCrud seleccionado.
- Si existe un archivo `layout.blade.php` en la raiz del directorio views este también será prioridad por sobre el tema.

> Para crear un tema se puede descargar [esta base de creación](#) para personalizarla de la manera que se estime conveniente.

### Vista personalizada de "Mi Aplicación"

Nuestra aplicación también debe colgarse de los templates base de LaCrud, por ejemplo, podemos tener `/resources/admin/dashboard.blade.php`, y necesitamos que esta vista use los mismos recursos del tema de LaCrud, para ello nuestro archivo deberá lucir de esta manera:

```php
@extends('vendor.LaCrud.YourAwesomeTheme.layout')

@section('header')
    {!! LaCrud::renderHeader() !!}
@stop

@section('footer')
    {!! LaCrud::renderFooter() !!}
@stop

@section('content')
    Tu contenido de la vista con {{ $variables }} de Blade
@stop
```

### Sistema Multi-Idioma

LaCrud tiene sus mensajes de alerta, texto de botones y demás bajo el sistema de [idioma de Laravel](http://laravel.com/docs/5.0/localization), la estructura de estos es:

```
├── ProjectName/
│   ├── (Laravel Directories)
│   ├── lang
│   │   ├── LaCrud
│   │   │   ├── en
│   │	│	│	├── notifications.php
│   │	│	│	├── templates.php
```
En el archivo `notifications.php` se almacenan las alertas que arroja el sistema, como creaciones y actualizaciones exitosas.

Por otra parte, en el archivo `templates.php` se almacenan los textos de los botones de las vistas y otros detalles de los títulos.