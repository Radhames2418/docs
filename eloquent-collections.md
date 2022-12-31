# Eloquent: Colecciones

- [Introducción](#introduction)
- [Métodos disponibles](#available-methods)
- [Colecciones personalizadas](#custom-collections)

[]()

## Introducción

Todos los métodos de Eloquent que devuelven más de un resultado de modelo devolverán instancias de la clase `Illuminate\Database\Eloquent\Collection`, incluidos los resultados recuperados mediante el método `get` o a los que se accede mediante una relación. El objeto de colección Eloquent extiende la [colección base](/docs/%7B%7Bversion%7D%7D/collections) de Laravel, por lo que hereda de forma natural docenas de métodos utilizados para trabajar de forma fluida con la array subyacente de modelos Eloquent. Asegúrate de revisar la documentación de la colección de Laravel para aprender todo sobre estos útiles métodos.

Todas las colecciones sirven también como iteradores, permitiéndote hacer bucles sobre ellas como si fueran simples arrays PHP:

    use App\Models\User;

    $users = User::where('active', 1)->get();

    foreach ($users as $user) {
        echo $user->name;
    }

Sin embargo, como se mencionó anteriormente, las colecciones son mucho más poderosas que los arrays y exponen una variedad de operaciones de mapeo / reducción que pueden ser encadenadas usando una interfaz intuitiva. Por ejemplo, podemos eliminar todos los modelos inactivos y luego recopilar el nombre de pila de cada usuario restante:

    $names = User::all()->reject(function ($user) {
        return $user->active === false;
    })->map(function ($user) {
        return $user->name;
    });

[]()

#### Conversión de colecciones Eloquent

Mientras que la mayoría de los métodos de colección Eloquent devuelven una nueva instancia de una colección Eloquent, los métodos `collapse`, `flatten`, `flip`, `keys`, `pluck` y `zip` devuelven una instancia de [colección base](/docs/%7B%7Bversion%7D%7D/collections). Del mismo modo, si una operación `map` devuelve una colección que no contiene ningún modelo Eloquent, se convertirá en una instancia de colección base.

[]()

## Métodos disponibles

Todas las colecciones Eloquent extienden el objeto de colección base [de Laravel](/docs/%7B%7Bversion%7D%7D/collections#available-methods); por lo tanto, heredan todos los potentes métodos proporcionados por la clase de colección base.

Además, la clase `Illuminate\Database\Eloquent\Collection` proporciona un superconjunto de métodos para ayudar con la gestión de sus colecciones modelo. La mayoría de los métodos devuelven instancias `Illuminate\Database\Eloquent\Collection`; sin embargo, algunos métodos, como `modelKeys`, devuelven una instancia `Illuminate\Support\Collection`.

<style>
    .collection-method-list &gt; p {
        columnas: 14.4em 1; -moz-columns: 14.4em 1; -webkit-columns: 14.4em 1;
    }

    .collection-method-list a {
        display: block;
        overflow: oculto;
        text-overflow: ellipsis;
        espacio en blanco: nowrap;
    }

    .collection-method code {
        font-size: 14px;
    }

    .collection-method:not(.first-collection-method) {
        margin-top: 50px;
    }
</style>

<div class="collection-method-list" markdown="1"/>

[appendcontainsdiffexceptfindfreshintersectloadloadMissingmodelKeysmakeVisiblemakeHiddenonlytoQueryunique](#method-append)

[object Object]

[]()

#### `append($atributos)` {.método-colección .método-primera-colección}

El método `append` puede utilizarse para indicar que se [añada](/docs/%7B%7Bversion%7D%7D/eloquent-serialization#appending-values-to-json) un atributo a cada modelo de la colección. Este método acepta una array de atributos o un único atributo:

    $users->append('team');

    $users->append(['team', 'is_admin']);

[]()

#### `contains($clave, $operador = null, $valor = null)` {.método-colección}

El método `contains` puede utilizarse para determinar si una instancia de modelo dada está contenida en la colección. Este método acepta una clave primaria o una instancia del modelo:

    $users->contains(1);

    $users->contains(User::find(1));

[]()

#### `diff($items)` {.método-colección}

El método `diff` devuelve todos los modelos que no están presentes en la colección dada:

    use App\Models\User;

    $users = $users->diff(User::whereIn('id', [1, 2, 3])->get());

[]()

#### `except($claves)` {.método-colección}

El método `except` devuelve todos los modelos que no tienen las claves primarias dadas:

    $users = $users->except([1, 2, 3]);

[]()

#### `find($clave)` {.método-colección}

El método `find` devuelve el modelo que tiene una clave primaria que coincide con la clave dada. Si `$clave` es una instancia de modelo, `find` intentará devolver un modelo que coincida con la clave primaria. Si `$key` es un array de claves, `find` devolverá todos los modelos que tengan una clave primaria en el array dado:

    $users = User::all();

    $user = $users->find(1);

[]()

#### `fresh($con = [])` {.método-colección}

El método `fresh` recupera de la base de datos una instancia nueva de cada modelo de la colección. Además, las relaciones especificadas se cargarán de forma inmediata:

    $users = $users->fresh();

    $users = $users->fresh('comments');

[]()

#### `intersect($elementos)` {.método-colección}

El método `intersect` devuelve todos los modelos que también están presentes en la colección dada:

    use App\Models\User;

    $users = $users->intersect(User::whereIn('id', [1, 2, 3])->get());

[]()

#### `load($relaciones)` {.método-colección}

El método `load` carga las relaciones de todos los modelos de la colección:

    $users->load(['comments', 'posts']);

    $users->load('comments.author');

    $users->load(['comments', 'posts' => fn ($query) => $query->where('active', 1)]);

[]()

#### `loadMissing($relations)` {.método-colección}

El método `loadMissing` carga las relaciones dadas para todos los modelos de la colección si las relaciones no están ya cargadas:

    $users->loadMissing(['comments', 'posts']);

    $users->loadMissing('comments.author');

    $users->loadMissing(['comments', 'posts' => fn ($query) => $query->where('active', 1)]);

[]()

#### `modelKeys()` {.método-colección}

El método `modelKeys` devuelve las claves primarias de todos los modelos de la colección:

    $users->modelKeys();

    // [1, 2, 3, 4, 5]

[]()

#### `makeVisible($atributos)` {.método-colección}

El método `makeVisible` [hace visibles](/docs/%7B%7Bversion%7D%7D/eloquent-serialization#hiding-attributes-from-json) los atributos que normalmente están "ocultos" en cada modelo de la colección:

    $users = $users->makeVisible(['address', 'phone_number']);

[]()

#### `makeHidden($atributos)` {.método-colección}

El método `makeHidden` [oculta](/docs/%7B%7Bversion%7D%7D/eloquent-serialization#hiding-attributes-from-json) atributos que normalmente están "visibles" en cada modelo de la colección:

    $users = $users->makeHidden(['address', 'phone_number']);

[]()

#### only(\$claves`)` {.método-colección}

El método `only` devuelve todos los modelos que tienen las claves primarias dadas:

    $users = $users->only([1, 2, 3]);

[]()

#### `toQuery()` {.método-colección}

El método `toQuery` devuelve una instancia del constructor de consultas Eloquent que contiene una restricción `whereIn` sobre las claves primarias del modelo de colección:

    use App\Models\User;

    $users = User::where('status', 'VIP')->get();

    $users->toQuery()->update([
        'status' => 'Administrator',
    ]);

[]()

#### `unique($key = null, $strict = false)` {.método-colección}

El método `unique` devuelve todos los modelos únicos de la colección. Se eliminan todos los modelos del mismo tipo con la misma clave primaria que otro modelo de la colección:

    $users = $users->unique();

[]()

## Colecciones personalizadas

Si desea utilizar un objeto `Collection` personalizado al interactuar con un modelo determinado, puede definir un método `newCollection` en su modelo:

    <?php

    namespace App\Models;

    use App\Support\UserCollection;
    use Illuminate\Database\Eloquent\Model;

    class User extends Model
    {
        /**
         * Create a new Eloquent Collection instance.
         *
         * @param  array  $models
         * @return \Illuminate\Database\Eloquent\Collection
         */
        public function newCollection(array $models = [])
        {
            return new UserCollection($models);
        }
    }

Una vez que haya definido un método `newCollection`, recibirá una instancia de su colección personalizada en cualquier momento en que Eloquent devolvería normalmente una instancia `Illuminate\Database\Eloquent\Collection`. Si desea utilizar una colección personalizada para cada modelo de su aplicación, debe definir el método `newCollection` en una clase modelo base que sea extendida por todos los modelos de su aplicación.
