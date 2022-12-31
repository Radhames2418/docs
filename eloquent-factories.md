# Eloquent: Fábricas

- [Introducción](#introduction)
- [Definición de fábricas modelo](#defining-model-factories)
  - [Generación de fábricas](#generating-factories)
  - [Estados de fábrica](#factory-states)
  - [Devoluciones de llamada de fábrica](#factory-callbacks)
- [Creación de modelos mediante fábricas](#creating-models-using-factories)
  - [Instanciación de modelos](#instantiating-models)
  - [Persistencia de modelos](#persisting-models)
  - [Secuencias](#sequences)
- [Relaciones de Fábrica](#factory-relationships)
  - [Tiene Muchas Relaciones](#has-many-relationships)
  - [Pertenece a relaciones](#belongs-to-relationships)
  - [Relaciones de muchos a muchos](#many-to-many-relationships)
  - [Relaciones polimórficas](#polymorphic-relationships)
  - [Definición de relaciones dentro de las fábricas](#defining-relationships-within-factories)
  - [Reciclaje de un Modelo Existente de Relaciones](#recycling-an-existing-model-for-relationships)

[]()

## Introducción

Cuando esté probando su aplicación o sembrando su base de datos, puede que necesite insertar algunos registros en su base de datos. En lugar de especificar manualmente el valor de cada columna, Laravel te permite definir un conjunto de atributos por defecto para cada uno de tus [modelos Eloquent](/docs/%7B%7Bversion%7D%7D/eloquent) utilizando fábricas de modelos.

Para ver un ejemplo de cómo escribir una fábrica, echa un vistazo al fichero `database/factories/UserFactory.` php de tu aplicación. Esta fábrica se incluye con todas las nuevas aplicaciones Laravel y contiene la siguiente definición de fábrica:

    namespace Database\Factories;

    use Illuminate\Database\Eloquent\Factories\Factory;
    use Illuminate\Support\Str;

    class UserFactory extends Factory
    {
        /**
         * Define the model's default state.
         *
         * @return array
         */
        public function definition()
        {
            return [
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
            ];
        }
    }

Como puedes ver, en su forma más básica, las factorías son clases que extienden la clase base factory de Laravel y definen un método de `definición`. El método de `definición` devuelve el conjunto predeterminado de valores de atributo que deben aplicarse al crear un modelo utilizando la fábrica.

A través del ayudante `fake`, las fábricas tienen acceso a la librería [Faker](https://github.com/FakerPHP/Faker) PHP, que te permite generar convenientemente varios tipos de datos aleatorios para pruebas y siembra.

> **Nota**  
> Puedes establecer la localización Faker de tu aplicación añadiendo una opción `faker_locale` a tu fichero de configuración `config/app.` php.

[]()

## Definición de fábricas de modelos

[]()

### Generación de fábricas

Para crear una fábrica, ejecuta el [comando](/docs/%7B%7Bversion%7D%7D/artisan) `make:factory` [Artisan](/docs/%7B%7Bversion%7D%7D/artisan):

```shell
php artisan make:factory PostFactory
```

La nueva clase de fábrica se colocará en su directorio `database/factories`.

[]()

#### Convenciones de descubrimiento de modelos y fábricas

Una vez que haya definido sus fábricas, puede utilizar el método de `fábrica` estática proporcionada a sus modelos por el `Illuminate\Database\Eloquent\Factories\HasFactory` rasgo con el fin de instanciar una instancia de fábrica para ese modelo.

El método de `fábrica` del rasgo `HasFactory` utilizará convenciones para determinar la fábrica adecuada para el modelo al que se asigna el rasgo. En concreto, el método buscará una fábrica en el espacio de nombres `Database\Factories` que tenga un nombre de clase que coincida con el nombre del modelo y que tenga el sufijo `Factory`. Si estas convenciones no se aplican a su aplicación o fábrica en particular, puede sobrescribir el método `newFactory` en su modelo para devolver directamente una instancia de la fábrica correspondiente del modelo:

    use Database\Factories\Administration\FlightFactory;

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return FlightFactory::new();
    }

A continuación, defina una propiedad de `modelo` en la fábrica correspondiente:

    use App\Administration\Flight;
    use Illuminate\Database\Eloquent\Factories\Factory;

    class FlightFactory extends Factory
    {
        /**
         * The name of the factory's corresponding model.
         *
         * @var string
         */
        protected $model = Flight::class;
    }

[]()

### Estados de Fábrica

Los métodos de manipulación de estado le permiten definir modificaciones discretas que se pueden aplicar a sus fábricas de modelos en cualquier combinación. Por ejemplo, su fábrica `Database\Factories\UserFactory` podría contener un método de estado `suspendido` que modifique uno de sus valores de atributo por defecto.

Los métodos de transformación de estado suelen llamar al método `state` proporcionado por la clase factory base de Laravel. El método `state` acepta un closure que recibirá el array de atributos raw definidos para la fábrica y debería devolver un array de atributos a modificar:

    /**
     * Indicate that the user is suspended.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function suspended()
    {
        return $this->state(function (array $attributes) {
            return [
                'account_status' => 'suspended',
            ];
        });
    }

#### "Estado "Desechado

Si tu modelo Eloquent puede ser [borrado suav](/docs/%7B%7Bversion%7D%7D/eloquent#soft-deleting)emente, puedes invocar el método de estado `"trashed"` incorporado para indicar que el modelo creado ya debe ser "borrado suavemente". No es necesario definir manualmente el estado "trashed `"`, ya que está disponible automáticamente para todas las fábricas:

    use App\Models\User;

    $user = User::factory()->trashed()->create();

[]()

### Devoluciones de Fábrica

Los callbacks de fábrica se registran usando los métodos `afterMaking` y `afterCreating` y permiten realizar tareas adicionales después de crear un modelo. Debes registrar estas retrollamadas definiendo un método `configure` en tu clase factory. Este método será llamado automáticamente por Laravel cuando la fábrica sea instanciada:

    namespace Database\Factories;

    use App\Models\User;
    use Illuminate\Database\Eloquent\Factories\Factory;
    use Illuminate\Support\Str;

    class UserFactory extends Factory
    {
        /**
         * Configure the model factory.
         *
         * @return $this
         */
        public function configure()
        {
            return $this->afterMaking(function (User $user) {
                //
            })->afterCreating(function (User $user) {
                //
            });
        }

        // ...
    }

[]()

## Creación de modelos mediante fábricas

[]()

### Instanciación de modelos

Una vez que haya definido sus fábricas, puede utilizar el método de `fábrica` estática proporcionada a sus modelos por el `Illuminate\Database\Eloquent\Factories\HasFactory` rasgo con el fin de instanciar una instancia de fábrica para ese modelo. Veamos algunos ejemplos de creación de modelos. En primer lugar, vamos a utilizar el método `make` para crear modelos sin persistir en la base de datos:

    use App\Models\User;

    $user = User::factory()->make();

Puedes crear una colección de muchos modelos usando el método `count`:

    $users = User::factory()->count(3)->make();

[]()

#### Aplicando Estados

También puedes aplicar cualquiera de tus [estados](#factory-states) a los modelos. Si desea aplicar múltiples transformaciones de estado a los modelos, puede simplemente llamar directamente a los métodos de transformación de estado:

    $users = User::factory()->count(5)->suspended()->make();

[]()

#### Sobreescribiendo Atributos

Si quieres sobreescribir algunos de los valores por defecto de tus modelos, puedes pasar un array de valores al método `make`. Sólo los atributos especificados serán reemplazados mientras que el resto de los atributos permanecerán con sus valores por defecto especificados por la fábrica:

    $user = User::factory()->make([
        'name' => 'Abigail Otwell',
    ]);

Alternativamente, el método `state` puede ser llamado directamente en la instancia de la fábrica para realizar una transformación de estado en línea:

    $user = User::factory()->state([
        'name' => 'Abigail Otwell',
    ])->make();

> **Nota**  
> [La protección de asignación masiva](/docs/%7B%7Bversion%7D%7D/eloquent#mass-assignment) se desactiva automáticamente al crear modelos utilizando fábricas.

[]()

### Persistencia de modelos

El método `create` crea instancias del modelo y las guarda en la base de datos utilizando el método `save` de Eloquent:

    use App\Models\User;

    // Create a single App\Models\User instance...
    $user = User::factory()->create();

    // Create three App\Models\User instances...
    $users = User::factory()->count(3)->create();

Puedes sobreescribir los atributos de modelo por defecto de la fábrica pasando un array de atributos al método `create`:

    $user = User::factory()->create([
        'name' => 'Abigail',
    ]);

[]()

### Secuencias

A veces puede que desee alternar el valor de un atributo de modelo dado para cada modelo creado. Esto se consigue definiendo una transformación de estado como una secuencia. Por ejemplo, puede que desee alternar el valor de una columna `admin` entre `Y` y `N` para cada usuario creado:

    use App\Models\User;
    use Illuminate\Database\Eloquent\Factories\Sequence;

    $users = User::factory()
                    ->count(10)
                    ->state(new Sequence(
                        ['admin' => 'Y'],
                        ['admin' => 'N'],
                    ))
                    ->create();

En este ejemplo, cinco usuarios serán creados con un valor `admin` de `Y` y cinco usuarios serán creados con un valor `admin` de `N`.

Si es necesario, puede incluir un closure como valor de secuencia. El closure se invocará cada vez que la secuencia necesite un nuevo valor:

    $users = User::factory()
                    ->count(10)
                    ->state(new Sequence(
                        fn ($sequence) => ['role' => UserRoles::all()->random()],
                    ))
                    ->create();

Dentro de un cierre closure secuencia, puede acceder a las propiedades `$index` o `$count` en la instancia de secuencia que se inyecta en el closure. La propiedad `$index` contiene el número de iteraciones a través de la secuencia que se han producido hasta el momento, mientras que la propiedad `$count` contiene el número total de veces que la secuencia será invocada:

    $users = User::factory()
                    ->count(10)
                    ->sequence(fn ($sequence) => ['name' => 'Name '.$sequence->index])
                    ->create();

[]()

## Relaciones de Fábrica

[]()

### Tiene Muchas Relaciones

A continuación, vamos a explorar la construcción de relaciones entre modelos Eloquent utilizando los métodos de fábrica fluidos de Laravel. En primer lugar, vamos a suponer que nuestra aplicación tiene un modelo `App\Models\User` y un modelo `App\Models\Post`. Además, supongamos que el modelo `User` define una relación `hasMany` con `Post`. Podemos crear un usuario que tenga tres posts utilizando el método `has` proporcionado por las factorías de Laravel. El método `has` acepta una instancia de fábrica:

    use App\Models\Post;
    use App\Models\User;

    $user = User::factory()
                ->has(Post::factory()->count(3))
                ->create();

Por convención, al pasar un modelo `Post` al método `has`, Laravel asumirá que el modelo `User` debe tener un método `posts` que defina la relación. Si es necesario, puede especificar explícitamente el nombre de la relación que desea manipular:

    $user = User::factory()
                ->has(Post::factory()->count(3), 'posts')
                ->create();

Por supuesto, puedes realizar manipulaciones de estado en los modelos relacionados. Además, puedes pasar una transformación de estado basada en un closure si tu cambio de estado requiere acceso al modelo padre:

    $user = User::factory()
                ->has(
                    Post::factory()
                            ->count(3)
                            ->state(function (array $attributes, User $user) {
                                return ['user_type' => $user->type];
                            })
                )
                ->create();

[]()

#### Uso de métodos mágicos

Por conveniencia, puedes usar los métodos mágicos de relación de Laravel para construir relaciones. Por ejemplo, el siguiente ejemplo utilizará la convención para determinar que los modelos relacionados deben ser creados a través de un método de relación `posts` en el modelo `User`:

    $user = User::factory()
                ->hasPosts(3)
                ->create();

Cuando se utilizan métodos mágicos para crear relaciones de fábrica, puede pasar una array de atributos para anular en los modelos relacionados:

    $user = User::factory()
                ->hasPosts(3, [
                    'published' => false,
                ])
                ->create();

Puedes proporcionar una transformación de estado basada en un closure si tu cambio de estado requiere acceso al modelo padre:

    $user = User::factory()
                ->hasPosts(3, function (array $attributes, User $user) {
                    return ['user_type' => $user->type];
                })
                ->create();

[]()

### Pertenece a relaciones

Ahora que hemos explorado cómo construir relaciones "has many" usando fábricas, exploremos la inversa de la relación. El método `for` puede usarse para definir el modelo padre al que pertenecen los modelos creados por la fábrica. Por ejemplo, podemos crear tres instancias del modelo `App\Models\Post` que pertenecen a un único usuario:

    use App\Models\Post;
    use App\Models\User;

    $posts = Post::factory()
                ->count(3)
                ->for(User::factory()->state([
                    'name' => 'Jessica Archer',
                ]))
                ->create();

Si ya tiene una instancia del modelo padre que debería estar asociada con los modelos que está creando, puede pasar la instancia del modelo al método `for`:

    $user = User::factory()->create();

    $posts = Post::factory()
                ->count(3)
                ->for($user)
                ->create();

[]()

#### Uso de métodos mágicos

Para mayor comodidad, puedes utilizar los métodos mágicos de relación de fábrica de Laravel para definir las relaciones "belongs to". Por ejemplo, el siguiente ejemplo utilizará la convención para determinar que los tres posts deben pertenecer a la relación `user` en el modelo `Post`:

    $posts = Post::factory()
                ->count(3)
                ->forUser([
                    'name' => 'Jessica Archer',
                ])
                ->create();

[]()

### Relaciones de muchos a muchos

Al igual que las [relaciones has many](#has-many-relationships), las relaciones "many to many" pueden crearse utilizando el método `has`:

    use App\Models\Role;
    use App\Models\User;

    $user = User::factory()
                ->has(Role::factory()->count(3))
                ->create();

[]()

#### Atributos de la tabla dinámica

Si necesita definir los atributos que deben establecerse en la tabla pivotante / intermedia que vincula los modelos, puede utilizar el método `hasAttached`. Este método acepta una array de nombres y valores de atributos de tabla dinámica como segundo argumento:

    use App\Models\Role;
    use App\Models\User;

    $user = User::factory()
                ->hasAttached(
                    Role::factory()->count(3),
                    ['active' => true]
                )
                ->create();

Puede proporcionar una transformación de estado basada en el closure si su cambio de estado requiere el acceso al modelo relacionado:

    $user = User::factory()
                ->hasAttached(
                    Role::factory()
                        ->count(3)
                        ->state(function (array $attributes, User $user) {
                            return ['name' => $user->name.' Role'];
                        }),
                    ['active' => true]
                )
                ->create();

Si ya dispone de instancias de modelo que desea adjuntar a los modelos que está creando, puede pasar las instancias de modelo al método `hasAttached`. En este ejemplo, los mismos tres roles se adjuntarán a los tres usuarios:

    $roles = Role::factory()->count(3)->create();

    $user = User::factory()
                ->count(3)
                ->hasAttached($roles, ['active' => true])
                ->create();

[]()

#### Uso de métodos mágicos

Por conveniencia, puedes usar los métodos de relación de la fábrica mágica de Laravel para definir relaciones de muchos a muchos. Por ejemplo, el siguiente ejemplo utilizará la convención para determinar que los modelos relacionados deben ser creados a través de un método de relación `roles` en el modelo `User`:

    $user = User::factory()
                ->hasRoles(1, [
                    'name' => 'Editor'
                ])
                ->create();

[]()

### Relaciones polimórficas

Las[relaciones polimórficas](/docs/%7B%7Bversion%7D%7D/eloquent-relationships#polymorphic-relationships) también pueden crearse utilizando fábricas. Las relaciones polimórficas "morph many" se crean de la misma manera que las relaciones típicas "has many". Por ejemplo, si un modelo `App\Models\Post` tiene una relación `morphMany` con un modelo `App\Models\Comment`:

    use App\Models\Post;

    $post = Post::factory()->hasComments(3)->create();

[]()

#### Morph To Relationships

No se pueden utilizar métodos mágicos para crear relaciones `morphTo`. En su lugar, se debe utilizar directamente el método `for` y proporcionar explícitamente el nombre de la relación. Por ejemplo, imagine que el modelo `Comment` tiene un método `commentable` que define una relación `morphTo`. En esta situación, podemos crear tres comentarios que pertenezcan a una única entrada utilizando directamente el método `for`:

    $comments = Comment::factory()->count(3)->for(
        Post::factory(), 'commentable'
    )->create();

[]()

#### Relaciones polimórficas Many To Many

Las relaciones polimórficas "muchos a muchos"`(morphToMany` / `morphedByMany`) pueden crearse igual que las relaciones no polimórficas "muchos a muchos":

    use App\Models\Tag;
    use App\Models\Video;

    $videos = Video::factory()
                ->hasAttached(
                    Tag::factory()->count(3),
                    ['public' => true]
                )
                ->create();

Por supuesto, el método mágico `has` también puede utilizarse para crear relaciones polimórficas "muchos a muchos":

    $videos = Video::factory()
                ->hasTags(3, ['public' => true])
                ->create();

[]()

### Definición de relaciones dentro de las fábricas

Para definir una relación dentro de su fábrica de modelos, normalmente asignará una nueva instancia de fábrica a la clave externa de la relación. Esto se hace normalmente para las relaciones "inversas" como las relaciones `belongsTo` y `morphTo`. Por ejemplo, si desea crear un nuevo usuario al crear una entrada, puede hacer lo siguiente:

    use App\Models\User;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->title(),
            'content' => fake()->paragraph(),
        ];
    }

Si las columnas de la relación dependen de la fábrica que la define puedes asignar un closure a un atributo. El closure recibirá el array atributos evaluados de la fábrica:

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'user_type' => function (array $attributes) {
                return User::find($attributes['user_id'])->type;
            },
            'title' => fake()->title(),
            'content' => fake()->paragraph(),
        ];
    }

[]()

### Reciclaje de un modelo existente de relaciones

Si tienes modelos que comparten una relación común con otro modelo, puedes utilizar el método `recycle` para asegurarte de que una única instancia del modelo relacionado se recicla para todas las relaciones creadas por la fábrica.

Por ejemplo, imagine que tiene modelos `Aerolínea`, `Vuelo` y `Billete`, donde el billete pertenece a una aerolínea y a un vuelo, y el vuelo también pertenece a una aerolínea. Al crear billetes, probablemente querrá la misma aerolínea tanto para el billete como para el vuelo, por lo que puede pasar una instancia de aerolínea al método `recycle`:

    Ticket::factory()
        ->recycle(Airline::factory()->create())
        ->create();

El método `recycle` puede resultarle especialmente útil si tiene modelos que pertenecen a un usuario o equipo común.

El método `recycle` también acepta una colección de modelos existentes. Cuando se proporciona una colección al método `recycle`, se elegirá un modelo aleatorio de la colección cuando la fábrica necesite un modelo de ese tipo:

    Ticket::factory()
        ->recycle($airlines)
        ->create();
