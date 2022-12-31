# Notas de la versión

- [Sistema de control de versiones](#versioning-scheme)
- [policy soporte](#support-policy)
- [Laravel 9](#laravel-9)

[]()

## Esquema de versiones

Laravel y sus otros paquetes de origen siguen el Versionado [Semántico](https://semver.org). Las versiones mayores del framework se publican cada año (\~Febrero), mientras que las versiones menores y de parche pueden publicarse cada semana. Las versiones menores y de parche **nunca** deben contener cambios de última hora.

Cuando hagas referencia al framework Laravel o a sus componentes desde tu aplicación o paquete, deberías usar siempre una restricción de versión como `^9.0`, ya que las versiones mayores de Laravel incluyen cambios de última hora. Sin embargo, nos esforzamos para asegurar que siempre se puede actualizar a una nueva versión principal en un día o menos.

[]()

#### Argumentos con nombre

Los[argumentos con nombre](https://www.php.net/manual/en/functions.arguments.php#functions.named-arguments) no están cubiertos por las directrices de retrocompatibilidad de Laravel. Podemos optar por cambiar el nombre de los argumentos de función cuando sea necesario con el fin de mejorar la base de código de Laravel. Por lo tanto, el uso de argumentos con nombre al llamar a métodos Laravel debe hacerse con cautela y con la comprensión de que los nombres de los parámetros pueden cambiar en el futuro.

[]()

## policy soporte

Para todas las versiones de Laravel, las correcciones de errores se proporcionan durante 18 meses y las correcciones de seguridad se proporcionan durante 2 años. Para todas las bibliotecas adicionales, incluyendo Lumen, sólo la última versión principal recibe correcciones de errores. Además, por favor revise las versiones de bases de datos [soportadas por Laravel](/docs/%7B%7Bversion%7D%7D/database#introduction).

| Versión | PHP (*)   | Versión                 | Corrección de errores hasta | Correcciones de seguridad hasta |
| ------- | --------- | ----------------------- | --------------------------- | ------------------------------- |
| 6 (LTS) | 7.2 - 8.0 | 3 de septiembre de 2019 | 25 de enero de 2022         | 6 de septiembre de 2022         |
| 7       | 7.2 - 8.0 | 3 de marzo de 2020      | 6 de octubre de 2020        | 3 de marzo de 2021              |
| 8       | 7.3 - 8.1 | 8 de septiembre de 2020 | 26 de julio de 2022         | 24 de enero de 2023             |
| 9       | 8.0 - 8.2 | 8 de febrero de 2022    | 8 de agosto de 2023         | 6 de febrero de 2024            |
| 10      | 8.1 - 8.2 | 7 de febrero de 2023    | 6 de agosto de 2024         | 4 de febrero de 2025            |

<div class="version-colors">
    <div class="end-of-life">
        <div class="color-box"/>
        <div>Fin de vida</div>
    </div>
    <div class="security-fixes">
        <div class="color-box"/>
        <div>Sólo correcciones de seguridad</div>
    </div>
</div>

(\*) Versiones PHP soportadas

[]()

## Laravel 9

Como ya sabrás, Laravel hizo la transición a versiones anuales con el lanzamiento de Laravel 8. Anteriormente, las versiones principales se lanzaban cada 6 meses. Esta transición tiene por objeto aliviar la carga de mantenimiento de la comunidad y desafiar a nuestro equipo de desarrollo para lanzar nuevas características sorprendentes y potentes sin introducir cambios de última hora. Por lo tanto, hemos enviado una variedad de características robustas a Laravel 8 sin romper la compatibilidad con versiones anteriores, tales como soporte de pruebas paralelas, kits de inicio Breeze mejorados, mejoras en el cliente HTTP, e incluso nuevos tipos de relación Eloquent como "tiene uno de muchos".

Por lo tanto, este compromiso de enviar grandes nuevas características durante la versión actual probablemente conducirá a futuras versiones "mayores" que se utilizarán principalmente para tareas de "mantenimiento", como la actualización de las dependencias de aguas arriba, que se puede ver en estas notas de la versión.

Laravel 9 continúa las mejoras realizadas en Laravel 8.x introduciendo soporte para componentes Symfony 6.0, Symfony Mailer, Flysystem 3.0, salida mejorada `route:list`, un controlador de base de datos Laravel Scout, nueva sintaxis Eloquent accessor / mutator, enlaces de ruta implícitos a través de Enums, y una variedad de otras correcciones de errores y mejoras de usabilidad.

[]()

### PHP 8.0

Laravel 9.x requiere una versión mínima de PHP 8.0.

[]()

### Symfony Mailer

_[Dries Vints](https://github.com/driesvints)_, [James Brooks](https://github.com/jbrooksuk) y [Julius Kiekbusch](https://github.com/Jubeki) han_contribuido al soporte de Symfony Mailer_.

Versiones anteriores de Laravel utilizaban la librería [Swift Mailer](https://swiftmailer.symfony.com/docs/introduction.html) para enviar correo electrónico. Sin embargo, esa librería ya no se mantiene y ha sido reemplazada por Symfony Mailer.

Por favor, revisa la [guía de actualización](/docs/%7B%7Bversion%7D%7D/upgrade#symfony-mailer) para saber más sobre cómo asegurar que tu aplicación es compatible con Symfony Mailer.

[]()

### Flysystem 3.x

La_compatibilidad con Flysystem 3.x fue aportada por [Dries Vints](https://github.com/driesvints)_.

Laravel 9.x actualiza nuestra dependencia de Flysystem a Flysystem 3.x. Flysystem potencia todas las interacciones del sistema de ficheros ofrecidas por la facade `Storage`.

Consulta la [guía](/docs/%7B%7Bversion%7D%7D/upgrade#flysystem-3) de actualización para obtener más información sobre la compatibilidad de tu aplicación con Flysystem 3.x.

[]()

### Accesores / Mutadores Eloquent mejorados

_Eloquent accessors / mutators mejorado fue contribu_ido por_ [Taylor Otwell](https://github.com/taylorotwell)_.

Laravel 9.x ofrece una nueva forma de definir Eloquent accessors [y mutators](/docs/%7B%7Bversion%7D%7D/eloquent-mutators#accessors-and-mutators). En versiones anteriores de Laravel, la única manera de definir accessors y mutators era definiendo métodos prefijados en tu modelo de esta manera:

```php
public function getNameAttribute($value)
{
    return strtoupper($value);
}

public function setNameAttribute($value)
{
    $this->attributes['name'] = $value;
}
```

Sin embargo, en Laravel 9.x puedes definir un accessor y un mutator utilizando un único método no prefijado, indicando un tipo de retorno `Illuminate\Database\Eloquent\Casts\Attribute`:

```php
use Illuminate\Database\Eloquent\Casts\Attribute;

public function name(): Attribute
{
    return new Attribute(
        get: fn ($value) => strtoupper($value),
        set: fn ($value) => $value,
    );
}
```

Además, este nuevo enfoque para la definición de accesores cache los valores de los objetos que son devueltos por el atributo, al igual que las [clases cast personalizadas](/docs/%7B%7Bversion%7D%7D/eloquent-mutators#custom-casts):

```php
use App\Support\Address;
use Illuminate\Database\Eloquent\Casts\Attribute;

public function address(): Attribute
{
    return new Attribute(
        get: fn ($value, $attributes) => new Address(
            $attributes['address_line_one'],
            $attributes['address_line_two'],
        ),
        set: fn (Address $value) => [
            'address_line_one' => $value->lineOne,
            'address_line_two' => $value->lineTwo,
        ],
    );
}
```

[]()

### Atributo Enum Eloquent Casting

> **Advertencia**  
> El casting de Enum sólo está disponible para PHP 8.1+.

_Enum casting fue contribuido por [Mohamed Said](https://github.com/themsaid)_.

Eloquent ahora te permite convertir los valores de tus atributos a [Enums "respaldados" por](https://www.php.net/manual/en/language.enumerations.backed.php) PHP. Para conseguirlo, puedes especificar el atributo y el enum que deseas convertir en el array propiedades `$casts` de tu modelo:

    use App\Enums\ServerStatus;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => ServerStatus::class,
    ];

Una vez que haya definido el reparto en su modelo, el atributo especificado será automáticamente convertido a y desde un enum cuando interactúe con el atributo:

    if ($server->status == ServerStatus::Provisioned) {
        $server->status = ServerStatus::Ready;

        $server->save();
    }

[]()

### Vinculación Implícita de Rutas con Enums

_Implicit Enum bindings fue contribuido por [Nuno Maduro](https://github.com/nunomaduro)_.

PHP 8.1 introduce soporte para [Enums](https://www.php.net/manual/en/language.enumerations.backed.php). Laravel 9.x introduce la capacidad de type-hint un Enum en su definición de ruta y Laravel sólo invocará la ruta si ese segmento de ruta es un valor Enum válido en el URI. En caso contrario, se devolverá automáticamente una respuesta HTTP 404. Por ejemplo, dado el siguiente Enum:

```php
enum Category: string
{
    case Fruits = 'fruits';
    case People = 'people';
}
```

Puede definir una ruta que sólo será invocada si el segmento de ruta `{category}` es `fruits` o `people`. En caso contrario, se devolverá una respuesta HTTP 404:

```php
Route::get('/categories/{category}', function (Category $category) {
    return $category->value;
});
```

[]()

### Alcance forzado de los enlaces de ruta

_Forced scoped bindings fue contribuido por [Claudio Dekker](https://github.com/claudiodekker)_.

En versiones anteriores de Laravel, es posible que desees delimitar el segundo modelo Eloquent en una definición de ruta de tal manera que debe ser hijo del modelo Eloquent anterior. Por ejemplo, considere esta definición de ruta que recupera una entrada de blog por slug para un usuario específico:

    use App\Models\Post;
    use App\Models\User;

    Route::get('/users/{user}/posts/{post:slug}', function (User $user, Post $post) {
        return $post;
    });

Cuando se utiliza un enlace implícito con clave personalizada como parámetro de ruta anidada, Laravel amplía automáticamente la consulta para recuperar el modelo anidado por su padre utilizando convenciones para adivinar el nombre de la relación en el padre. Sin embargo, este comportamiento sólo era soportado anteriormente por Laravel cuando se utilizaba una clave personalizada para el enlace de ruta hijo.

Sin embargo, en Laravel 9.x, ahora puedes ordenar a Laravel que haga scope de los bindings "child" incluso cuando no se proporciona una clave personalizada. Para ello, puedes invocar el método `scopeBindings` al definir tu ruta:

    use App\Models\Post;
    use App\Models\User;

    Route::get('/users/{user}/posts/{post}', function (User $user, Post $post) {
        return $post;
    })->scopeBindings();

También puede indicar a todo un grupo de definiciones de ruta que utilicen enlaces de ámbito:

    Route::scopeBindings()->group(function () {
        Route::get('/users/{user}/posts/{post}', function (User $user, Post $post) {
            return $post;
        });
    });

[]()

### Grupos de rutas de controlador

Las_mejoras en los grupos de rutas han sido aportadas por [Luke Downing](https://github.com/lukeraymonddowning)_.

Ahora puede utilizar el método `controlador` para definir el controlador común para todas las rutas dentro del grupo. Luego, al definir las rutas, sólo es necesario proporcionar el método controlador que invocan:

    use App\Http\Controllers\OrderController;

    Route::controller(OrderController::class)->group(function () {
        Route::get('/orders/{id}', 'show');
        Route::post('/orders', 'store');
    });

[]()

### Índices de Texto Completo / Cláusulas Where

Los_índices de texto completo y las cláusulas "where_" han sido aportados por_ [Taylor Otwell](https://github.com/taylorotwell) y [Dries Vints](https://github.com/driesvints)_.

Cuando se utiliza MySQL o PostgreSQL, ahora se puede añadir el método `fullText` a las definiciones de columna para generar índices de texto completo:

    $table->text('bio')->fullText();

Además, los métodos `whereFullText` y `orWhereFullText` pueden utilizarse para añadir cláusulas "where" de texto completo a una consulta para columnas que tengan índices de [texto](/docs/%7B%7Bversion%7D%7D/migrations#available-index-types) completo. Laravel transformará estos métodos en el SQL apropiado para el sistema de base de datos subyacente. Por ejemplo, se generará una cláusula `MATCH AGAINST` para las aplicaciones que utilicen MySQL:

    $users = DB::table('users')
               ->whereFullText('bio', 'web developer')
               ->get();

[]()

### Motor de base de datos Laravel Scout

_El motor de base de datos Laravel Scout fue contribuido por [Taylor Otwell](https://github.com/taylorotwell) y [Dries Vints](https://github.com/driesvints)_.

Si tu aplicación interactúa con bases de datos pequeñas o medianas o tiene una carga de trabajo ligera, ahora puedes utilizar el motor de "base de datos" de Scout en lugar de un servicio de búsqueda dedicado como Algolia o MeiliSearch. El motor de base de datos utilizará cláusulas "where like" e índices de texto completo al filtrar los resultados de su base de datos existente para determinar los resultados de búsqueda aplicables a su consulta.

Para obtener más información sobre el motor de base de datos de Scout, consulte la [documentación de Scout](/docs/%7B%7Bversion%7D%7D/scout).

[]()

### Renderizado de Plantillas Inline Blade

_Rendering inline Blade templates fue contribuido por [Jason Beggs](https://github.com/jasonlbeggs). [Toby Zerner](https://github.com/tobyzerner)_ ha contribuido a la_creación de componentes Bl_ade en línea.

A veces puede ser necesario transformar una cadena de plantilla Blade sin procesar en HTML válido. Para ello puede utilizar el método `render` proporcionado por la facade `Blade`. El método `render` acepta la cadena de la plantilla Blade y un array opcional de datos para proporcionar a la plantilla:

```php
use Illuminate\Support\Facades\Blade;

return Blade::render('Hello, {{ $name }}', ['name' => 'Julian Bashir']);
```

De forma similar, el método `renderComponent` puede utilizarse para renderizar un componente de clase determinado pasando la instancia del componente al método:

```php
use App\View\Components\HelloComponent;

return Blade::renderComponent(new HelloComponent('Julian Bashir'));
```

[]()

### Atajo de nombre de ranura

_[Caleb Porzio](https://github.com/calebporzio) ha contribuido a los atajos de nombres de ranuras._

En versiones anteriores de Laravel, los nombres de las ranuras se proporcionaban utilizando un atributo `name` en la etiqueta `x-slot`:

```blade
<x-alert>
    <x-slot name="title">
        Server Error
    </x-slot>

    <strong>Whoops!</strong> Something went wrong!
</x-alert>
```

Sin embargo, a partir de Laravel 9.x, puede especificar el nombre de la ranura utilizando una sintaxis conveniente y más corta:

```xml
<x-slot:title>
    Server Error
</x-slot>
```

[]()

### Directivas de Hoja Comprobadas / Seleccionadas

Las_directivas Blade comprobadas y seleccionadas_ han sido creadas por_ [Ash Allen](https://github.com/ash-jc-allen) y [Taylor Otwell](https://github.com/taylorotwell)_.

Para mayor comodidad, ahora puede utilizar la directiva `@checked` para indicar fácilmente si una entrada de casilla de verificación HTML está "marcada". Esta directiva hará eco de " `checked"` si la condición proporcionada se evalúa como `verdadera`:

```blade
<input type="checkbox"
        name="active"
        value="active"
        @checked(old('active', $user->active)) />
```

Del mismo modo, la directiva `@selected` puede utilizarse para indicar si una opción de selección dada debe estar "seleccionada":

```blade
<select name="version">
    @foreach ($product->versions as $version)
        <option value="{{ $version }}" @selected(old('version') == $version)>
            {{ $version }}
        </option>
    @endforeach
</select>
```

[]()

### Vistas de paginación de Bootstrap 5

Las vistas de paginación de_Bootstrap 5 fueron aportadas por [Jared Lewis](https://github.com/jrd-lewis)_.

Laravel ahora incluye vistas de paginación construidas usando [Bootstrap 5](https://getbootstrap.com/). Para utilizar estas vistas en lugar de las vistas por defecto de Tailwind, puede llamar al método `useBootstrapFive` del paginador dentro del método `boot` de su clase `App\Providers\AppServiceProvider`:

    use Illuminate\Pagination\Paginator;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFive();
    }

[array-data">]()

### Validación mejorada de datos de array anidadas

_Mejora de la validación de las entradas de array anidada fue contribuido por [Steve Bauman](https://github.com/stevebauman)_.

A veces puede ser necesario acceder al valor de un elemento de array anidada dado al asignar reglas de validación al atributo. Ahora puede hacerlo utilizando el método `Rule::forEach`. El método `forEach` acepta un closure que se invocará para cada iteración del atributo array array bajo validación y recibirá el valor del atributo y el nombre explícito y completamente expandido del atributo. El closure debe devolver un array de reglas para asignar al elemento del array:

    use App\Rules\HasPermission;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Validation\Rule;

    $validator = Validator::make($request->all(), [
        'companies.*.id' => Rule::forEach(function ($value, $attribute) {
            return [
                Rule::exists(Company::class, 'id'),
                new HasPermission('manage-company', $value),
            ];
        }),
    ]);

[]()

### API Laravel Breeze y Next.js

_El andamiaje de la API Laravel Breeze y el kit de inicio Next.js_ han sido creados_por [Taylor Otwell](https://github.com/taylorotwell) y [Miguel Piedrafita](https://twitter.com/m1guelpf)_.

El kit de inicio de [Laravel](/docs/%7B%7Bversion%7D%7D/starter-kits#breeze-and-next) Breeze ha recibido un modo de andamiaje "API" y una [implementación frontend](https://github.com/laravel/breeze-next) Next. [js](https://nextjs.org) complementaria. Este kit de inicio de andamiaje puede ser utilizado para poner en marcha sus aplicaciones Laravel que están sirviendo como un backend, Laravel Sanctum autenticado API para un frontend JavaScript.

[]()

### Página de excepciones de Ignition mejorada

_Ignition está desarrollado por [Spatie](https://spatie.be/)._

Ignition, la página de depuración de excepciones de código abierto creada por Spatie, ha sido rediseñada desde cero. La nueva y mejorada Ignition se entrega con Laravel 9.x e incluye temas claros / oscuros, funcionalidad personalizable "abrir en editor", y mucho más.

<p align="center">
<img width="100%" src="https://user-images.githubusercontent.com/483853/149235404-f7caba56-ebdf-499e-9883-cac5d5610369.png"/>
</p>

[]()

### Mejora de la salida CLI de route: `list`

La_mejora de `route:list` CLI output fue contribuida por [Nuno Maduro](https://github.com/nunomaduro)_.

La salida CLI de route `:` list ha sido significativamente mejorada para la versión Laravel 9.x, ofreciendo una nueva y hermosa experiencia al explorar sus definiciones de ruta.

<p align="center">
<img src="https://user-images.githubusercontent.com/5457236/148321982-38c8b869-f188-4f42-a3cc-a03451d5216c.png"/>
</p>

[test-Command">]()

### Cobertura detest mediante el comando Artisan `test`

La_cobertura detest prueba cuando se utiliza el comando de `test` Artisan_ fue contribuido por_ [Nuno](https://github.com/nunomaduro)__ [](https://github.com/nunomaduro)_[Maduro.](https://github.com/nunomaduro)

El comando Artisan `test` ha recibido una nueva opción `--coverage` que puedes utilizar para explorar la cantidad de cobertura de código que tus tests están proporcionando a tu aplicación:

```shell
php artisan test --coverage
```

Los resultados de la cobertura de la test serán mostrados directamente dentro de la salida CLI.

<p align="center">
<img width="100%" src="https://user-images.githubusercontent.com/5457236/150133237-440290c2-3538-4d8e-8eac-4fdd5ec7bd9e.png"/>
</p>

Además, si desea especificar un umbral mínimo que debe cumplir el porcentaje de cobertura de las test, puede utilizar la opción `--min`. El conjunto de test fallará si no se alcanza el umbral mínimo especificado:

```shell
php artisan test --coverage --min=80.3
```

<p align="center">
<img width="100%" src="https://user-images.githubusercontent.com/5457236/149989853-a29a7629-2bfa-4bf3-bbf7-cdba339ec157.png"/>
</p>

[]()

### Servidor Eco Soketi

_El servidor Soketi Echo fue desarrollado_ por_ [Alex Renoki](https://github.com/rennokki)_.

Aunque no es exclusivo de Laravel 9.x, Laravel ha ayudado recientemente con la documentación de Soketi, un servidor Web Socket compatible con [Laravel Echo](/docs/%7B%7Bversion%7D%7D/broadcasting) escrito para Node.js. Soketi proporciona una gran alternativa de código abierto a Pusher y Ably para aquellas aplicaciones que prefieren gestionar su propio servidor Web Socket.

Para obtener más información sobre el uso de Soketi, consulte la [documentación de difusión](/docs/%7B%7Bversion%7D%7D/broadcasting) y [la documentación de Soketi](https://docs.soketi.app/).

[]()

### Soporte mejorado de Collections IDE

La_mejora del soporte de colecciones IDE ha sido aportada por [Nuno Maduro](https://github.com/nunomaduro)_.

Laravel 9.x añade definiciones de tipo mejoradas y de estilo "genérico" al componente collections, mejorando el soporte IDE y el análisis estático. IDEs como [PHPStorm](https://blog.jetbrains.com/phpstorm/2021/12/phpstorm-2021-3-release/#support_for_future_laravel_collections) o herramientas de análisis estático como [PHPStan](https://phpstan.org) ahora entenderán mejor las colecciones de Laravel de forma nativa.

<p align="center">
<img width="100%" src="https://user-images.githubusercontent.com/5457236/151783350-ed301660-1e09-44c1-b549-85c6db3f078d.gif"/>
</p>

[]()

### Nuevos ayudantes

Laravel 9.x introduce dos nuevas y prácticas funciones de ayuda que puede utilizar en su propia aplicación.

[]()

#### `str`

La función `str` devuelve una nueva instancia `Illuminate\Support\Stringable` para la cadena dada. Esta función es equivalente al método `Str::of`:

    $string = str('Taylor')->append(' Otwell');

    // 'Taylor Otwell'

Si no se proporciona ningún argumento a la función `str`, la función devuelve una instancia de `Illuminate\Support\Str`:

    $snake = str()->snake('LaravelFramework');

    // 'laravel_framework'

[]()

#### `to_route`

La función `to_route` genera una respuesta HTTP de redirección para una ruta con nombre dada, proporcionando una forma expresiva de redirigir a rutas con nombre desde tus rutas y controladores:

    return to_route('users.show', ['user' => 1]);

Si es necesario, puede pasar el código de estado HTTP que debería asignarse a la redirección y cualquier cabecera de respuesta adicional como tercer y cuarto argumento al método to_route:

    return to_route('users.show', ['user' => 1], 302, ['X-Framework' => 'Laravel']);
