## LaCrud

LaCrud es una herramienta que te ayudará a crear CRUDs en Laravel de manera rápida y sin romper patrones de diseño dentro de tu aplicación, ya que solo es un componente que se integra y trata de amoldar a tu desarrollo.

LaCrud está construido para que funcione con la nueva **versión 5 de Laravel** :)

En estos momentos, LaCrud se encuentra en una versión alpha y en desarrollo, pero ya se pueden aprovehar algunos de sus beneficios que son:

 * Indicar el nombre de la tabla y LaCrud realizará el trabajo por ti.
 * Todo el proceso de un CRUD funcionado en solo unos minutos.
 * Detección de relaciones nativas de tu Base de Datos
 * Capacidad de crear relaciones foráneas falsas por código.
 * Lograr agregar relaciones "n a n" en tu entidad.
 * Capacidad de denegar operaciones del CRUD.
 * Filtro de contenido en la vista.
 * Visualización de solo algunos campos en las distintas operaciones.

Aunque LaCrud ya está operativo en su gran medida, también tiene objetivos para su versión 1, entre ellos:

 * Pasar los botones y textos como paquete de idiomas.
 * Soporte para que agregues tu propio tema en LaCrud.
 * Helper para carga de imágenes y manipulación de estas.
 * Funciones de imprimir y exportar datos.
 * Callbacks para las diferentes operaciones.

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
'DevSwert\LaCrud\Providers\LaCrudServiceProvider'
```

- Facade
```
'LaCrud'	=> 'DevSwert\LaCrud\Facades\LaCrud'
```

## Uso Básico

Una vez instalado LaCrud, ya puedes usarlo directamente en tu archivo de rutas de la siguiente manera:

```php
LaCrud::RegisterCrud([
	'users',
	'posts',
    'otra_tabla'
]);
```
Donde cada elemento del array es una tabla en la Base de Datos. Ahora, se podría ingresar desde el navegador a:

```
http://proyecto.app/users
```
El tema integrado por defecto en LaCrud listará todas las tablas en un menu lateral.

Ya que mediante el array solo se registra el nombre de la tabla y no una ruta hasta ella, se puede agregar el metodo `prefix` para indicar un path común.

```php
LaCrud::prefix('admin')
->RegisterCrud([
	'users',
	'posts',
    'otra_tabla'
]);
```

De esta manera, ahora se debe acceder a él mediante:

```
http://proyecto.app/admin/users
```

**ESTO APLICA PARA TODAS LAS RUTAS, NO ES INDIVIDUAL**

En el caso que se quieran agregar un alias a la tabla, agregar validadores a los campos, en si, personalizar tu CRUD a una tabla, debes indicarlo mediante un controlador de la siguiente manera:

```php
LaCrud::appName('MyAppNamespace')
->RegisterCrud([
	'usuarios' => 'UsersController',
	'posts',
    'otra_tabla'
]);
```
**Ya que Laravel 5 trabaja en base a namespaces es necesario indicarle a LaCrud en que namespace tiene tu aplicación, para que así pueda acceder a los controladores**

El próximo paso es crear un controlador como se ve en la sección que sigue.

## Personaliza tu entidad

### Controlador Básico

Una vez asignado el nombre del controlador en la ruta, lo que resta es crear el archivo, en el caso de Laravel 5 estos se encuentran en `app/Http/Controllers/`. En el caso del ejemplo anterior, debemos crear `UsersController.php`, el cual debería lucir así:

```php
<?php namespace TuNamespaceApp\Http\Controllers;

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

	public function index(){
		return $this->render();
	}

	public function create(){
		return $this->baseCreate();
	}

	public function store(){
		return $this->baseStore();
	}

	public function show($id){
		return $this->baseShow($id);
	}

	public function edit($id){
		return $this->baseEdit($id);
	}

	public function update($id){
		return $this->baseUpdate($id);
	}

	public function destroy($id){
		return $this->baseDestroy($id);
	}

}
```

Como se logra apreciar, nuestro controlador ya no extiende el *BaseController* que ofrece Laravel, debe hacerlo de *LaCrudBaseController*, esta es una clase abstracta, por lo que la implementación de los metodos `index`, `create`, `store`, `show`, `edit`, `update` y `destroy` son **obligatorios**.

El constructor de nuestro *UsersController* ahora debe recibir 3 dependencias, un objecto *LaCrudRepository, LaCrudManager* y *Configuration*.

Mediantes los objetos *LaCrudRepository* y *LaCrudManager* es como se realizarán y ajustarán las configuraciones necesarias según cada desarrollador en su proyecto. Por ejemplo, el cambio básico es indicar el verdadero nombre de la tabla que se usará como Entidad de datos.

La configuración de Título y Subtítulo son opcionales, y se utilizan en el Template del sistema.

**Todos los cambios y configuraciones en los repositorios y manager es recomendable que los realicen en el constructor, para que asi aplique en todas las rutas**.

### Restricción de campos

Existen dos tipos de restricciones de campos, unos para que el usuario **no los pueda visualizar**, estos aplican en la lista completa de los registros y el detalle de cada uno, y el otro tipo son los que el usuario **no puede editar**, aquí se omitirán los campos para agregar un nuevo registro o actualizarlo.

Para que un usuario no pueda ver ciertos campos, debemos editar una propiedad en nuestro repositorio:

```php
$this->repository->fieldsNotSee = array(
	'password',
	'remember_token'
);
```

Y en el caso de que no queremos que edite otros campos debemos modificar el manager:

```php
$this->manager->fieldsNotEdit = array(
	'remember_token',
);
```

### Alias de campos

Si no queremos que se despliege el nombre original de nuestro campo de la Base de Datos, podemos establecer alias:

```php
$this->repository->displayAs = array(
	'created_at' => 'Creado',
	'updated_at' => 'Actualizado',
	'password' => 'Contraseña'
);
```

### Campos tipo password

Si tenemos campos que necesitamos sean `password` o se les aplique un Hash de Laravel, debemos modificar:

```php
$this->repository->isPassword = array(
	'password'
);
```

### Relaciones foráneas nativas

Si un campo tiene una clave foránea establecida por Base de Datos esta se cargará automáticamente en un `select` mostrando por valor la clave primaria de la "tabla remota", pero si queremos desplegar otro dato para visualizar podemos:

```php
$this->repository->nameDisplayForeignsKeys = array(
	'parent_id' => 'username'
);
```

Donde *parent_id* en el nombre del campo de la tabla local que posee la relación y *username* en un campo de la "tabla remota",

### Relaciones foráneas falsas

Muchas veces, por razones que nadie entiende, la relaciones no están establecidas por Base de Datos. LaCrud ofrece una configuración para que crees tus relaciones por código, las cuales tendrán el mismo efecto que una relación nativa:

```php
$this->repository->fakeRelation = array(
	'fake_user' => array(
		'table' => 'users',
		'field' => 'id',
		'alias' => 'username',//Optional
	),
);
```

Donde *fake_user* en el nombre del campo en nuestra tabla local que deseamos relacionar con los datos indicados en su array de opciones, *field* seria lo similar a la primary key foranea.

### Relaciones muchos a muchos

Si la tabla de nuestro tiene relaciones muchos a muchos dentro de su sistema, LaCrud posee una configuración especial para que se anclen estas relaciones.

```php
$this->repository->manyRelations = array(
	'post_de_usuarios' => array(
		'pivot' => array(
			'table' => 'pivot',
			'local_key'  => 'user_id',
			'remote_key' => 'post_id',
			//'order' => 'order'
		),
		'remote' => array(
			'key' => 'id', // By default it's id
			'table' => 'posts',
			'display' => 'title'
		),
		'local_key' => 'id', //Optional
	)
);
```

### Validaciones

Es muy importante mantener siempre estar validando lo que ingrese nuestro usuario final al sistema, por lo que LaCrud aprovecha el sistema de validaciones de Laravel para resolverlo, esto debe ser aplicado de la siguiente manera:

```php
$this->manager->rules = array(
	'password' => 'required'
);
```

### Deshabilitar opciones del CRUD

En el caso que se requiera deshabilitar ciertas caracteristicas del CRUD se puede usar:

- Para deshabilitar la edicion

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
- Para deshabilitar que tenga acceso a eliminar registros.

```php
$this->unsetDelete();
```

### Filtros al listar

En el caso que en la lista general se necesite mostrar un filtro de registros LaCrud tiene metodos para ayudar esa tarea. Se recomienda que se acceda a ellos en el metodo *index*  y no en el constructor del controlador. Un ejemplo quedaria similar a:

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
