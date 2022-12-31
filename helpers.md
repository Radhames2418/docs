# Ayudantes

- [Introducción](#introduction)
- [Métodos disponibles](#available-methods)
- [Otras utilidades](#other-utilities)
  - [Evaluación comparativa](#benchmarking)
  - [Lotería](#lottery)

[]()

## Introducción

Laravel incluye una variedad de funciones PHP globales de "ayuda". Muchas de estas funciones son usadas por el propio framework; sin embargo, eres libre de usarlas en tus propias aplicaciones si las encuentras convenientes.

[]()

## Métodos disponibles

<style>
    .collection-method-list &gt; p {
        columnas: 10.8em 3; -moz-columns: 10.8em 3; -webkit-columns: 10.8em 3;
    }

    .collection-method-list a {
        display: block;
        overflow: oculto;
        text-overflow: ellipsis;
        espacio en blanco: nowrap;
    }
</style>

[]()

### Matrices y objetos

<div class="collection-method-list" markdown="1"/>

[array-accessible">Arr::accessibleArr](<#method-\<glossary variable=>)[array-add">::addArr](<#method-\<glossary variable=>)[array-collapse">::collapseArr](<#method-\<glossary variable=>)[array-crossjoin">::crossJoinArr](<#method-\<glossary variable=>)[array-divide">::divideArr](<#method-\<glossary variable=>)[array-dot">::dotArr](<#method-\<glossary variable=>)[array-except">::exceptArr](<#method-\<glossary variable=>)[array-exists">::existsArr](<#method-\<glossary variable=>)[array-first">::firstArr](<#method-\<glossary variable=>)[array-flatten">::flattenArr](<#method-\<glossary variable=>)[array-forget">::forgetArr](<#method-\<glossary variable=>)[array-get">::getArr](<#method-\<glossary variable=>)[array-has">::hasArr](<#method-\<glossary variable=>)[array-hasany">::hasAnyArr](<#method-\<glossary variable=>)[array-isassoc">::isAssocArr](<#method-\<glossary variable=>)[array-islist">::isListArr](<#method-\<glossary variable=>)[array-join">::joinArr](<#method-\<glossary variable=>)[array-keyby">::keyByArr](<#method-\<glossary variable=>)[array-last">::lastArr](<#method-\<glossary variable=>)[array-map">::mapArr](<#method-\<glossary variable=>)[array-only">::onlyArr](<#method-\<glossary variable=>)[array-pluck">::pluckArr](<#method-\<glossary variable=>)[array-prepend">::prependArr](<#method-\<glossary variable=>)[array-prependkeyswith">::prependKeysWithArr](<#method-\<glossary variable=>)[array-pull">::pullArr](<#method-\<glossary variable=>)[array-query">::queryArr](<#method-\<glossary variable=>)[array-random">::randomArr](<#method-\<glossary variable=>)[array-set">::setArr](<#method-\<glossary variable=>)[array-shuffle">::shuffleArr](<#method-\<glossary variable=>)[array-sort">::sortArr](<#method-\<glossary variable=>)[array-sort-recursive">::sortRecursiveArr](<#method-\<glossary variable=>)[array-to-css-classes">::toCssClassesArr](<#method-\<glossary variable=>)[array-undot">::undotArr](<#method-\<glossary variable=>)[array-where">::whereArr](<#method-\<glossary variable=>)[array-where-not-null">::whereNotNullArr](<#method-\<glossary variable=>)[array-wrap">::wrapdata_filldata_getdata_setheadlast](<#method-\<glossary variable=>)

[object Object]

[]()

### Rutas

<div class="collection-method-list" markdown="1"/>

[app_pathbase_pathconfig_pathdatabase_pathlang_pathmixpublic_pathresource_pathstorage_path](#method-app-path)

[object Object]

[]()

### Cadenas

<div class="collection-method-list" markdown="1"/>

[\__class_basenameepreg_replace_arrayStr](#method-\__)[::afterStr](#method-str-after)[::afterLastStr](#method-str-after-last)[::asciiStr](#method-str-ascii)[::beforeStr](#method-str-before)[::beforeLastStr](#method-str-before-last)[::betweenStr](#method-str-between)[::betweenFirstStr](#method-str-between-first)[::camelStr](#method-camel-case)[::containsStr](#method-str-contains)[::containsAllStr](#method-str-contains-all)[::endsWithStr](#method-ends-with)[::excerptStr](#method-excerpt)[::finishStr](#method-str-finish)::[headlineStr](#method-str-headline):[:inlineMarkdownStr](#method-str-inline-markdown)[::isStr](#method-str-is)[::isAsciiStr](#method-str-is-ascii)[::isJsonStr](#method-str-is-json)[::](#method-str-is-ulid)[isUlidStr](#method-str-ordered-uuid):[:isUuidStr](#method-str-is-uuid)[::kebabStr](#method-kebab-case)[::lcfirstStr](#method-str-lcfirst)[::lengthStr](#method-str-length)[::limitStr](#method-str-limit)[::lowerStr](#method-str-lower)[::markdownStr](#method-str-markdown)[::maskStr](#method-str-mask)[::orderedUuidStr](#method-str-ordered-uuid)[::padBothStr](#method-str-padboth)[::padLeftStr](#method-str-padleft)[::padRightStr](#method-str-padright)[::pluralStr](#method-str-plural)[::pluralStudlyStr](#method-str-plural-studly)[::randomStr](#method-str-random)[::removeStr](#method-str-remove)[::replaceStr](#method-str-replace)[::replaceArrayStr](#method-str-replace-array)[::replaceFirstStr](#method-str-replace-first)[::replaceLastStr](#method-str-replace-last)[::reverseStr](#method-str-reverse)[::singularStr](#method-str-singular)[::slugStr](#method-str-slug)[::snakeStr](#method-snake-case)[::squishStr](#method-str-squish)[::startStr](#method-str-start)[::startsWithStr](#method-starts-with)::[studlyStr](#method-studly-case)[::substrStr](#method-str-substr)[::substrCountStr](#method-str-substrcount)[::substrReplaceStr](#method-str-substrreplace)[::swapStr](#method-str-swap)[::titleStr](#method-title-case)[::toHtmlStringStr](#method-str-to-html-string)[::ucfirstStr](#method-str-ucfirst)[::ucsplitStr](#method-str-ucsplit)[::upperStr](#method-str-upper)[::ulidStr](#method-str-ulid)[::uuidStr](#method-str-uuid)[::wordCountStr](#method-str-word-count)[::wordsstrtranstrans_choice](#method-str-words)

[object Object]

[]()

### Cadenas fluidas

<div class="collection-method-list" markdown="1"/>

[afterafterLastappendasciibasenamebeforebeforeLastbetweenbetweenFirstcamelclassBasenamecontainscontainsAlldirnameendsWithexcerptexactlyexplodefinishheadlineinlineMarkdownisisAsciiisEmptyisNotEmptyisJsonisUlidisUuidkebablcfirstlengthlimitlowerltrimmarkdownmaskmatchmatchAllnewLinepadBothpadLeftpadRightpipepluralprependremovereplacereplaceArrayreplaceFirstreplaceLastreplaceMatchesrtrimscansingularslugsnakesplitsquishstartstartsWithstudlysubstrsubstrReplaceswaptaptesttitletrimucfirstucsplitupperwhenwhenContainswhenContainsAllwhenEmptywhenNotEmptywhenStartsWithwhenEndsWithwhenExactlywhenNotExactlywhenIswhenIsAsciiwhenIsUlidwhenIsUuidwhenTestwordCountwords](#method-fluent-str-words)

[object Object]

[]()

### URLs

<div class="collection-method-list" markdown="1"/>

[actionassetroutesecure_assetsecure_urlto_routeurl](#method-action)

[object Object]

[]()

### Varios

<div class="collection-method-list" markdown="1"/>

[abortabort_ifabort_unlessappauthbackbcryptblankbroadcastcacheclass_uses_recursivecollectconfigcookiecsrf_fieldcsrf_tokendecryptdddispatchdumpencryptenveventfakefilledinfologgermethod_fieldnowoldoptionalpolicyredirectreportreport_ifreport_unlessrequestrescueresolveresponseretrysessiontapthrow_ifthrow_unlesstodaytrait_uses_recursivetransformvalidatorvalueviewwith](#method-abort)

[object Object]

[]()

## Listado de métodos

<style>
    .collection-method código {
        font-size: 14px;
    }

    .collection-method:not(.first-collection-method) {
        margin-top: 50px;
    }
</style>

[]()

## Matrices y objetos

[array-accessible">]()

#### Arr`::accessible()` {.método-colección .método-primera-colección}

El método Arr `:`:accessible determina si el valor dado es array accesible:

    use Illuminate\Support\Arr;
    use Illuminate\Support\Collection;

    $isAccessible = Arr::accessible(['a' => 1, 'b' => 2]);

    // true

    $isAccessible = Arr::accessible(new Collection);

    // true

    $isAccessible = Arr::accessible('abc');

    // false

    $isAccessible = Arr::accessible(new stdClass);

    // false

[array-add">]()

#### Arr`::add()` {.método-colección}

El método Arr:: `add` añade un par clave / valor dado a un array si la clave dada no existe ya en el array o es `nula`:

    use Illuminate\Support\Arr;

    $array = Arr::add(['name' => 'Desk'], 'price', 100);

    // ['name' => 'Desk', 'price' => 100]

    $array = Arr::add(['name' => 'Desk', 'price' => null], 'price', 100);

    // ['name' => 'Desk', 'price' => 100]

[array-collapse">]()

#### Arr::`collapse()` {.método-colección}

El método Arr `::coll` apse contrae una array de matrices en una única array:

    use Illuminate\Support\Arr;

    $array = Arr::collapse([[1, 2, 3], [4, 5, 6], [7, 8, 9]]);

    // [1, 2, 3, 4, 5, 6, 7, 8, 9]

[array-crossjoin">]()

#### Arr::`crossJoin()` {.método-colección}

El método Arr `::crossJoin` cruza las matrices dadas, devolviendo un producto cartesiano con todas las permutaciones posibles:

    use Illuminate\Support\Arr;

    $matrix = Arr::crossJoin([1, 2], ['a', 'b']);

    /*
        [
            [1, 'a'],
            [1, 'b'],
            [2, 'a'],
            [2, 'b'],
        ]
    */

    $matrix = Arr::crossJoin([1, 2], ['a', 'b'], ['I', 'II']);

    /*
        [
            [1, 'a', 'I'],
            [1, 'a', 'II'],
            [1, 'b', 'I'],
            [1, 'b', 'II'],
            [2, 'a', 'I'],
            [2, 'a', 'II'],
            [2, 'b', 'I'],
            [2, 'b', 'II'],
        ]
    */

[array-divide">]()

#### Arr`::divide()` {.método-colección}

El método Arr `::` divide devuelve dos matrices: una con las claves y otra con los valores de la array dada:

    use Illuminate\Support\Arr;

    [$keys, $values] = Arr::divide(['name' => 'Desk']);

    // $keys: ['name']

    // $values: ['Desk']

[array-dot">]()

#### Arr`:`:dot() {.método-colección}

El método Arr `::dot` aplana una array multidimensional en una array de un solo nivel que utiliza la notación "dot" para indicar la profundidad:

    use Illuminate\Support\Arr;

    $array = ['products' => ['desk' => ['price' => 100]]];

    $flattened = Arr::dot($array);

    // ['products.desk.price' => 100]

[array-except">]()

#### Arr::except(`)` {.método-colección}

El método Arr: `:` except elimina de una array los pares clave/valor dados:

    use Illuminate\Support\Arr;

    $array = ['name' => 'Desk', 'price' => 100];

    $filtered = Arr::except($array, ['price']);

    // ['name' => 'Desk']

[array-exists">]()

#### Arr::exists(`)` {.método-colección}

El método Arr `::exists` comprueba que la clave dada existe en el array proporcionado:

    use Illuminate\Support\Arr;

    $array = ['name' => 'John Doe', 'age' => 17];

    $exists = Arr::exists($array, 'name');

    // true

    $exists = Arr::exists($array, 'salary');

    // false

[array-first">]()

#### Arr`::first()` {.método-colección}

El método Arr:: `first` devuelve el primer elemento de una array que supera una test dada:

    use Illuminate\Support\Arr;

    $array = [100, 200, 300];

    $first = Arr::first($array, function ($value, $key) {
        return $value >= 150;
    });

    // 200

También se puede pasar un valor por defecto como tercer parámetro del método. Este valor se devolverá si ningún valor supera la test de verdad:

    use Illuminate\Support\Arr;

    $first = Arr::first($array, $callback, $default);

[array-flatten">]()

#### `Arr::aplanar()` {.método-colección}

El método Arr:: `flatten` transforma una array multidimensional en una array de un solo nivel:

    use Illuminate\Support\Arr;

    $array = ['name' => 'Joe', 'languages' => ['PHP', 'Ruby']];

    $flattened = Arr::flatten($array);

    // ['Joe', 'PHP', 'Ruby']

[array-forget">]()

#### Arr::`forget` () {.método-colección}

El método Arr:: `forget` elimina un par clave/valor dado de una array anidada en profundidad utilizando la notación "dot":

    use Illuminate\Support\Arr;

    $array = ['products' => ['desk' => ['price' => 100]]];

    Arr::forget($array, 'products.desk');

    // ['products' => []]

[array-get">]()

#### Arr`::get()` {.método-colección}

El método Arr: `:get` recupera un valor de una array anidada utilizando la notación "dot":

    use Illuminate\Support\Arr;

    $array = ['products' => ['desk' => ['price' => 100]]];

    $price = Arr::get($array, 'products.desk.price');

    // 100

El método Arr `::get` también acepta un valor por defecto, que será devuelto si la clave especificada no está presente en el array:

    use Illuminate\Support\Arr;

    $discount = Arr::get($array, 'products.desk.discount', 0);

    // 0

[array-has">]()

#### Arr`::has()` {.método-colección}

El método Arr: `:has` comprueba si uno o varios elementos existen en una array utilizando la notación "dot":

    use Illuminate\Support\Arr;

    $array = ['product' => ['name' => 'Desk', 'price' => 100]];

    $contains = Arr::has($array, 'product.name');

    // true

    $contains = Arr::has($array, ['product.price', 'product.discount']);

    // false

[array-hasany">]()

#### Arr::`hasAny()` {.método-colección}

El método Arr:: `hasAny` comprueba si algún elemento de un conjunto dado existe en una array utilizando la notación "punto":

    use Illuminate\Support\Arr;

    $array = ['product' => ['name' => 'Desk', 'price' => 100]];

    $contains = Arr::hasAny($array, 'product.name');

    // true

    $contains = Arr::hasAny($array, ['product.name', 'product.discount']);

    // true

    $contains = Arr::hasAny($array, ['category', 'product.discount']);

    // false

[array-isassoc">]()

#### Arr`::isAssoc` () {.método-colección}

El método Arr `::isAssoc` devuelve `verdadero` si la array dada es una array asociativa. Una array se considera "asociativa" si no tiene claves numéricas secuenciales que empiecen por cero:

    use Illuminate\Support\Arr;

    $isAssoc = Arr::isAssoc(['product' => ['name' => 'Desk', 'price' => 100]]);

    // true

    $isAssoc = Arr::isAssoc([1, 2, 3]);

    // false

[array-islist">]()

#### Arr:`:isList` () {.método-colección}

El método Arr `::is` List devuelve `verdadero` si las claves de la array dada son enteros secuenciales empezando por cero:

    use Illuminate\Support\Arr;

    $isList = Arr::isList(['foo', 'bar', 'baz']);

    // true

    $isList = Arr::isList(['product' => ['name' => 'Desk', 'price' => 100]]);

    // false

[array-join">]()

#### Arr::join(`)` {.método-colección}

El método Arr: `:` join une los elementos de array con una cadena. Utilizando el segundo argumento de este método, también puede especificar la cadena de unión para el elemento final de la array:

    use Illuminate\Support\Arr;

    $array = ['Tailwind', 'Alpine', 'Laravel', 'Livewire'];

    $joined = Arr::join($array, ', ');

    // Tailwind, Alpine, Laravel, Livewire

    $joined = Arr::join($array, ', ', ' and ');

    // Tailwind, Alpine, Laravel and Livewire

[array-keyby">]()

#### Arr`::keyBy()` {.método-colección}

El método `Arr:`:keyBy ordena la array por la clave dada. Si varios elementos tienen la misma clave, sólo el último aparecerá en la nueva array:

    use Illuminate\Support\Arr;

    $array = [
        ['product_id' => 'prod-100', 'name' => 'Desk'],
        ['product_id' => 'prod-200', 'name' => 'Chair'],
    ];

    $keyed = Arr::keyBy($array, 'product_id');

    /*
        [
            'prod-100' => ['product_id' => 'prod-100', 'name' => 'Desk'],
            'prod-200' => ['product_id' => 'prod-200', 'name' => 'Chair'],
        ]
    */

[array-last">]()

#### Arr`::last()` {.método-colección}

El método Arr `::last` devuelve el último elemento de una array que pasa un test verdad dado:

    use Illuminate\Support\Arr;

    $array = [100, 200, 300, 110];

    $last = Arr::last($array, function ($value, $key) {
        return $value >= 150;
    });

    // 300

Se puede pasar un valor por defecto como tercer argumento del método. Este valor se devolverá si ningún valor supera la test de verdad:

    use Illuminate\Support\Arr;

    $last = Arr::last($array, $callback, $default);

[array-map">]()

#### Arr`::map()` {.método-colección}

El método Arr `::` map recorre la array y pasa cada valor y clave a la llamada de retorno dada. El valor de array se sustituye por el valor devuelto por la llamada de retorno:

    use Illuminate\Support\Arr;

    $array = ['first' => 'james', 'last' => 'kirk'];

    $mapped = Arr::map($array, function ($value, $key) {
        return ucfirst($value);
    });

    // ['first' => 'James', 'last' => 'Kirk']

[array-only">]()

#### Arr::`only()` {.método-colección}

El método Arr `::only` devuelve sólo los pares clave/valor especificados de la array dada:

    use Illuminate\Support\Arr;

    $array = ['name' => 'Desk', 'price' => 100, 'orders' => 10];

    $slice = Arr::only($array, ['name', 'price']);

    // ['name' => 'Desk', 'price' => 100]

[array-pluck">]()

#### Arr`::pluck()` {.método-colección}

El método Arr `:`:pluck recupera todos los valores de una clave dada de una array:

    use Illuminate\Support\Arr;

    $array = [
        ['developer' => ['id' => 1, 'name' => 'Taylor']],
        ['developer' => ['id' => 2, 'name' => 'Abigail']],
    ];

    $names = Arr::pluck($array, 'developer.name');

    // ['Taylor', 'Abigail']

También puede especificar cómo desea que sea la clave de la lista resultante:

    use Illuminate\Support\Arr;

    $names = Arr::pluck($array, 'developer.name', 'developer.id');

    // [1 => 'Taylor', 2 => 'Abigail']

[array-prepend">]()

#### Arr`::prepend()` {.método-colección}

El método Arr `::pre` pend coloca un elemento al principio de una array:

    use Illuminate\Support\Arr;

    $array = ['one', 'two', 'three', 'four'];

    $array = Arr::prepend($array, 'zero');

    // ['zero', 'one', 'two', 'three', 'four']

Si es necesario, puede especificar la clave que se utilizará para el valor:

    use Illuminate\Support\Arr;

    $array = ['price' => 100];

    $array = Arr::prepend($array, 'Desk', 'name');

    // ['name' => 'Desk', 'price' => 100]

[array-prependkeyswith">]()

#### Arr`::prependKeysWith()` {.método-colección}

Arr `::pre` pendKeysWith antepone a todos los nombres de las claves de una array asociativa el prefijo dado:

    use Illuminate\Support\Arr;

    $array = [
        'name' => 'Desk',
        'price' => 100,
    ];

    $keyed = Arr::prependKeysWith($array, 'product.');

    /*
        [
            'product.name' => 'Desk',
            'product.price' => 100,
        ]
    */

[array-pull">]()

#### Arr::pull(`)` {.método-colección}

El método Arr `::` pull devuelve y elimina un par clave/valor de una array:

    use Illuminate\Support\Arr;

    $array = ['name' => 'Desk', 'price' => 100];

    $name = Arr::pull($array, 'name');

    // $name: Desk

    // $array: ['price' => 100]

Se puede pasar un valor por defecto como tercer argumento del método. Este valor se devolverá si la clave no existe:

    use Illuminate\Support\Arr;

    $value = Arr::pull($array, $key, $default);

[array-query">]()

#### Arr`::query()` {.método-colección}

El método Arr `:`:query convierte la array en una cadena de consulta:

    use Illuminate\Support\Arr;

    $array = [
        'name' => 'Taylor',
        'order' => [
            'column' => 'created_at',
            'direction' => 'desc'
        ]
    ];

    Arr::query($array);

    // name=Taylor&order[column]=created_at&order[direction]=desc

[array-random">]()

#### Arr::random(`)` {.método-colección}

El método Arr `::random` devuelve un valor aleatorio de una array:

    use Illuminate\Support\Arr;

    $array = [1, 2, 3, 4, 5];

    $random = Arr::random($array);

    // 4 - (retrieved randomly)

También puede especificar el número de elementos a devolver como segundo argumento opcional. Tenga en cuenta que este argumento devolverá una array incluso si sólo desea un elemento:

    use Illuminate\Support\Arr;

    $items = Arr::random($array, 2);

    // [2, 5] - (retrieved randomly)

[array-set">]()

#### Arr`::set()` {.método-colección}

El método `Arr::set` establece un valor dentro de un array profundamente anidado usando la notación "dot":

    use Illuminate\Support\Arr;

    $array = ['products' => ['desk' => ['price' => 100]]];

    Arr::set($array, 'products.desk.price', 200);

    // ['products' => ['desk' => ['price' => 200]]]

[array-shuffle">]()

#### Arr::`shuffle()` {.método-colección}

El método Arr `::shuffle` mezcla aleatoriamente los elementos del array:

    use Illuminate\Support\Arr;

    $array = Arr::shuffle([1, 2, 3, 4, 5]);

    // [3, 2, 5, 1, 4] - (generated randomly)

[array-sort">]()

#### Arr`::sort()` {.método-colección}

El método Arr `::` sort ordena un array por sus valores:

    use Illuminate\Support\Arr;

    $array = ['Desk', 'Table', 'Chair'];

    $sorted = Arr::sort($array);

    // ['Chair', 'Desk', 'Table']

También puede ordenar la array por los resultados de un closure dado:

    use Illuminate\Support\Arr;

    $array = [
        ['name' => 'Desk'],
        ['name' => 'Table'],
        ['name' => 'Chair'],
    ];

    $sorted = array_values(Arr::sort($array, function ($value) {
        return $value['name'];
    }));

    /*
        [
            ['name' => 'Chair'],
            ['name' => 'Desk'],
            ['name' => 'Table'],
        ]
    */

[array-sort-recursive">]()

#### Arr`::sortRecursive()` {.método-colección}

El método Arr `::sort` Recursive ordena recursivamente una array utilizando la función `sort` para submatrices indexadas numéricamente y la función `ksort` para submatrices asociativas:

    use Illuminate\Support\Arr;

    $array = [
        ['Roman', 'Taylor', 'Li'],
        ['PHP', 'Ruby', 'JavaScript'],
        ['one' => 1, 'two' => 2, 'three' => 3],
    ];

    $sorted = Arr::sortRecursive($array);

    /*
        [
            ['JavaScript', 'PHP', 'Ruby'],
            ['one' => 1, 'three' => 3, 'two' => 2],
            ['Li', 'Roman', 'Taylor'],
        ]
    */

[array-to-css-classes">]()

#### Arr`::toCssClasses()` {.método-colección}

Arr `::to` CssClasses compila condicionalmente una cadena de clases CSS. El método acepta un array de clases donde la clave del array contiene la clase o clases que desea añadir, mientras que el valor es una expresión booleana. Si el elemento array array tiene una clave numérica, siempre se incluirá en la lista de clases renderizada:

    use Illuminate\Support\Arr;

    $isActive = false;
    $hasError = true;

    $array = ['p-4', 'font-bold' => $isActive, 'bg-red' => $hasError];

    $classes = Arr::toCssClasses($array);

    /*
        'p-4 bg-red'
    */

Este método potencia la funcionalidad de Laravel que permite [combinar clases con la bolsa de atributos de un componente](/docs/%7B%7Bversion%7D%7D/blade#conditionally-merge-classes) [Blade](/docs/%7B%7Bversion%7D%7D/blade#conditional-classes), así como la [directiva](/docs/%7B%7Bversion%7D%7D/blade#conditional-classes) `@class` [Blade](/docs/%7B%7Bversion%7D%7D/blade#conditional-classes).

[array-undot">]()

#### Arr`::undot` () {.método-colección}

El método `Arr::undot` expande un array unidimensional que utiliza la notación "dot" a un array multidimensional:

    use Illuminate\Support\Arr;

    $array = [
        'user.name' => 'Kevin Malone',
        'user.occupation' => 'Accountant',
    ];

    $array = Arr::undot($array);

    // ['user' => ['name' => 'Kevin Malone', 'occupation' => 'Accountant']]

[array-where">]()

#### Arr::where(`)` {.método-colección}

El método Arr `::` where filtra una array utilizando el closure dado:

    use Illuminate\Support\Arr;

    $array = [100, '200', 300, '400', 500];

    $filtered = Arr::where($array, function ($value, $key) {
        return is_string($value);
    });

    // [1 => '200', 3 => '400']

[array-where-not-null">]()

#### Arr`::whereNotNull()` {.método-colección}

El método Arr `::where` NotNull elimina todos los valores `nulos` del array dado:

    use Illuminate\Support\Arr;

    $array = [0, null];

    $filtered = Arr::whereNotNull($array);

    // [0 => 0]

[array-wrap">]()

#### Arr`::wrap()` {.método-colección}

El método Arr:: `wrap` envuelve el valor dado en una array. Si el valor dado ya es una array, se devolverá sin modificaciones:

    use Illuminate\Support\Arr;

    $string = 'Laravel';

    $array = Arr::wrap($string);

    // ['Laravel']

Si el valor dado es `nulo`, se devolverá un array vacío:

    use Illuminate\Support\Arr;

    $array = Arr::wrap(null);

    // []

[]()

#### `data_fill()` {.método-colección}

La función `data_fill` establece un valor que falta dentro de una array u objeto anidado utilizando la notación "dot":

    $data = ['products' => ['desk' => ['price' => 100]]];

    data_fill($data, 'products.desk.price', 200);

    // ['products' => ['desk' => ['price' => 100]]]

    data_fill($data, 'products.desk.discount', 10);

    // ['products' => ['desk' => ['price' => 100, 'discount' => 10]]]

Esta función también acepta asteriscos como comodines y rellenará el objetivo en consecuencia:

    $data = [
        'products' => [
            ['name' => 'Desk 1', 'price' => 100],
            ['name' => 'Desk 2'],
        ],
    ];

    data_fill($data, 'products.*.price', 200);

    /*
        [
            'products' => [
                ['name' => 'Desk 1', 'price' => 100],
                ['name' => 'Desk 2', 'price' => 200],
            ],
        ]
    */

[]()

#### data_get(`)` {.método-colección}

La función `data_get` recupera un valor de una array u objeto anidado utilizando la notación "punto":

    $data = ['products' => ['desk' => ['price' => 100]]];

    $price = data_get($data, 'products.desk.price');

    // 100

La función `data_get` también acepta un valor por defecto, que se devolverá si no se encuentra la clave especificada:

    $discount = data_get($data, 'products.desk.discount', 0);

    // 0

La función también acepta comodines utilizando asteriscos, que pueden apuntar a cualquier clave del array u objeto:

    $data = [
        'product-one' => ['name' => 'Desk 1', 'price' => 100],
        'product-two' => ['name' => 'Desk 2', 'price' => 150],
    ];

    data_get($data, '*.name');

    // ['Desk 1', 'Desk 2'];

[]()

#### `data_set()` {.método-colección}

La función `data_set` establece un valor dentro de una array u objeto anidado utilizando la notación "punto":

    $data = ['products' => ['desk' => ['price' => 100]]];

    data_set($data, 'products.desk.price', 200);

    // ['products' => ['desk' => ['price' => 200]]]

Esta función también acepta comodines utilizando asteriscos y establecerá los valores en el objetivo en consecuencia:

    $data = [
        'products' => [
            ['name' => 'Desk 1', 'price' => 100],
            ['name' => 'Desk 2', 'price' => 150],
        ],
    ];

    data_set($data, 'products.*.price', 200);

    /*
        [
            'products' => [
                ['name' => 'Desk 1', 'price' => 200],
                ['name' => 'Desk 2', 'price' => 200],
            ],
        ]
    */

Por defecto, los valores existentes se sobrescriben. Si sólo desea establecer un valor si no existe, puede pasar `false` como cuarto argumento a la función:

    $data = ['products' => ['desk' => ['price' => 100]]];

    data_set($data, 'products.desk.price', 200, overwrite: false);

    // ['products' => ['desk' => ['price' => 100]]]

[]()

#### `head()` {.método-colección}

La función `head` devuelve el primer elemento de la array dada:

    $array = [100, 200, 300];

    $first = head($array);

    // 100

[]()

#### last(`)` {.método-colección}

La función `last` devuelve el último elemento de la array dada:

    $array = [100, 200, 300];

    $last = last($array);

    // 300

[]()

## Rutas

[]()

#### `app_path()` {.método-colección}

La función `app_path` devuelve la ruta completa al directorio de `aplicaciones` de su aplicación. También puede utilizar la función `app_path` para generar una ruta completa a un archivo relativo al directorio de la aplicación:

    $path = app_path();

    $path = app_path('Http/Controllers/Controller.php');

[]()

#### base_path`()` {.collection-method}

La función `base_path` devuelve la ruta completa al directorio raíz de la aplicación. También puede utilizar la función `base_path` para generar una ruta completa a un archivo dado relativo al directorio raíz del proyecto:

    $path = base_path();

    $path = base_path('vendor/bin');

[]()

#### `config_path()` {.collection-method}

La función `config_path` devuelve la ruta completa al directorio de `configuración` de su aplicación. También puede utilizar la función `config_path` para generar una ruta completa a un archivo determinado dentro del directorio de configuración de la aplicación:

    $path = config_path();

    $path = config_path('app.php');

[]()

#### ruta_base_datos`()` {.collection-method}

La función `database_path` devuelve la ruta completa al directorio de `la base de datos` de la aplicación. También puede utilizar la función `database_path` para generar una ruta completa a un archivo determinado dentro del directorio de la base de datos:

    $path = database_path();

    $path = database_path('factories/UserFactory.php');

[]()

#### lang_path`()` {.colección-método}

La función `lang_path` devuelve la ruta completa al directorio `lang` de su aplicación. También puede utilizar la función `lang_path` para generar una ruta completa a un archivo determinado dentro del directorio:

    $path = lang_path();

    $path = lang_path('en/messages.php');

[]()

#### `mix` () {.colección-método}

La función `mix` devuelve la ruta a un [archivo Mix versionado](/docs/%7B%7Bversion%7D%7D/mix):

    $path = mix('css/app.css');

[]()

#### public_path`()` {.método-de-colección}

La función `public_path` devuelve la ruta completa al directorio `público` de su aplicación. También puede utilizar la función `public_path` para generar una ruta completa a un archivo determinado dentro del directorio público:

    $path = public_path();

    $path = public_path('css/app.css');

[]()

#### `resource_path()` {.colección-método}

La función `resource_path` devuelve la ruta completa al directorio de `recursos` de su aplicación. También puede utilizar la función `resource_path` para generar una ruta completa a un archivo determinado dentro del directorio de recursos:

    $path = resource_path();

    $path = resource_path('sass/app.scss');

[]()

#### `storage_path()` {.collection-method}

La función `storage_path` devuelve la ruta completa al directorio de `almacenamiento de` su aplicación. También puede utilizar la función `storage_path` para generar una ruta completa a un archivo determinado dentro del directorio de almacenamiento:

    $path = storage_path();

    $path = storage_path('app/file.txt');

[]()

## Cadenas

[]()

#### `__()` {.collection-method}

La función `__` traduce la cadena o clave de traducción dada utilizando sus [archivos de localización](/docs/%7B%7Bversion%7D%7D/localization):

    echo __('Welcome to our application');

    echo __('messages.welcome');

Si la cadena o clave de traducción especificada no existe, la función `__` devolverá el valor dado. Así, utilizando el ejemplo anterior, la función `__` devolvería `messages.` welcome si esa clave de traducción no existe.

[]()

#### `class_basename()` {.método-de-colección}

La función `class_basename` devuelve el nombre de la clase dada sin el espacio de nombres de la clase:

    $class = class_basename('Foo\Bar\Baz');

    // Baz

[]()

#### `e()` {.colección-método}

La función `e` ejecuta la función `htmlspecialchars` de PHP con la opción `double_encode` activada `por` defecto:

    echo e('<html>foo</html>');

    // &lt;html&gt;foo&lt;/html&gt;

[]()

#### `preg_replace_array()` {.collection-method}

La función `preg_replace_array` reemplaza un patrón dado en la cadena secuencialmente usando un array:

    $string = 'The event will take place between :start and :end';

    $replaced = preg_replace_array('/:[a-z_]+/', ['8:30', '9:00'], $string);

    // The event will take place between 8:30 and 9:00

[]()

#### Str::after(`)` {.método-colección}

El método Str: `:` after devuelve todo lo que hay después del valor dado en una cadena. Se devolverá la cadena completa si el valor no existe dentro de la cadena:

    use Illuminate\Support\Str;

    $slice = Str::after('This is my name', 'This is');

    // ' my name'

[]()

#### Str::afterLast`()` {.método-colección}

El método Str `::` afterLast devuelve todo lo que hay después de la última aparición del valor dado en una cadena. Se devolverá la cadena completa si el valor no existe dentro de la cadena:

    use Illuminate\Support\Str;

    $slice = Str::afterLast('App\Http\Controllers\Controller', '\\');

    // 'Controller'

[]()

#### Str::ascii`()` {.método-colección}

El método Str `::` ascii intentará transliterar la cadena a un valor ASCII:

    use Illuminate\Support\Str;

    $slice = Str::ascii('û');

    // 'u'

[]()

#### Str::before(`)` {.método-colección}

El método Str:: `before` devuelve todo lo anterior al valor dado en una cadena:

    use Illuminate\Support\Str;

    $slice = Str::before('This is my name', 'my name');

    // 'This is '

[]()

#### Str::`beforeLast()` {.método-colección}

El método Str:: `beforeLast` devuelve todo lo que hay antes de la última aparición del valor dado en una cadena:

    use Illuminate\Support\Str;

    $slice = Str::beforeLast('This is my name', 'is');

    // 'This '

[]()

#### Str::between(`)` {.método-colección}

El método Str: `:` between devuelve la parte de una cadena comprendida entre dos valores:

    use Illuminate\Support\Str;

    $slice = Str::between('This is my name', 'This', 'name');

    // ' is my '

[]()

#### Str::betweenFirst(`)` {.método-colección}

El método Str:: `betweenFirst` devuelve la porción más pequeña posible de una cadena entre dos valores:

    use Illuminate\Support\Str;

    $slice = Str::betweenFirst('[a] bc [d]', '[', ']');

    // 'a'

[]()

#### Str::camel(`)` {.método-colección}

El método Str:: `camel` convierte la cadena dada a `camelCase`:

    use Illuminate\Support\Str;

    $converted = Str::camel('foo_bar');

    // fooBar

[]()

#### Str`::contains()` {.método-colección}

El método Str: `:contains` determina si la cadena dada contiene el valor dado. Este método distingue entre mayúsculas y minúsculas:

    use Illuminate\Support\Str;

    $contains = Str::contains('This is my name', 'my');

    // true

También puede pasar una array de valores para determinar si la cadena dada contiene alguno de los valores de la array:

    use Illuminate\Support\Str;

    $contains = Str::contains('This is my name', ['my', 'foo']);

    // true

[]()

#### `Str::containsAll()` {.método-colección}

El método Str: `:containsAll` determina si la cadena dada contiene todos los valores de una array dada:

    use Illuminate\Support\Str;

    $containsAll = Str::containsAll('This is my name', ['my', 'name']);

    // true

[]()

#### Str:`:endsWith()` {.método-colección}

El método Str:: `endsWith` determina si la cadena dada termina con el valor dado:

    use Illuminate\Support\Str;

    $result = Str::endsWith('This is my name', 'name');

    // true

También puede pasar una array de valores para determinar si la cadena dada termina con cualquiera de los valores de la array:

    use Illuminate\Support\Str;

    $result = Str::endsWith('This is my name', ['name', 'foo']);

    // true

    $result = Str::endsWith('This is my name', ['this', 'foo']);

    // false

[]()

#### Str::excerpt`()` {.método-colección}

El método Str:: `excerpt` extrae un fragmento de una cadena dada que coincide con la primera instancia de una frase dentro de esa cadena:

    use Illuminate\Support\Str;

    $excerpt = Str::excerpt('This is my name', 'my', [
        'radius' => 3
    ]);

    // '...is my na...'

La opción `radius`, que por defecto es `100`, permite definir el número de caracteres que deben aparecer a cada lado de la cadena truncada.

Además, puede utilizar la opción de `omisión` para definir la cadena que se antepondrá y añadirá a la cadena truncada:

    use Illuminate\Support\Str;

    $excerpt = Str::excerpt('This is my name', 'name', [
        'radius' => 3,
        'omission' => '(...) '
    ]);

    // '(...) my name'

[]()

#### Str::`finish()` {.método-colección}

El método `Str`::finish añade una única instancia del valor dado a una cadena si aún no termina con ese valor:

    use Illuminate\Support\Str;

    $adjusted = Str::finish('this/string', '/');

    // this/string/

    $adjusted = Str::finish('this/string/', '/');

    // this/string/

[]()

#### Str::headline(`)` {.método-colección}

El método Str:: `headline` convierte cadenas delimitadas por mayúsculas, guiones o guiones bajos en una cadena delimitada por espacios con la primera letra de cada palabra en mayúscula:

    use Illuminate\Support\Str;

    $headline = Str::headline('steve_jobs');

    // Steve Jobs

    $headline = Str::headline('EmailNotificationSent');

    // Email Notification Sent

[]()

#### Str::`inlineMarkdown` () {.método-colección}

El método `Str::inlineMarkdown` convierte Markdown con sabor a GitHub en HTML en línea utilizando [CommonMark](https://commonmark.thephpleague.com/). Sin embargo, a diferencia del método `markdown`, no envuelve todo el HTML generado en un elemento a nivel de bloque:

    use Illuminate\Support\Str;

    $html = Str::inlineMarkdown('**Laravel**');

    // <strong>Laravel</strong>

[]()

#### Str::is(`)` {.método-colección}

El método Str: `:is` determina si una cadena dada coincide con un patrón dado. Los asteriscos pueden utilizarse como comodines:

    use Illuminate\Support\Str;

    $matches = Str::is('foo*', 'foobar');

    // true

    $matches = Str::is('baz*', 'foobar');

    // false

[]()

#### Str`::isAscii()` {.método-colección}

El método Str `::` isAscii determina si una cadena dada es ASCII de 7 bits:

    use Illuminate\Support\Str;

    $isAscii = Str::isAscii('Taylor');

    // true

    $isAscii = Str::isAscii('ü');

    // false

[]()

#### Str:`:isJson()` {.método-colección}

El método Str `::isJson` determina si la cadena dada es JSON válida:

    use Illuminate\Support\Str;

    $result = Str::isJson('[1,2,3]');

    // true

    $result = Str::isJson('{"first": "John", "last": "Doe"}');

    // true

    $result = Str::isJson('{first: "John", last: "Doe"}');

    // false

[]()

#### Str::isUlid(`)` {.método-colección}

El método Str `::is` Ulid determina si la cadena dada es un ULID válido:

    use Illuminate\Support\Str;

    $isUlid = Str::isUlid('01gd6r360bp37zj17nxb55yv40');

    // true

    $isUlid = Str::isUlid('laravel');

    // false

[]()

#### Str::isUuid(`)` {.método-colección}

El método Str: `:` isUuid determina si la cadena dada es un UUID válido:

    use Illuminate\Support\Str;

    $isUuid = Str::isUuid('a0a2a2d2-0b87-4a18-83f2-2529882be2de');

    // true

    $isUuid = Str::isUuid('laravel');

    // false

[]()

#### Str::`kebab()` {.método-colección}

El método Str:: `kebab` convierte la cadena dada a `kebab-case`:

    use Illuminate\Support\Str;

    $converted = Str::kebab('fooBar');

    // foo-bar

[]()

#### Str::lcfirst(`)` {.método-colección}

El método Str: `:` lcfirst devuelve la cadena dada con el primer carácter en minúsculas:

    use Illuminate\Support\Str;

    $string = Str::lcfirst('Foo Bar');

    // foo Bar

[]()

#### Str::length(`)` {.método-colección}

El método Str: `:` length devuelve la longitud de la cadena dada:

    use Illuminate\Support\Str;

    $length = Str::length('Laravel');

    // 7

[]()

#### Str::limit(`)` {.método-colección}

El método Str:: `limit` trunca la cadena dada hasta la longitud especificada:

    use Illuminate\Support\Str;

    $truncated = Str::limit('The quick brown fox jumps over the lazy dog', 20);

    // The quick brown fox...

Puede pasar un tercer argumento al método para cambiar la cadena que se añadirá al final de la cadena truncada:

    use Illuminate\Support\Str;

    $truncated = Str::limit('The quick brown fox jumps over the lazy dog', 20, ' (...)');

    // The quick brown fox (...)

[]()

#### `Str::lower()` {.método-colección}

El método `Str`::lower convierte la cadena dada a minúsculas:

    use Illuminate\Support\Str;

    $converted = Str::lower('LARAVEL');

    // laravel

[]()

#### Str::`markdown` () {.método-colección}

El método Str:: `markdown` convierte GitHub flavored Markdown en HTML utilizando [CommonMark](https://commonmark.thephpleague.com/):

    use Illuminate\Support\Str;

    $html = Str::markdown('# Laravel');

    // <h1>Laravel</h1>

    $html = Str::markdown('# Taylor <b>Otwell</b>', [
        'html_input' => 'strip',
    ]);

    // <h1>Taylor Otwell</h1>

[]()

#### Str::`mask` () {.método-colección}

El método Str:: `mask` enmascara una parte de una cadena con un carácter repetido, y puede utilizarse para ofuscar segmentos de cadenas como direcciones de correo electrónico y números de teléfono:

    use Illuminate\Support\Str;

    $string = Str::mask('taylor@example.com', '*', 3);

    // tay***************

Si es necesario, proporcione un número negativo como tercer argumento del método `mask`, que indicará al método que comience a enmascarar a la distancia dada del final de la cadena:

    $string = Str::mask('taylor@example.com', '*', -15, 3);

    // tay***@example.com

[]()

#### Str::`orderedUuid` () {.método-colección}

El método Str:: `orderedUuid` genera un UUID "timestamp first" que puede almacenarse de forma eficiente en una columna indexada de la base de datos. Cada UUID que se genere utilizando este método se ordenará después de los UUID generados previamente utilizando el método:

    use Illuminate\Support\Str;

    return (string) Str::orderedUuid();

[]()

#### Str::`padBoth` () {.método-colección}

El método Str: `:padBoth` envuelve la función `str_pad` de PHP, rellenando ambos lados de una cadena con otra cadena hasta que la cadena final alcanza la longitud deseada:

    use Illuminate\Support\Str;

    $padded = Str::padBoth('James', 10, '_');

    // '__James___'

    $padded = Str::padBoth('James', 10);

    // '  James   '

[]()

#### Str::padLeft`()` {.método-de-colección}

El método `Str`::padLeft envuelve la función `str_pad` de PHP, rellenando el lado izquierdo de una cadena con otra cadena hasta que la cadena final alcanza la longitud deseada:

    use Illuminate\Support\Str;

    $padded = Str::padLeft('James', 10, '-=');

    // '-=-=-James'

    $padded = Str::padLeft('James', 10);

    // '     James'

[]()

#### Str::padRight`()` {.método-colección}

El método `Str::pad` Right envuelve la función `str_pad` de PHP, rellenando el lado derecho de una cadena con otra cadena hasta que la cadena final alcanza la longitud deseada:

    use Illuminate\Support\Str;

    $padded = Str::padRight('James', 10, '-');

    // 'James-----'

    $padded = Str::padRight('James', 10);

    // 'James     '

[]()

#### Str::`plural()` {.método-de-colección}

El método `Str::plural` convierte una cadena de palabras en singular a su forma plural. Esta función soporta [cualquiera de los lenguajes soportados por el pluralizador de Laravel](/docs/%7B%7Bversion%7D%7D/localization#pluralization-language):

    use Illuminate\Support\Str;

    $plural = Str::plural('car');

    // cars

    $plural = Str::plural('child');

    // children

Puede proporcionar un número entero como segundo argumento de la función para recuperar la forma singular o plural de la cadena:

    use Illuminate\Support\Str;

    $plural = Str::plural('child', 2);

    // children

    $singular = Str::plural('child', 1);

    // child

[]()

#### Str::`pluralStudly` () {.método-colección}

El método Str:: `pluralStudly` convierte una palabra singular formateada en mayúsculas a su forma plural. Esta función soporta cualquiera de [los lenguajes soport](/docs/%7B%7Bversion%7D%7D/localization#pluralization-language)ados por el pluralizador de Laravel:

    use Illuminate\Support\Str;

    $plural = Str::pluralStudly('VerifiedHuman');

    // VerifiedHumans

    $plural = Str::pluralStudly('UserFeedback');

    // UserFeedback

Puede proporcionar un número entero como segundo argumento de la función para recuperar la forma singular o plural de la cadena:

    use Illuminate\Support\Str;

    $plural = Str::pluralStudly('VerifiedHuman', 2);

    // VerifiedHumans

    $singular = Str::pluralStudly('VerifiedHuman', 1);

    // VerifiedHuman

[]()

#### Str::random(`)` {.método-colección}

El método Str:: `random` genera una cadena aleatoria de la longitud especificada. Esta función usa la función `random_bytes` de PHP:

    use Illuminate\Support\Str;

    $random = Str::random(40);

[]()

#### Str::remove(`)` {.método-colección}

El método Str:: `remove` elimina el valor o array de valores dados de la cadena:

    use Illuminate\Support\Str;

    $string = 'Peter Piper picked a peck of pickled peppers.';

    $removed = Str::remove('e', $string);

    // Ptr Pipr pickd a pck of pickld ppprs.

También puede pasar `false` como tercer argumento al método `remove` para ignorar mayúsculas y minúsculas al eliminar cadenas.

[]()

#### `Str::replace()` {.método-colección}

El método Str: `:replace` sustituye una cadena dada dentro de la cadena:

    use Illuminate\Support\Str;

    $string = 'Laravel 8.x';

    $replaced = Str::replace('8.x', '9.x', $string);

    // Laravel 9.x

[]()

#### Str:`:replaceArray()` {.método-colección}

El método Str:: `replaceArray` reemplaza un valor dado en la cadena secuencialmente usando un array:

    use Illuminate\Support\Str;

    $string = 'The event will take place between ? and ?';

    $replaced = Str::replaceArray('?', ['8:30', '9:00'], $string);

    // The event will take place between 8:30 and 9:00

[]()

#### Str:`:replaceFirst()` {.método-colección}

El método Str: `:replaceFirst` reemplaza la primera aparición de un valor dado en una cadena:

    use Illuminate\Support\Str;

    $replaced = Str::replaceFirst('the', 'a', 'the quick brown fox jumps over the lazy dog');

    // a quick brown fox jumps over the lazy dog

[]()

#### Str::replaceLast(`)` {.método-colección}

El método Str: `:replaceLast` reemplaza la última aparición de un valor dado en una cadena:

    use Illuminate\Support\Str;

    $replaced = Str::replaceLast('the', 'a', 'the quick brown fox jumps over the lazy dog');

    // the quick brown fox jumps over a lazy dog

[]()

#### Str::reverse(`)` {.método-colección}

El método Str:: `reverse` invierte la cadena dada:

    use Illuminate\Support\Str;

    $reversed = Str::reverse('Hello World');

    // dlroW olleH

[]()

#### Str::`singular` () {.método-colección}

El método `Str::singular` convierte una cadena a su forma singular. Esta función soporta cualquiera de [los lenguajes soport](/docs/%7B%7Bversion%7D%7D/localization#pluralization-language)ados por el pluralizador de Laravel:

    use Illuminate\Support\Str;

    $singular = Str::singular('cars');

    // car

    $singular = Str::singular('children');

    // child

[]()

#### Str::`slug` () {.método-colección}

El método Str:: `slug` genera una URL amigable "slug" a partir de la cadena dada:

    use Illuminate\Support\Str;

    $slug = Str::slug('Laravel 5 Framework', '-');

    // laravel-5-framework

[]()

#### Str::`snake` () {.método-colección}

El método Str:: `snake` convierte la cadena dada a `snake_case`:

    use Illuminate\Support\Str;

    $converted = Str::snake('fooBar');

    // foo_bar

    $converted = Str::snake('fooBar', '-');

    // foo-bar

[]()

#### Str::`squish()` {.método-colección}

El método Str:: `squish` elimina todos los espacios en blanco extraños de una cadena, incluidos los espacios en blanco extraños entre palabras:

    use Illuminate\Support\Str;

    $string = Str::squish('    laravel    framework    ');

    // laravel framework

[]()

#### Str:`:start()` {.método-colección}

El método Str: `:start` añade una única instancia del valor dado a una cadena si no empieza ya con ese valor:

    use Illuminate\Support\Str;

    $adjusted = Str::start('this/string', '/');

    // /this/string

    $adjusted = Str::start('/this/string', '/');

    // /this/string

[]()

#### Str`::startsWith()` {.método-colección}

El método Str `::startsWith` determina si la cadena dada comienza con el valor dado:

    use Illuminate\Support\Str;

    $result = Str::startsWith('This is my name', 'This');

    // true

Si se pasa un array de posibles valores, el método `startsWith` devolverá `true` si la cadena empieza por alguno de los valores dados:

    $result = Str::startsWith('This is my name', ['This', 'That', 'There']);

    // true

[]()

#### Str::studly`()` {.método-colección}

El método Str:: `studly` convierte la cadena dada a `StudlyCase`:

    use Illuminate\Support\Str;

    $converted = Str::studly('foo_bar');

    // FooBar

[]()

#### Str`::substr()` {.método-colección}

El método Str: `:substr` devuelve la parte de la cadena especificada por los parámetros start y length:

    use Illuminate\Support\Str;

    $converted = Str::substr('The Laravel Framework', 4, 7);

    // Laravel

[]()

#### Str::substrCount(`)` {.método-colección}

El método Str: `:` substrCount devuelve el número de apariciones de un valor dado en la cadena dada:

    use Illuminate\Support\Str;

    $count = Str::substrCount('If you like ice cream, you will like snow cones.', 'like');

    // 2

[]()

#### Str::substrReplace(`)` {.método-colección}

El método Str: `:subs` trReplace sustituye texto dentro de una parte de una cadena, empezando en la posición especificada por el tercer argumento y sustituyendo el número de caracteres especificado por el cuarto argumento. Pasando `0` al cuarto argumento del método se insertará la cadena en la posición especificada sin reemplazar ninguno de los caracteres existentes en la cadena:

    use Illuminate\Support\Str;

    $result = Str::substrReplace('1300', ':', 2);
    // 13:

    $result = Str::substrReplace('1300', ':', 2, 0);
    // 13:00

[]()

#### Str::swap`()` {.método-colección}

El método `Str`::swap reemplaza múltiples valores en la cadena dada usando la función `strtr` de PHP:

    use Illuminate\Support\Str;

    $string = Str::swap([
        'Tacos' => 'Burritos',
        'great' => 'fantastic',
    ], 'Tacos are great!');

    // Burritos are fantastic!

[]()

#### Str::title(`)` {.método-colección}

El método Str:: `title` convierte la cadena dada a `Title Case`:

    use Illuminate\Support\Str;

    $converted = Str::title('a nice title uses the correct case');

    // A Nice Title Uses The Correct Case

[]()

#### Str:`:toHtmlString()` {.método-colección}

El método Str: `:toHtmlString` convierte la instancia de cadena en una instancia de `Illuminate\Support\HtmlString`, que puede mostrarse en plantillas Blade:

    use Illuminate\Support\Str;

    $htmlString = Str::of('Nuno Maduro')->toHtmlString();

[]()

#### Str::`ucfirst` () {.método-colección}

El método Str `::` ucfirst devuelve la cadena dada con el primer carácter en mayúsculas:

    use Illuminate\Support\Str;

    $string = Str::ucfirst('foo bar');

    // Foo bar

[]()

#### Str::`ucsplit()` {.método-colección}

El método Str:: `ucsplit` divide la cadena dada en una array por caracteres en mayúsculas:

    use Illuminate\Support\Str;

    $segments = Str::ucsplit('FooBar');

    // [0 => 'Foo', 1 => 'Bar']

[]()

#### Str::upper(`)` {.método-colección}

El método Str: `:` upper convierte la cadena dada a mayúsculas:

    use Illuminate\Support\Str;

    $string = Str::upper('laravel');

    // LARAVEL

[]()

#### Str::ulid(`)` {.método-colección}

El método Str:: `ulid` genera un ULID:

    use Illuminate\Support\Str;

    return (string) Str::ulid();

    // 01gd6r360bp37zj17nxb55yv40

[]()

#### Str`::uuid()` {.método-colección}

El método Str:: `uuid` genera un UUID (versión 4):

    use Illuminate\Support\Str;

    return (string) Str::uuid();

[]()

#### Str::`wordCount()` {.método-colección}

El método Str:: `wordCount` devuelve el número de palabras que contiene una cadena:

```php
use Illuminate\Support\Str;

Str::wordCount('Hello, world!'); // 2
```

[]()

#### Str::words(`)` {.método-colección}

El método Str:: `words` limita el número de palabras de una cadena. Puede pasarse una cadena adicional a este método a través de su tercer argumento para especificar qué cadena debe añadirse al final de la cadena truncada:

    use Illuminate\Support\Str;

    return Str::words('Perfectly balanced, as all things should be.', 3, ' >>>');

    // Perfectly balanced, as >>>

[]()

#### `str` () {.método-colección}

La función `str` devuelve una nueva instancia `Illuminate\Support\Stringable` de la cadena dada. Esta función es equivalente al método `Str::of`:

    $string = str('Taylor')->append(' Otwell');

    // 'Taylor Otwell'

Si no se proporciona ningún argumento a la función `str`, la función devuelve una instancia de `Illuminate\Support\Str`:

    $snake = str()->snake('FooBar');

    // 'foo_bar'

[]()

#### `trans()` {.método-colección}

La función `trans` traduce la clave de traducción dada utilizando sus [archivos de localización](/docs/%7B%7Bversion%7D%7D/localization):

    echo trans('messages.welcome');

Si la clave de traducción especificada no existe, la función `trans` devolverá la clave dada. Así, utilizando el ejemplo anterior, la función `trans` devolvería `messages.welcome` si la clave de traducción no existe.

[]()

#### `trans_choice()` {.método-de-colección}

La función `trans_choice` traduce la clave de traducción dada con inflexión:

    echo trans_choice('messages.notifications', $unreadCount);

Si la clave de traducción especificada no existe, la función `trans_choice` devolverá la clave dada. Así, utilizando el ejemplo anterior, la función `trans_choice` devolvería `messages.notifications` si la clave de traducción no existe.

[]()

## Cadenas fluidas

Las cadenas fluidas proporcionan una interfaz más fluida y orientada a objetos para trabajar con valores de cadena, lo que permite encadenar múltiples operaciones de cadena utilizando una sintaxis más legible en comparación con las operaciones de cadena tradicionales.

[]()

#### `after` {.método-colección}

El método `after` devuelve todo lo que hay después del valor dado en una cadena. Se devolverá la cadena completa si el valor no existe dentro de la cadena:

    use Illuminate\Support\Str;

    $slice = Str::of('This is my name')->after('This is');

    // ' my name'

[]()

#### `afterLast` {.método-colección}

El método `afterLast` devuelve todo lo que hay después de la última aparición del valor dado en una cadena. Se devolverá la cadena entera si el valor no existe dentro de la cadena:

    use Illuminate\Support\Str;

    $slice = Str::of('App\Http\Controllers\Controller')->afterLast('\\');

    // 'Controller'

[]()

#### `append` {.método-colección}

El método `append` añade los valores dados a la cadena:

    use Illuminate\Support\Str;

    $string = Str::of('Taylor')->append(' Otwell');

    // 'Taylor Otwell'

[]()

#### `ascii` {.método-colección}

El método `ascii` intentará transcribir la cadena a un valor ASCII:

    use Illuminate\Support\Str;

    $string = Str::of('ü')->ascii();

    // 'u'

[]()

#### `basename` {.método-colección}

El método `basename` devolverá el componente final del nombre de la cadena dada:

    use Illuminate\Support\Str;

    $string = Str::of('/foo/bar/baz')->basename();

    // 'baz'

Si es necesario, puede proporcionar una "extensión" que se eliminará del componente final:

    use Illuminate\Support\Str;

    $string = Str::of('/foo/bar/baz.jpg')->basename('.jpg');

    // 'baz'

[]()

#### `before` {.método-colección}

El método `before` devuelve todo lo que hay antes del valor dado en una cadena:

    use Illuminate\Support\Str;

    $slice = Str::of('This is my name')->before('my name');

    // 'This is '

[]()

#### `beforeLast` {.método-colección}

El método `beforeLast` devuelve todo lo que hay antes de la última aparición del valor dado en una cadena:

    use Illuminate\Support\Str;

    $slice = Str::of('This is my name')->beforeLast('is');

    // 'This '

[]()

#### `between` {.método-colección}

El método `between` devuelve la parte de una cadena comprendida entre dos valores:

    use Illuminate\Support\Str;

    $converted = Str::of('This is my name')->between('This', 'name');

    // ' is my '

[]()

#### `betweenFirst` {.método-colección}

El método `betweenFirst` devuelve la porción más pequeña posible de una cadena entre dos valores:

    use Illuminate\Support\Str;

    $converted = Str::of('[a] bc [d]')->betweenFirst('[', ']');

    // 'a'

[]()

#### `camel` {.método-colección}

El método `camel` convierte la cadena dada a `camelCase`:

    use Illuminate\Support\Str;

    $converted = Str::of('foo_bar')->camel();

    // fooBar

[]()

#### `classBasename` {.método-colección}

El método `classBasename` devuelve el nombre de la clase dada sin su espacio de nombres:

    use Illuminate\Support\Str;

    $class = Str::of('Foo\Bar\Baz')->classBasename();

    // Baz

[]()

#### `contains` {.método-colección}

El método `contains` determina si la cadena dada contiene el valor dado. Este método distingue entre mayúsculas y minúsculas:

    use Illuminate\Support\Str;

    $contains = Str::of('This is my name')->contains('my');

    // true

También puede pasar una array de valores para determinar si la cadena dada contiene alguno de los valores de la array:

    use Illuminate\Support\Str;

    $contains = Str::of('This is my name')->contains(['my', 'foo']);

    // true

[]()

#### `containsAll` {.método-colección}

El método `containsAll` determina si la cadena dada contiene todos los valores de la array dada:

    use Illuminate\Support\Str;

    $containsAll = Str::of('This is my name')->containsAll(['my', 'name']);

    // true

[]()

#### `dirname` {.método-colección}

El método `dirname` devuelve la parte del directorio padre de la cadena dada:

    use Illuminate\Support\Str;

    $string = Str::of('/foo/bar/baz')->dirname();

    // '/foo/bar'

Si es necesario, puede especificar cuántos niveles de directorio desea recortar de la cadena:

    use Illuminate\Support\Str;

    $string = Str::of('/foo/bar/baz')->dirname(2);

    // '/foo'

[]()

#### `excerpt` {.método-colección}

El método `excerpt` extrae un fragmento de la cadena que coincide con la primera instancia de una frase dentro de esa cadena:

    use Illuminate\Support\Str;

    $excerpt = Str::of('This is my name')->excerpt('my', [
        'radius' => 3
    ]);

    // '...is my na...'

La opción `radio`, que por defecto es `100`, le permite definir el número de caracteres que deben aparecer a cada lado de la cadena truncada.

Además, puede utilizar la opción de `omisión` para cambiar la cadena que se antepondrá y añadirá a la cadena truncada:

    use Illuminate\Support\Str;

    $excerpt = Str::of('This is my name')->excerpt('name', [
        'radius' => 3,
        'omission' => '(...) '
    ]);

    // '(...) my name'

[]()

#### `endsWith` {.método-colección}

El método `endsWith` determina si la cadena dada termina con el valor dado:

    use Illuminate\Support\Str;

    $result = Str::of('This is my name')->endsWith('name');

    // true

También puede pasar una array de valores para determinar si la cadena dada termina con cualquiera de los valores de la array:

    use Illuminate\Support\Str;

    $result = Str::of('This is my name')->endsWith(['name', 'foo']);

    // true

    $result = Str::of('This is my name')->endsWith(['this', 'foo']);

    // false

[]()

#### `exactly` {.método-colección}

El método `exactly` determina si la cadena dada coincide exactamente con otra cadena:

    use Illuminate\Support\Str;

    $result = Str::of('Laravel')->exactly('Laravel');

    // true

[]()

#### `explode` {.método-colección}

El método `explode` divide la cadena por el delimitador dado y devuelve una colección que contiene cada sección de la cadena dividida:

    use Illuminate\Support\Str;

    $collection = Str::of('foo bar baz')->explode(' ');

    // collect(['foo', 'bar', 'baz'])

[]()

#### `finish` {.método-colección}

El método `finish` añade una única instancia del valor dado a una cadena si no termina ya con ese valor:

    use Illuminate\Support\Str;

    $adjusted = Str::of('this/string')->finish('/');

    // this/string/

    $adjusted = Str::of('this/string/')->finish('/');

    // this/string/

[]()

#### `headline` {.método-colección}

El método `headline` convierte cadenas delimitadas por mayúsculas, guiones o guiones bajos en una cadena delimitada por espacios con la primera letra de cada palabra en mayúscula:

    use Illuminate\Support\Str;

    $headline = Str::of('taylor_otwell')->headline();

    // Taylor Otwell

    $headline = Str::of('EmailNotificationSent')->headline();

    // Email Notification Sent

[]()

#### `inlineMarkdown` {.método-colección}

El método `inlineMarkdown` convierte Markdown con sabor a GitHub en HTML en línea utilizando [CommonMark](https://commonmark.thephpleague.com/). Sin embargo, a diferencia del método `markdown`, no envuelve todo el HTML generado en un elemento a nivel de bloque:

    use Illuminate\Support\Str;

    $html = Str::of('**Laravel**')->inlineMarkdown();

    // <strong>Laravel</strong>

[]()

#### `is` {.método-colección}

El método `is` determina si una cadena dada coincide con un patrón dado. Los asteriscos pueden utilizarse como comodines

    use Illuminate\Support\Str;

    $matches = Str::of('foobar')->is('foo*');

    // true

    $matches = Str::of('foobar')->is('baz*');

    // false

[]()

#### `isAscii` {.método-colección}

El método `isAscii` determina si una cadena dada es una cadena ASCII:

    use Illuminate\Support\Str;

    $result = Str::of('Taylor')->isAscii();

    // true

    $result = Str::of('ü')->isAscii();

    // false

[]()

#### `isEmpty` {.método-colección}

El método `isEmpty` determina si la cadena dada está vacía:

    use Illuminate\Support\Str;

    $result = Str::of('  ')->trim()->isEmpty();

    // true

    $result = Str::of('Laravel')->trim()->isEmpty();

    // false

[]()

#### `isNotEmpty` {.método-colección}

El método `isNotEmpty` determina si la cadena dada no está vacía:

    use Illuminate\Support\Str;

    $result = Str::of('  ')->trim()->isNotEmpty();

    // false

    $result = Str::of('Laravel')->trim()->isNotEmpty();

    // true

[]()

#### `isJson` {.método-colección}

El método `isJson` determina si una cadena dada es JSON válido:

    use Illuminate\Support\Str;

    $result = Str::of('[1,2,3]')->isJson();

    // true

    $result = Str::of('{"first": "John", "last": "Doe"}')->isJson();

    // true

    $result = Str::of('{first: "John", last: "Doe"}')->isJson();

    // false

[]()

#### `isUlid` {.método-colección}

El método `isUlid` determina si una cadena dada es un ULID:

    use Illuminate\Support\Str;

    $result = Str::of('01gd6r360bp37zj17nxb55yv40')->isUlid();

    // true

    $result = Str::of('Taylor')->isUlid();

    // false

[]()

#### `isUuid` {.método-colección}

El método `isUuid` determina si una cadena dada es un UUID:

    use Illuminate\Support\Str;

    $result = Str::of('5ace9ab9-e9cf-4ec6-a19d-5881212a452c')->isUuid();

    // true

    $result = Str::of('Taylor')->isUuid();

    // false

[]()

#### `kebab` {.collection-method}

El método `kebab` convierte la cadena dada a `kebab-case`:

    use Illuminate\Support\Str;

    $converted = Str::of('fooBar')->kebab();

    // foo-bar

[]()

#### `lcfirst` {.método-colección}

El método `lcfirst` devuelve la cadena dada con el primer carácter en minúsculas:

    use Illuminate\Support\Str;

    $string = Str::of('Foo Bar')->lcfirst();

    // foo Bar

[]()

#### `length` {.método-colección}

El método `length` devuelve la longitud de la cadena dada:

    use Illuminate\Support\Str;

    $length = Str::of('Laravel')->length();

    // 7

[]()

#### `limit` {.método-colección}

El método `limit` trunca la cadena dada hasta la longitud especificada:

    use Illuminate\Support\Str;

    $truncated = Str::of('The quick brown fox jumps over the lazy dog')->limit(20);

    // The quick brown fox...

También puede pasar un segundo argumento para cambiar la cadena que se añadirá al final de la cadena truncada:

    use Illuminate\Support\Str;

    $truncated = Str::of('The quick brown fox jumps over the lazy dog')->limit(20, ' (...)');

    // The quick brown fox (...)

[]()

#### `lower` {.método-colección}

El método `lower` convierte la cadena dada a minúsculas:

    use Illuminate\Support\Str;

    $result = Str::of('LARAVEL')->lower();

    // 'laravel'

[]()

#### `ltrim` {.método-colección}

El método `ltrim` recorta el lado izquierdo de la cadena:

    use Illuminate\Support\Str;

    $string = Str::of('  Laravel  ')->ltrim();

    // 'Laravel  '

    $string = Str::of('/Laravel/')->ltrim('/');

    // 'Laravel/'

[]()

#### `markdown` {.método-colección}

El método `markdown` convierte Markdown con sabor a GitHub en HTML:

    use Illuminate\Support\Str;

    $html = Str::of('# Laravel')->markdown();

    // <h1>Laravel</h1>

    $html = Str::of('# Taylor <b>Otwell</b>')->markdown([
        'html_input' => 'strip',
    ]);

    // <h1>Taylor Otwell</h1>

[]()

#### `mask` {.collection-method}

El método `mask` enmascara una parte de una cadena con un carácter repetido, y puede utilizarse para ofuscar segmentos de cadenas como direcciones de correo electrónico y números de teléfono:

    use Illuminate\Support\Str;

    $string = Str::of('taylor@example.com')->mask('*', 3);

    // tay***************

Si es necesario, proporcione un número negativo como tercer argumento al método `mask`, que le indicará que comience a enmascarar a la distancia dada del final de la cadena:

    $string = Str::of('taylor@example.com')->mask('*', -15, 3);

    // tay***@example.com

[]()

#### `match` {.método-colección}

El método `match` devuelve la parte de una cadena que coincide con un patrón de expresión regular dado:

    use Illuminate\Support\Str;

    $result = Str::of('foo bar')->match('/bar/');

    // 'bar'

    $result = Str::of('foo bar')->match('/foo (.*)/');

    // 'bar'

[]()

#### `matchAll` {.método-colección}

El método `matchAll` devuelve una colección que contiene las partes de una cadena que coinciden con un patrón de expresión regular dado:

    use Illuminate\Support\Str;

    $result = Str::of('bar foo bar')->matchAll('/bar/');

    // collect(['bar', 'bar'])

Si especifica un grupo coincidente dentro de la expresión, Laravel devolverá una colección con las coincidencias de ese grupo:

    use Illuminate\Support\Str;

    $result = Str::of('bar fun bar fly')->matchAll('/f(\w*)/');

    // collect(['un', 'ly']);

Si no se encuentra ninguna coincidencia, se devolverá una colección vacía.

[]()

#### `newLine` {.método-colección}

El método `newLine` añade un carácter de "fin de línea" a una cadena:

    use Illuminate\Support\Str;

    $padded = Str::of('Laravel')->newLine()->append('Framework');

    // 'Laravel
    //  Framework'

[]()

#### `padBoth` {.método-colección}

El método `padBoth` envuelve la función `str_pad` de PHP, rellenando ambos lados de una cadena con otra cadena hasta que la cadena final alcanza la longitud deseada:

    use Illuminate\Support\Str;

    $padded = Str::of('James')->padBoth(10, '_');

    // '__James___'

    $padded = Str::of('James')->padBoth(10);

    // '  James   '

[]()

#### `padLeft` {.collection-method}

El método `padLeft` envuelve la función `str_pad` de PHP, rellenando el lado izquierdo de una cadena con otra cadena hasta que la cadena final alcanza la longitud deseada:

    use Illuminate\Support\Str;

    $padded = Str::of('James')->padLeft(10, '-=');

    // '-=-=-James'

    $padded = Str::of('James')->padLeft(10);

    // '     James'

[]()

#### `padRight` {.collection-method}

El método `padRight` envuelve la función `str_pad` de PHP, rellenando el lado derecho de una cadena con otra cadena hasta que la cadena final alcance la longitud deseada:

    use Illuminate\Support\Str;

    $padded = Str::of('James')->padRight(10, '-');

    // 'James-----'

    $padded = Str::of('James')->padRight(10);

    // 'James     '

[]()

#### `pipe` {.collection-method}

El método `pipe` permite transformar la cadena pasando su valor actual a la llamada dada:

    use Illuminate\Support\Str;

    $hash = Str::of('Laravel')->pipe('md5')->prepend('Checksum: ');

    // 'Checksum: a5c95b86291ea299fcbe64458ed12702'

    $closure = Str::of('foo')->pipe(function ($str) {
        return 'bar';
    });

    // 'bar'

[]()

#### `plural` {.método-colección}

El método `plural` convierte una cadena de palabras en singular a su forma plural. Esta función soporta [cualquiera de los lenguajes soportados por el pluralizador de Laravel](/docs/%7B%7Bversion%7D%7D/localization#pluralization-language):

    use Illuminate\Support\Str;

    $plural = Str::of('car')->plural();

    // cars

    $plural = Str::of('child')->plural();

    // children

Puede proporcionar un número entero como segundo argumento de la función para recuperar la forma singular o plural de la cadena:

    use Illuminate\Support\Str;

    $plural = Str::of('child')->plural(2);

    // children

    $plural = Str::of('child')->plural(1);

    // child

[]()

#### `prepend` {.método-colección}

El método `prepend` añade los valores dados a la cadena:

    use Illuminate\Support\Str;

    $string = Str::of('Framework')->prepend('Laravel ');

    // Laravel Framework

[]()

#### `remove` {.método-colección}

El método `remove` elimina el valor o la array de valores dados de la cadena:

    use Illuminate\Support\Str;

    $string = Str::of('Arkansas is quite beautiful!')->remove('quite');

    // Arkansas is beautiful!

También puede pasar `false` como segundo parámetro para ignorar mayúsculas y minúsculas al eliminar cadenas.

[]()

#### `replace` {.método-colección}

El método `replace` reemplaza una cadena dada dentro de la cadena:

    use Illuminate\Support\Str;

    $replaced = Str::of('Laravel 6.x')->replace('6.x', '7.x');

    // Laravel 7.x

[]()

#### `replaceArray` {.método-colección}

El método `replaceArray` reemplaza un valor dado en la cadena secuencialmente usando un array:

    use Illuminate\Support\Str;

    $string = 'The event will take place between ? and ?';

    $replaced = Str::of($string)->replaceArray('?', ['8:30', '9:00']);

    // The event will take place between 8:30 and 9:00

[]()

#### `replaceFirst` {.método-colección}

El método `replaceFirst` reemplaza la primera aparición de un valor dado en una cadena:

    use Illuminate\Support\Str;

    $replaced = Str::of('the quick brown fox jumps over the lazy dog')->replaceFirst('the', 'a');

    // a quick brown fox jumps over the lazy dog

[]()

#### `replaceLast` {.método-colección}

El método `replaceLast` reemplaza la última aparición de un valor dado en una cadena:

    use Illuminate\Support\Str;

    $replaced = Str::of('the quick brown fox jumps over the lazy dog')->replaceLast('the', 'a');

    // the quick brown fox jumps over a lazy dog

[]()

#### `replaceMatches` {.método-colección}

El método `replaceMatches` sustituye todas las partes de una cadena que coincidan con un patrón por la cadena de sustitución dada:

    use Illuminate\Support\Str;

    $replaced = Str::of('(+1) 501-555-1000')->replaceMatches('/[^A-Za-z0-9]++/', '')

    // '15015551000'

El método `replaceMatches` también acepta un closure que será invocado con cada porción de la cadena que coincida con el patrón dado, permitiéndole realizar la lógica de reemplazo dentro del closure y devolver el valor reemplazado:

    use Illuminate\Support\Str;

    $replaced = Str::of('123')->replaceMatches('/\d/', function ($match) {
        return '['.$match[0].']';
    });

    // '[1][2][3]'

[]()

#### `rtrim` {.método-colección}

El método `rtrim` recorta el lado derecho de la cadena dada:

    use Illuminate\Support\Str;

    $string = Str::of('  Laravel  ')->rtrim();

    // '  Laravel'

    $string = Str::of('/Laravel/')->rtrim('/');

    // '/Laravel'

[]()

#### `scan` {.método-colección}

El método `scan` analiza la entrada de una cadena en una colección de acuerdo a un formato soportado por la [función PHP`sscanf`](https://www.php.net/manual/en/function.sscanf.php):

    use Illuminate\Support\Str;

    $collection = Str::of('filename.jpg')->scan('%[^.].%s');

    // collect(['filename', 'jpg'])

[]()

#### `singular` {.método-colección}

El método `singular` convierte una cadena a su forma singular. Esta función soporta [cualquiera de los lenguajes soportados por el pluralizador de Laravel](/docs/%7B%7Bversion%7D%7D/localization#pluralization-language):

    use Illuminate\Support\Str;

    $singular = Str::of('cars')->singular();

    // car

    $singular = Str::of('children')->singular();

    // child

[]()

#### `slug` {.método-colección}

El método `slug` genera una URL amigable "slug" a partir de la cadena dada:

    use Illuminate\Support\Str;

    $slug = Str::of('Laravel Framework')->slug('-');

    // laravel-framework

[]()

#### `snake` {.método-colección}

El método `snake` convierte la cadena dada a `snake_case`:

    use Illuminate\Support\Str;

    $converted = Str::of('fooBar')->snake();

    // foo_bar

[]()

#### `split` {.método-colección}

El método `split` divide una cadena en una colección utilizando una expresión regular:

    use Illuminate\Support\Str;

    $segments = Str::of('one, two, three')->split('/[\s,]+/');

    // collect(["one", "two", "three"])

[]()

#### `squish` {.método-colección}

El método `squish` elimina todos los espacios en blanco extraños de una cadena, incluidos los espacios en blanco extraños entre palabras:

    use Illuminate\Support\Str;

    $string = Str::of('    laravel    framework    ')->squish();

    // laravel framework

[]()

#### `start` {.método-colección}

El método `start` añade una única instancia del valor dado a una cadena si no empieza ya con ese valor:

    use Illuminate\Support\Str;

    $adjusted = Str::of('this/string')->start('/');

    // /this/string

    $adjusted = Str::of('/this/string')->start('/');

    // /this/string

[]()

#### `startsWith` {.método-colección}

El método `startsWith` determina si la cadena dada empieza por el valor dado:

    use Illuminate\Support\Str;

    $result = Str::of('This is my name')->startsWith('This');

    // true

[]()

#### `studly` {.método-colección}

El método `studly` convierte la cadena dada a `StudlyCase`:

    use Illuminate\Support\Str;

    $converted = Str::of('foo_bar')->studly();

    // FooBar

[]()

#### `substr` {.método-colección}

El método `substr` devuelve la parte de la cadena especificada por los parámetros start y length dados:

    use Illuminate\Support\Str;

    $string = Str::of('Laravel Framework')->substr(8);

    // Framework

    $string = Str::of('Laravel Framework')->substr(8, 5);

    // Frame

[]()

#### `substrReplace` {.método-colección}

El método `substrReplace` sustituye texto dentro de una parte de una cadena, empezando en la posición especificada por el segundo argumento y sustituyendo el número de caracteres especificado por el tercer argumento. Si se pasa `0` al tercer argumento del método, se insertará la cadena en la posición especificada sin reemplazar ninguno de los caracteres existentes en la cadena:

    use Illuminate\Support\Str;

    $string = Str::of('1300')->substrReplace(':', 2);

    // 13:

    $string = Str::of('The Framework')->substrReplace(' Laravel', 3, 0);

    // The Laravel Framework

[]()

#### `swap` {.método-colección}

El método `swap` reemplaza múltiples valores en la cadena usando la función `strtr` de PHP:

    use Illuminate\Support\Str;

    $string = Str::of('Tacos are great!')
        ->swap([
            'Tacos' => 'Burritos',
            'great' => 'fantastic',
        ]);

    // Burritos are fantastic!

[]()

#### `tap` {.método-colección}

El método `tap` pasa la cadena al closure dado, permitiéndole examinar e interactuar con la cadena sin afectar a la propia cadena. La cadena original es devuelta por el método `tap` independientemente de lo que sea devuelto por el closure:

    use Illuminate\Support\Str;

    $string = Str::of('Laravel')
        ->append(' Framework')
        ->tap(function ($string) {
            dump('String after append: '.$string);
        })
        ->upper();

    // LARAVEL FRAMEWORK

[]()

#### `test` {.método-colección}

El método `test` determina si una cadena coincide con el patrón de expresión regular dado:

    use Illuminate\Support\Str;

    $result = Str::of('Laravel Framework')->test('/Laravel/');

    // true

[]()

#### `title` {.método-colección}

El método `title` convierte la cadena dada a `Title Case`:

    use Illuminate\Support\Str;

    $converted = Str::of('a nice title uses the correct case')->title();

    // A Nice Title Uses The Correct Case

[]()

#### `trim` {.método-colección}

El método `trim` recorta la cadena dada:

    use Illuminate\Support\Str;

    $string = Str::of('  Laravel  ')->trim();

    // 'Laravel'

    $string = Str::of('/Laravel/')->trim('/');

    // 'Laravel'

[]()

#### `ucfirst` {.método-colección}

El método `ucfirst` devuelve la cadena dada con el primer carácter en mayúscula:

    use Illuminate\Support\Str;

    $string = Str::of('foo bar')->ucfirst();

    // Foo bar

[]()

#### `ucsplit` {.método-colección}

El método ucsplit divide la cadena dada en una colección por caracteres en mayúsculas:

    use Illuminate\Support\Str;

    $string = Str::of('Foo Bar')->ucsplit();

    // collect(['Foo', 'Bar'])

[]()

#### `upper` {.método-colección}

El método `upper` convierte la cadena dada a mayúsculas:

    use Illuminate\Support\Str;

    $adjusted = Str::of('laravel')->upper();

    // LARAVEL

[]()

#### `when` {.método-colección}

El método `when` invoca el closure dado si una condición dada es `verdadera`. El closure recibirá la instancia de la cadena fluent:

    use Illuminate\Support\Str;

    $string = Str::of('Taylor')
                    ->when(true, function ($string) {
                        return $string->append(' Otwell');
                    });

    // 'Taylor Otwell'

Si es necesario, puede pasar otro closure como tercer parámetro al método `when`. Este closure se ejecutará si el parámetro de la condición se evalúa como `falso`.

[]()

#### `whenContains` {.método-colección}

El método `whenContains` invoca el closure dado si la cadena contiene el valor dado. El closure recibirá la instancia fluent string:

    use Illuminate\Support\Str;

    $string = Str::of('tony stark')
                ->whenContains('tony', function ($string) {
                    return $string->title();
                });

    // 'Tony Stark'

Si es necesario, puede pasar otro closure como tercer parámetro al método `when`. Este closure se ejecutará si la cadena no contiene el valor dado.

También puede pasar una array de valores para determinar si la cadena dada contiene alguno de los valores de la array:

    use Illuminate\Support\Str;

    $string = Str::of('tony stark')
                ->whenContains(['tony', 'hulk'], function ($string) {
                    return $string->title();
                });

    // Tony Stark

[]()

#### `whenContainsAll` {.método-colección}

El método `whenContainsAll` invoca el closure dado si la cadena contiene todas las subcadenas dadas. El closure recibirá la instancia de cadena fluida:

    use Illuminate\Support\Str;

    $string = Str::of('tony stark')
                    ->whenContainsAll(['tony', 'stark'], function ($string) {
                        return $string->title();
                    });

    // 'Tony Stark'

Si es necesario, puede pasar otro closure como tercer parámetro al método `when`. Este closure se ejecutará si el parámetro de condición es `falso`.

[]()

#### `whenEmpty` {.método-colección}

El método `whenEmpty` invoca el closure dado si la cadena está vacía. Si el closure devuelve un valor, éste también será devuelto por el método `whenEmpty`. Si el closure no devuelve ningún valor, se devolverá la instancia de cadena fluida:

    use Illuminate\Support\Str;

    $string = Str::of('  ')->whenEmpty(function ($string) {
        return $string->trim()->prepend('Laravel');
    });

    // 'Laravel'

[]()

#### `whenNotEmpty` {.método-colección}

El método `whenNotEmpty` invoca el closure dado si la cadena no está vacía. Si el closure devuelve un valor, éste también será devuelto por el método `whenNotEmpty`. Si el closure no devuelve ningún valor, se devolverá la instancia de cadena fluida:

    use Illuminate\Support\Str;

    $string = Str::of('Framework')->whenNotEmpty(function ($string) {
        return $string->prepend('Laravel ');
    });

    // 'Laravel Framework'

[]()

#### `whenStartsWith` {.método-colección}

El método `whenStartsWith` invoca el closure dado si la cadena comienza con la subcadena dada. El closure recibirá la instancia de cadena fluida:

    use Illuminate\Support\Str;

    $string = Str::of('disney world')->whenStartsWith('disney', function ($string) {
        return $string->title();
    });

    // 'Disney World'

[]()

#### `whenEndsWith` {.método-colección}

El método `whenEndsWith` invoca el closure dado si la cadena termina con la subcadena dada. El closure recibirá la instancia de cadena fluida:

    use Illuminate\Support\Str;

    $string = Str::of('disney world')->whenEndsWith('world', function ($string) {
        return $string->title();
    });

    // 'Disney World'

[]()

#### `whenExactly` {.método-colección}

El método `whenExactly` invoca el closure dado si la cadena coincide exactamente con la cadena dada. El closure recibirá la instancia de cadena fluida:

    use Illuminate\Support\Str;

    $string = Str::of('laravel')->whenExactly('laravel', function ($string) {
        return $string->title();
    });

    // 'Laravel'

[]()

#### `whenNotExactly` {.método-colección}

El método `whenNotExactly` invoca el closure dado si la cadena no coincide exactamente con la cadena dada. El closure recibirá la instancia de cadena fluida:

    use Illuminate\Support\Str;

    $string = Str::of('framework')->whenNotExactly('laravel', function ($string) {
        return $string->title();
    });

    // 'Framework'

[]()

#### `whenIs` {.método-colección}

El método `whenIs` invoca el closure dado si la cadena coincide con un patrón dado. Se pueden utilizar asteriscos como comodines. El closure recibirá la instancia de cadena fluida:

    use Illuminate\Support\Str;

    $string = Str::of('foo/bar')->whenIs('foo/*', function ($string) {
        return $string->append('/baz');
    });

    // 'foo/bar/baz'

[]()

#### `whenIsAscii` {.método-colección}

El método `whenIsAscii` invoca el closure dado si la cadena es ASCII de 7 bits. El closure recibirá la instancia de cadena fluida:

    use Illuminate\Support\Str;

    $string = Str::of('laravel')->whenIsAscii(function ($string) {
        return $string->title();
    });

    // 'Laravel'

[]()

#### `whenIsUlid` {.método-colección}

El método `whenIsUlid` invoca el closure dado si la cadena es un ULID válido. El closure recibirá la instancia de cadena fluida:

    use Illuminate\Support\Str;

    $string = Str::of('01gd6r360bp37zj17nxb55yv40')->whenIsUlid(function ($string) {
        return $string->substr(0, 8);
    });

    // '01gd6r36'

[]()

#### `whenIsUuid` {.método-colección}

El método `whenIsUuid` invoca el closure dado si la cadena es un UUID válido. El closure recibirá la instancia de cadena fluida:

    use Illuminate\Support\Str;

    $string = Str::of('a0a2a2d2-0b87-4a18-83f2-2529882be2de')->whenIsUuid(function ($string) {
        return $string->substr(0, 8);
    });

    // 'a0a2a2d2'

[]()

#### `whenTest` {.método-colección}

El método `whenTest` invoca el closure dado si la cadena coincide con la expresión regular dada. El closure recibirá la instancia de cadena fluida:

    use Illuminate\Support\Str;

    $string = Str::of('laravel framework')->whenTest('/laravel/', function ($string) {
        return $string->title();
    });

    // 'Laravel Framework'

[]()

#### `wordCount` {.método-colección}

El método `wordCount` devuelve el número de palabras que contiene una cadena:

```php
use Illuminate\Support\Str;

Str::of('Hello, world!')->wordCount(); // 2
```

[]()

#### `words` {.método-colección}

El método `words` limita el número de palabras de una cadena. Si es necesario, puede especificar una cadena adicional que se añadirá a la cadena truncada:

    use Illuminate\Support\Str;

    $string = Str::of('Perfectly balanced, as all things should be.')->words(3, ' >>>');

    // Perfectly balanced, as >>>

[]()

## URLs

[]()

#### `action()` {.método-colección}

La función `action` genera una URL para la acción del controlador dada:

    use App\Http\Controllers\HomeController;

    $url = action([HomeController::class, 'index']);

Si el método acepta parámetros de ruta, puede pasarlos como segundo argumento al método:

    $url = action([UserController::class, 'profile'], ['id' => 1]);

[]()

#### asset(`)` {.método-colección}

La función `asset` genera una URL para un asset utilizando el esquema actual de la petición (HTTP o HTTPS):

    $url = asset('img/photo.jpg');

Puede configurar el host de la URL de asset estableciendo la variable `ASSET_URL` en su archivo `.env`. Esto puede ser útil si aloja sus activos en un servicio externo como Amazon S3 u otro CDN:

    // ASSET_URL=http://example.com/assets

    $url = asset('img/photo.jpg'); // http://example.com/assets/img/photo.jpg

[]()

#### `route()` {.collection-method}

La función `route` genera una URL para una [ruta](/docs/%7B%7Bversion%7D%7D/routing#named-routes) dada:

    $url = route('route.name');

Si la ruta acepta parámetros, puede pasarlos como segundo argumento a la función:

    $url = route('route.name', ['id' => 1]);

Por defecto, la función `route` genera una URL absoluta. Si desea generar una URL relativa, puede pasar `false` como tercer argumento a la función:

    $url = route('route.name', ['id' => 1], false);

[]()

#### secure_asset`()` {.collection-method}

La función `secure_asset` genera una URL para un activo utilizando HTTPS:

    $url = secure_asset('img/photo.jpg');

[]()

#### `secure_url()` {.collection-method}

La función `secure_url` genera una URL HTTPS completa para la ruta indicada. Se pueden pasar segmentos de URL adicionales en el segundo argumento de la función:

    $url = secure_url('user/profile');

    $url = secure_url('user/profile', [1]);

[]()

#### `to_route()` {.collection-method}

La función `to_route` genera una [respuesta HTTP de redirección](/docs/%7B%7Bversion%7D%7D/responses#redirects) para una [ruta](/docs/%7B%7Bversion%7D%7D/routing#named-routes) determinada:

    return to_route('users.show', ['user' => 1]);

Si es necesario, puede pasar el código de estado HTTP que debe asignarse a la redirección y cualquier cabecera de respuesta adicional como tercer y cuarto argumento del método `to_route`:

    return to_route('users.show', ['user' => 1], 302, ['X-Framework' => 'Laravel']);

[]()

#### `url()` {.collection-method}

La función `url` genera una URL completa para la ruta dada:

    $url = url('user/profile');

    $url = url('user/profile', [1]);

Si no se proporciona ninguna ruta, se devuelve una instancia de `Illuminate\Routing\UrlGenerator`:

    $current = url()->current();

    $full = url()->full();

    $previous = url()->previous();

[]()

## Varios

[]()

#### `abort()` {.método-colección}

La función `abort` lanza [una excepción HT](/docs/%7B%7Bversion%7D%7D/errors#http-exceptions) TP que será procesada por el [gestor de excepciones](/docs/%7B%7Bversion%7D%7D/errors#the-exception-handler):

    abort(403);

También puede proporcionar el mensaje de la excepción y las cabeceras de respuesta HTTP personalizadas que deben enviarse al navegador:

    abort(403, 'Unauthorized.', $headers);

[]()

#### `abort_if()` {.collection-method}

La función `abort_if` lanza una excepción HTTP si una expresión booleana dada se evalúa como `verdadera`:

    abort_if(! Auth::user()->isAdmin(), 403);

Al igual que el método `abort`, también puede proporcionar el texto de respuesta de la excepción como tercer argumento y una array de cabeceras de respuesta personalizadas como cuarto argumento de la función.

[]()

#### abort_unless`()` {.collection-method}

La función `abort_unless` lanza una excepción HTTP si una expresión booleana dada se evalúa como `false`:

    abort_unless(Auth::user()->isAdmin(), 403);

Al igual que en el método `abort`, también puede proporcionar el texto de respuesta de la excepción como tercer argumento y una array de encabezados de respuesta personalizados como cuarto argumento de la función.

[]()

#### `app()` {.método-colección}

La función `app` devuelve la instancia del [contenedor de servicios](/docs/%7B%7Bversion%7D%7D/container):

    $container = app();

Puede pasar un nombre de clase o interfaz para resolverlo desde el contenedor:

    $api = app('HelpSpot\API');

[]()

#### `auth()` {.collection-method}

La función `auth` devuelve una instancia [authenticator](/docs/%7B%7Bversion%7D%7D/authentication). Puede utilizarla como alternativa a la facade `Auth`:

    $user = auth()->user();

Si es necesario, puede especificar a qué instancia de guarda desea acceder:

    $user = auth('admin')->user();

[]()

#### `back()` {.método-colección}

La función `back` genera una [respuesta HTTP de redirección](/docs/%7B%7Bversion%7D%7D/responses#redirects) a la ubicación anterior del usuario:

    return back($status = 302, $headers = [], $fallback = '/');

    return back();

[]()

#### `bcrypt` () {.método-colección}

La función `bcrypt` [realiza el hash](/docs/%7B%7Bversion%7D%7D/hashing) del valor dado utilizando Bcrypt. Puede utilizar esta función como alternativa a la facade `Hash`:

    $password = bcrypt('my-secret-password');

[]()

#### `blank` () {.método-colección}

La función `blank` determina si el valor dado está "en blanco":

    blank('');
    blank('   ');
    blank(null);
    blank(collect());

    // true

    blank(0);
    blank(true);
    blank(false);

    // false

Para la inversa de `blank`, véase el método [`filled`](#method-filled).

[]()

#### `broadcast` () {.método-colección}

La función `broadcast` [transmite](/docs/%7B%7Bversion%7D%7D/broadcasting) el [evento](/docs/%7B%7Bversion%7D%7D/events) dado a sus oyentes:

    broadcast(new UserRegistered($user));

    broadcast(new UserRegistered($user))->toOthers();

[]()

#### `cache` (`)` {.método-colección}

La función `cache` puede utilizarse para obtener valores de la [cache](/docs/%7B%7Bversion%7D%7D/cache). Si la clave dada no existe en la cache, se devolverá un valor por defecto opcional:

    $value = cache('key');

    $value = cache('key', 'default');

Puede añadir elementos a la cache pasando una array de pares clave / valor a la función. También debe pasar el número de segundos o duración que el valor almacenado en caché debe considerarse válido:

    cache(['key' => 'value'], 300);

    cache(['key' => 'value'], now()->addSeconds(10));

[]()

#### `class_uses_recursive()` {.collection-method}

La función `class_uses_recursive` devuelve todos los traits utilizados por una clase, incluyendo los traits utilizados por todas sus clases padre:

    $traits = class_uses_recursive(App\Models\User::class);

[]()

#### `collect()` {.método-colección}

La función `collect` crea una instancia de [colección](/docs/%7B%7Bversion%7D%7D/collections) a partir del valor dado:

    $collection = collect(['taylor', 'abigail']);

[]()

#### `config()` {.método-de-colección}

La función `config` obtiene el valor de una variable de [configuración](/docs/%7B%7Bversion%7D%7D/configuration). Se puede acceder a los valores de configuración utilizando la sintaxis "dot", que incluye el nombre del archivo y la opción a la que se desea acceder. Se puede especificar un valor por defecto, que se devuelve si la opción de configuración no existe:

    $value = config('app.timezone');

    $value = config('app.timezone', $default);

Puede establecer variables de configuración en tiempo de ejecución pasando una array de pares clave / valor. Sin embargo, tenga en cuenta que esta función sólo afecta al valor de configuración para la solicitud actual y no actualiza sus valores de configuración reales:

    config(['app.debug' => true]);

[]()

#### `cookie()` {.collection-method}

La función `cookie` crea una nueva instancia de [cookie](/docs/%7B%7Bversion%7D%7D/requests#cookies):

    $cookie = cookie('name', 'value', $minutes);

[]()

#### `csrf_field` () {.collection-method}

La función `csrf_field` genera un campo de entrada `oculto` HTML que contiene el valor del token CSRF. Por ejemplo, utilizando [la sintaxis Blade](/docs/%7B%7Bversion%7D%7D/blade):

    {{ csrf_field() }}

[]()

#### `csrf_token` () {.collection-method}

La función `csrf_token` recupera el valor del token CSRF actual:

    $token = csrf_token();

[]()

#### `decrypt()` {.método-colección}

La función `decrypt` [descifra](/docs/%7B%7Bversion%7D%7D/encryption) el valor dado. Puede utilizar esta función como alternativa a la facade `Crypt`:

    $password = decrypt($value);

[]()

#### dd`()` {.método-colección}

La función `dd` vuelca las variables dadas y finaliza la ejecución del script:

    dd($value);

    dd($value1, $value2, $value3, ...);

Si no desea detener la ejecución de su script, utilice en su lugar la función [`dump`](#method-dump).

[]()

#### `dispatch()` {.método-colección}

La función `dispatch` empuja el [trabajo](/docs/%7B%7Bversion%7D%7D/queues#creating-jobs) dado a la [cola de trabajos de](/docs/%7B%7Bversion%7D%7D/queues) Laravel:

    dispatch(new App\Jobs\SendEmails);

[]()

#### `dump()` {.método-colección}

La función `dump` vuelca las variables dadas:

    dump($value);

    dump($value1, $value2, $value3, ...);

Si desea detener la ejecución del script después de volcar las variables, utilice la función [`dd`](#method-dd) en su lugar.

[]()

#### `encrypt` () {.método-colección}

La función `encrypt` [cifra](/docs/%7B%7Bversion%7D%7D/encryption) el valor dado. Puedes usar esta función como alternativa a la facade `Crypt`:

    $secret = encrypt('my-secret-value');

[]()

#### `env` () {.método-colección}

La función `env` recupera el valor de una [variable de entorno](/docs/%7B%7Bversion%7D%7D/configuration#environment-configuration) o devuelve un valor por defecto:

    $env = env('APP_ENV');

    $env = env('APP_ENV', 'production');

> **Advertencia**  
> Si ejecutas el comando `config:cache` durante tu proceso de despliegue, debes asegurarte de que sólo estás llamando a la función `env` desde dentro de tus archivos de configuración. Una vez que la configuración ha sido cacheada, el archivo `.env` no será cargado y todas las llamadas a la función `env` devolverán `null`.

[]()

#### `event(` ) {.método-colección}

La función `event` envía el [evento](/docs/%7B%7Bversion%7D%7D/events) dado a sus oyentes:

    event(new UserRegistered($user));

[]()

#### `fake()` {.método-colección}

La función `fake` resuelve un singleton [Faker](https://github.com/FakerPHP/Faker) del contenedor, lo que puede ser útil al crear datos falsos en fábricas de modelos, siembra de bases de datos, tests y prototipado de vistas:

```blade
@for($i = 0; $i < 10; $i++)
    <dl>
        <dt>Name</dt>
        <dd>{{ fake()->name() }}</dd>

        <dt>Email</dt>
        <dd>{{ fake()->unique()->safeEmail() }}</dd>
    </dl>
@endfor
```

Por defecto, la función `fake` utilizará la opción de configuración `app.faker_locale` en su fichero de configuración `config/app.` php; sin embargo, también puede especificar la configuración regional pasándola a la función `fake`. Cada configuración regional resolverá un singleton individual:

    fake('nl_NL')->name()

[]()

#### `filled` () {.colección-método}

La función `filled` determina si el valor dado no está "en blanco":

    filled(0);
    filled(true);
    filled(false);

    // true

    filled('');
    filled('   ');
    filled(null);
    filled(collect());

    // false

Para la inversa de `filled`, véase el método [`blank`](#method-blank).

[]()

#### `info()` {.método-colección}

La función `info` escribirá información en el [registro de](/docs/%7B%7Bversion%7D%7D/logging) su aplicación:

    info('Some helpful information!');

También se puede pasar a la función una array de datos contextuales:

    info('User login attempt failed.', ['id' => $user->id]);

[]()

#### `logger` () {.colección-método}

La función `logger` puede utilizarse para escribir un mensaje de nivel de `depuración` en el [log](/docs/%7B%7Bversion%7D%7D/logging):

    logger('Debug message');

También se puede pasar a la función una array de datos contextuales:

    logger('User has logged in.', ['id' => $user->id]);

Se devolverá una instancia de [logger](/docs/%7B%7Bversion%7D%7D/errors#logging) si no se pasa ningún valor a la función:

    logger()->error('You are not allowed here.');

[]()

#### `method_field` () {.método-colección}

La función `method_field` genera un campo de entrada `oculto` HTML que contiene el valor falsificado del verbo HTTP del formulario. Por ejemplo, utilizando [la sintaxis de Blade](/docs/%7B%7Bversion%7D%7D/blade):

    <form method="POST">
        {{ method_field('DELETE') }}
    </form>

[]()

#### now(`)` {.método-colección}

La función `now` crea una nueva instancia `Illuminate\Support\Carbon` para la hora actual:

    $now = now();

[]()

#### `old` () {.método-colección}

La función `old` [recupera](/docs/%7B%7Bversion%7D%7D/requests#retrieving-input) un valor de [entrada antiguo](/docs/%7B%7Bversion%7D%7D/requests#old-input) introducido en la sesión:

    $value = old('value');

    $value = old('value', 'default');

Dado que el "valor por defecto" proporcionado como segundo argumento a la función `old` es a menudo un atributo de un modelo Eloquent, Laravel le permite simplemente pasar todo el modelo Eloquent como segundo argumento a la función `old`. Al hacerlo, Laravel asumirá que el primer argumento proporcionado a la función `antigua` es el nombre del atributo de Eloquent que debe considerarse el "valor por defecto":

    {{ old('name', $user->name) }}

    // Is equivalent to...

    {{ old('name', $user) }}

[]()

#### optional(`)` {.método-colección}

La función `optional` acepta cualquier argumento y permite acceder a propiedades o llamar a métodos de ese objeto. Si el objeto dado es `null`, las propiedades y métodos devolverán `null` en lugar de provocar un error:

    return optional($user->address)->street;

    {!! old('name', optional($user)->name) !!}

La función `opcional` también acepta un closure como segundo argumento. El closure se invocará si el valor proporcionado como primer argumento no es nulo:

    return optional(User::find($id), function ($user) {
        return $user->name;
    });

[]()

#### `policy` () {.colección-método}

El método `policy` recupera una instancia de [policy](/docs/%7B%7Bversion%7D%7D/authorization#creating-policies) para una clase dada:

    $policy = policy(App\Models\User::class);

[]()

#### `redirect` () {.método-colección}

La función `redirect` devuelve una [respuesta HTTP de redirección](/docs/%7B%7Bversion%7D%7D/responses#redirects), o devuelve la instancia del redirector si se llama sin argumentos:

    return redirect($to = null, $status = 302, $headers = [], $https = null);

    return redirect('/home');

    return redirect()->route('route.name');

[]()

#### `report` () {.método-colección}

La función `report` informará de una excepción utilizando su [manejador de excepciones](/docs/%7B%7Bversion%7D%7D/errors#the-exception-handler):

    report($e);

La función `report` también acepta una cadena como argumento. Cuando se da una cadena a la función, ésta creará una excepción con la cadena dada como mensaje:

    report('Something went wrong.');

[]()

#### `report_if` () {.método-colección}

La función `report_if` informará de una excepción utilizando su [gestor](/docs/%7B%7Bversion%7D%7D/errors#the-exception-handler) de excepciones si la condición dada es `verdadera`:

    report_if($shouldReport, $e);

    report_if($shouldReport, 'Something went wrong.');

[]()

#### `report_unless` () {.método-colección}

La función `report_unless` informará de una excepción utilizando su [gestor](/docs/%7B%7Bversion%7D%7D/errors#the-exception-handler) de excepciones si la condición dada es `falsa`:

    report_unless($reportingDisabled, $e);

    report_unless($reportingDisabled, 'Something went wrong.');

[]()

#### `request` () {.método-colección}

La función `request` devuelve la instancia de [petición](/docs/%7B%7Bversion%7D%7D/requests) actual u obtiene el valor de un campo de entrada de la petición actual:

    $request = request();

    $value = request('key', $default);

[]()

#### `rescue` () {.método-colección}

La función `rescue` ejecuta el closure dado y captura cualquier excepción que ocurra durante su ejecución. Todas las excepciones capturadas serán enviadas a su [manejador de excepciones](/docs/%7B%7Bversion%7D%7D/errors#the-exception-handler); sin embargo, la petición continuará procesándose:

    return rescue(function () {
        return $this->method();
    });

También puede pasar un segundo argumento a la función de `rescate`. Este argumento será el valor "por defecto" que se devolverá si se produce una excepción durante la ejecución del closure:

    return rescue(function () {
        return $this->method();
    }, false);

    return rescue(function () {
        return $this->method();
    }, function () {
        return $this->failure();
    });

[]()

#### `resolve` () {.método-colección}

La función `resolver` resuelve un nombre de clase o interfaz dado a una instancia utilizando el [contenedor de servicios](/docs/%7B%7Bversion%7D%7D/container):

    $api = resolve('HelpSpot\API');

[]()

#### `response` () {.método-colección}

La función `response` crea una instancia de [respuesta](/docs/%7B%7Bversion%7D%7D/responses) u obtiene una instancia de la fábrica de respuestas:

    return response('Hello World', 200, $headers);

    return response()->json(['foo' => 'bar'], 200, $headers);

[]()

#### retry(`)` {.método-colección}

La función `retry` intenta ejecutar la llamada de retorno dada hasta que se alcanza el umbral máximo de intentos dado. Si la llamada de retorno no lanza una excepción, se devolverá su valor. Si la llamada de retorno lanza una excepción, se reintentará automáticamente. Si se supera el número máximo de intentos, se lanzará la excepción:

    return retry(5, function () {
        // Attempt 5 times while resting 100ms between attempts...
    }, 100);

Si desea calcular manualmente el número de milisegundos que deben transcurrir entre los intentos, puede pasar un closure como tercer argumento a la función de `reintento`:

    return retry(5, function () {
        // ...
    }, function ($attempt, $exception) {
        return $attempt * 100;
    });

Para mayor comodidad, puede proporcionar un array como primer argumento de la función `retry`. Esta array se utilizará para determinar cuántos milisegundos deben transcurrir entre los siguientes intentos:

    return retry([100, 200], function () {
        // Sleep for 100ms on first retry, 200ms on second retry...
    });

Para reintentar sólo bajo condiciones específicas, puedes pasar un closure como cuarto argumento a la función `retry`:

    return retry(5, function () {
        // ...
    }, 100, function ($exception) {
        return $exception instanceof RetryException;
    });

[]()

#### `session()` {.collection-method}

La función `session` puede utilizarse para obtener o establecer valores de [sesión](/docs/%7B%7Bversion%7D%7D/session):

    $value = session('key');

Puede establecer valores pasando un array de pares clave / valor a la función:

    session(['chairs' => 7, 'instruments' => 3]);

El almacén de sesiones se devolverá si no se pasa ningún valor a la función:

    $value = session()->get('key');

    session()->put('key', $value);

[]()

#### tap(`)` {.collection-method}

La función `tap` acepta dos argumentos: un `$valor` arbitrario y un closure. El `$valor` será pasado al closure y luego devuelto por la función `tap`. El valor de retorno del closure es irrelevante:

    $user = tap(User::first(), function ($user) {
        $user->name = 'taylor';

        $user->save();
    });

Si no se pasa ningún closure a la función `tap`, puede llamar a cualquier método con el `valor`\$ dado. El valor de retorno del método al que llame siempre será `$valor`, independientemente de lo que el método devuelva realmente en su definición. Por ejemplo, el método `update` de Eloquent normalmente devuelve un entero. Sin embargo, podemos forzar que el método devuelva el propio modelo encadenando la llamada al método `update` a través de la función `tap`:

    $user = tap($user)->update([
        'name' => $name,
        'email' => $email,
    ]);

Para añadir un método `tap` a una clase, puede añadir el rasgo `Illuminate\Support\Traits\Tappable` a la clase. El método `tap` de este rasgo acepta un closure como único argumento. La propia instancia del objeto se pasará al closure y luego será devuelta por el método `tap`:

    return $user->tap(function ($user) {
        //
    });

[]()

#### throw_if`()` {.collection-method}

La función `throw_if` lanza la excepción dada si una expresión booleana dada se evalúa como `verdadera`:

    throw_if(! Auth::user()->isAdmin(), AuthorizationException::class);

    throw_if(
        ! Auth::user()->isAdmin(),
        AuthorizationException::class,
        'You are not allowed to access this page.'
    );

[]()

#### `throw_unless()` {.método-colección}

La función `throw_unless` lanza la excepción dada si una expresión booleana dada es `falsa`:

    throw_unless(Auth::user()->isAdmin(), AuthorizationException::class);

    throw_unless(
        Auth::user()->isAdmin(),
        AuthorizationException::class,
        'You are not allowed to access this page.'
    );

[]()

#### `today()` {.método-colección}

La función `today` crea una nueva instancia de `Illuminate\Support\Carbon` para la fecha actual:

    $today = today();

[]()

#### `trait_uses_recursive()` {.método-colección}

La función `trait_uses_recursive` devuelve todos los traits utilizados por un trait:

    $traits = trait_uses_recursive(\Illuminate\Notifications\Notifiable::class);

[]()

#### `transform()` {.método-colección}

La función `transform` ejecuta un closure sobre un valor dado si el valor no [está en blanco](#method-blank) y luego devuelve el valor de retorno del closure:

    $callback = function ($value) {
        return $value * 2;
    };

    $result = transform(5, $callback);

    // 10

Se puede pasar un valor por defecto o un closure como tercer argumento de la función. Este valor se devolverá si el valor dado está vacío:

    $result = transform(null, $callback, 'The value is blank');

    // The value is blank

[]()

#### `validator` () {.método-colección}

La función `validador` crea una nueva instancia de [validador](/docs/%7B%7Bversion%7D%7D/validation) con los argumentos dados. Se puede utilizar como alternativa a la facade `Validator`:

    $validator = validator($data, $rules, $messages);

[]()

#### value(`)` {.método-colección}

La función `value` devuelve el valor que se le da. Sin embargo, si se pasa un closure a la función, el closure se ejecutará y se devolverá su valor:

    $result = value(true);

    // true

    $result = value(function () {
        return false;
    });

    // false

[]()

#### `view()` {.método-colección}

La función `view` recupera una instancia de [vista](/docs/%7B%7Bversion%7D%7D/views):

    return view('auth.login');

[]()

#### `con` () {.método-colección}

La función `with` devuelve el valor que se le pasa. Si se pasa un closure como segundo argumento a la función, se ejecutará el closure y se devolverá su valor:

    $callback = function ($value) {
        return is_numeric($value) ? $value * 2 : 0;
    };

    $result = with(5, $callback);

    // 10

    $result = with(null, $callback);

    // 0

    $result = with(5, null);

    // 5

[]()

## Otras utilidades

[]()

### Evaluación comparativa

A veces es posible que desee test rápidamente el rendimiento de ciertas partes de su aplicación. En esas ocasiones, puedes utilizar la clase de soporte `Benchmark` para medir el número de milisegundos que tardan en completarse las llamadas de retorno dadas:

    <?php

    use App\Models\User;
    use Illuminate\Support\Benchmark;

    Benchmark::dd(fn () => User::find(1)); // 0.1 ms

    Benchmark::dd([
        'Scenario 1' => fn () => User::count(), // 0.5 ms
        'Scenario 2' => fn () => User::all()->count(), // 20.0 ms
    ]);

Por defecto, los callbacks dados se ejecutarán una vez (una iteración), y su duración se mostrará en el navegador / consola.

Para invocar una llamada de retorno más de una vez, puede especificar el número de iteraciones que la llamada de retorno debe ser invocada como segundo argumento del método. Cuando se ejecuta un callback más de una vez, la clase `Benchmark` devolverá la cantidad media de milisegundos que se tardó en ejecutar el callback en todas las iteraciones:

    Benchmark::dd(fn () => User::count(), iterations: 10); // 0.5 ms

[]()

### Lotería

La clase Lotería de Laravel puede utilizarse para ejecutar callbacks basados en un conjunto de probabilidades dadas. Esto puede ser particularmente útil cuando sólo se desea ejecutar código para un porcentaje de las peticiones entrantes:

    use Illuminate\Support\Lottery;

    Lottery::odds(1, 20)
        ->winner(fn () => $user->won())
        ->loser(fn () => $user->lost())
        ->choose();

Puede combinar la clase de lotería de Laravel con otras características de Laravel. Por ejemplo, es posible que sólo desee informar de un pequeño porcentaje de consultas lentas a su gestor de excepciones. Y, puesto que la clase lotería es invocable, podemos pasar una instancia de la clase a cualquier método que acepte invocables:

    use Carbon\CarbonInterval;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Lottery;

    DB::whenQueryingForLongerThan(
        CarbonInterval::seconds(2),
        Lottery::odds(1, 100)->winner(fn () => report('Querying > 2 seconds.')),
    );

[]()

#### Probando Loterías

Laravel proporciona algunos métodos simples que te permiten test fácilmente las invocaciones de lotería de tu aplicación:

    // Lottery will always win...
    Lottery::alwaysWin();

    // Lottery will always lose...
    Lottery::alwaysLose();

    // Lottery will win then lose, and finally return to normal behavior...
    Lottery::fix([true, false]);

    // Lottery will return to normal behavior...
    Lottery::determineResultsNormally();
