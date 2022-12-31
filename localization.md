# Localización

- [Introducción](#introduction)
  - [Configuración regional](#configuring-the-locale)
  - [Pluralización del lenguaje](#pluralization-language)
- [Definición de cadenas de traducción](#defining-translation-strings)
  - [Uso de claves abreviadas](#using-short-keys)
  - [Uso de cadenas de traducción como claves](#using-translation-strings-as-keys)
- [Recuperación de cadenas de traducción](#retrieving-translation-strings)
  - [Sustitución de parámetros en cadenas de traducción](#replacing-parameters-in-translation-strings)
  - [Pluralización](#pluralization)
- [Anulación de los archivos de idioma del paquete](#overriding-package-language-files)

[]()

## Introducción

Las características de localización de Laravel proporcionan una forma conveniente de recuperar cadenas en varios idiomas, permitiéndote soportar fácilmente múltiples idiomas dentro de tu aplicación.

Laravel proporciona dos formas de gestionar las cadenas de traducción. En primer lugar, las cadenas de idioma se pueden almacenar en archivos dentro del directorio `lang`. Dentro de este directorio, puede haber subdirectorios para cada idioma soportado por la aplicación. Este es el enfoque que Laravel utiliza para gestionar las cadenas de traducción para las características integradas de Laravel, como los mensajes de error de validación:

    /lang
        /en
            messages.php
        /es
            messages.php

O bien, las cadenas de traducción se pueden definir dentro de archivos JSON que se colocan dentro del directorio `lang`. Cuando se adopta este enfoque, cada idioma soportado por su aplicación tendría un archivo JSON correspondiente dentro de este directorio. Este enfoque se recomienda para aplicaciones que tienen un gran número de cadenas traducibles:

    /lang
        en.json
        es.json

Discutiremos cada aproximación a la gestión de cadenas de traducción dentro de esta documentación.

[]()

### Configuración de la localización

El idioma predeterminado para su aplicación se almacena en la opción de configuración `regional` del archivo de configuración `config/app.` php. Usted es libre de modificar este valor para adaptarlo a las necesidades de su aplicación.

Puede modificar el idioma predeterminado para una única petición HTTP en tiempo de ejecución utilizando el método `setLocale` proporcionado por la facade `App`:

    use Illuminate\Support\Facades\App;

    Route::get('/greeting/{locale}', function ($locale) {
        if (! in_array($locale, ['en', 'es', 'fr'])) {
            abort(400);
        }

        App::setLocale($locale);

        //
    });

Puede configurar un "idioma alternativo", que se utilizará cuando el idioma activo no contenga una cadena de traducción determinada. Al igual que el idioma por defecto, el idioma alternativo también se configura en el archivo de configuración `config/app.php`:

    'fallback_locale' => 'en',

[]()

#### Determinación de la configuración regional actual

Puede utilizar los métodos `currentLocale` e `isLocale` en la facade `App` para determinar la configuración regional actual o comprobar si la configuración regional es un valor dado:

    use Illuminate\Support\Facades\App;

    $locale = App::currentLocale();

    if (App::isLocale('en')) {
        //
    }

[]()

### Pluralización Idioma

Puedes indicar al "pluralizador" de Laravel, que es utilizado por Eloquent y otras partes del framework para convertir cadenas singulares en plurales, que utilice un idioma distinto del inglés. Esto puede lograrse invocando el método `useLanguage` dentro del método `boot` de uno de los proveedores de servicio de tu aplicación. Los idiomas actualmente soportados por el pluralizador son: `francés`, `noruego-bokmal`, `portugués`, `español` y `turco`:

    use Illuminate\Support\Pluralizer;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Pluralizer::useLanguage('spanish');     

        // ...     
    }

> **Advertencia**  
> Si personalizas el idioma del pluralizador, deberás definir explícitamente los [nombres de las tablas](/docs/%7B%7Bversion%7D%7D/eloquent#table-names) de tu modelo Eloquent.

[]()

## Definición de cadenas de traducción

[]()

### Uso de claves abreviadas

Normalmente, las cadenas de traducción se almacenan en archivos dentro del directorio `lang`. Dentro de este directorio, debería haber un subdirectorio para cada idioma soportado por tu aplicación. Este es el enfoque que Laravel utiliza para gestionar las cadenas de traducción para las características integradas de Laravel, como los mensajes de error de validación:

    /lang
        /en
            messages.php
        /es
            messages.php

Todos los archivos de idioma devuelven una array de cadenas con claves. Por ejemplo:

    <?php

    // lang/en/messages.php

    return [
        'welcome' => 'Welcome to our application!',
    ];

> **Advertencia**  
> En el caso de los idiomas que difieren según el territorio, debes nombrar los directorios de idioma según la norma ISO 15897. Por ejemplo, para el inglés británico debería utilizarse "en_GB" en lugar de "en-gb".

[]()

### Uso de cadenas de traducción como claves

Para aplicaciones con un gran número de cadenas traducibles, definir cada cadena con una "clave corta" puede resultar confuso a la hora de referenciar las claves en tus vistas y es engorroso inventar continuamente claves para cada cadena de traducción soportada por tu aplicación.

Por esta razón, Laravel también proporciona soporte para definir cadenas de traducción utilizando la traducción "por defecto" de la cadena como clave. Los archivos de traducción que utilizan cadenas de traducción como claves se almacenan como archivos JSON en el directorio `lang`. Por ejemplo, si tu aplicación tiene una traducción al español, debes crear un archivo `lang/es.json`:

```json
{
    "I love programming.": "Me encanta programar."
}
```

#### Conflictos de Clave / Archivo

No debe definir claves de cadenas de traducción que entren en conflicto con otros nombres de archivos de traducción. Por ejemplo, si traduce `__('Acción')` para la configuración regional "NL" mientras existe un archivo `nl/action.` php pero no existe un archivo `nl.j` son, el traductor devolverá el contenido de `nl/action.php`.

[]()

## Recuperación de cadenas de traducción

Puede recuperar cadenas de traducción de sus archivos de idioma utilizando la función de ayuda `__`. Si utiliza "claves cortas" para definir sus cadenas de traducción, debe pasar el archivo que contiene la clave y la propia clave a la función `__` utilizando la sintaxis "dot". Por ejemplo, recuperemos la cadena de traducción de `bienvenida` del archivo de idioma `lang/es/messages.php`:

    echo __('messages.welcome');

Si la cadena de traducción especificada no existe, la función `__` devolverá la clave de la cadena de traducción. Así, utilizando el ejemplo anterior, la función `__` devolvería `messages.welcome` si la cadena de traducción no existe.

Si está utilizando sus cadenas [de traducción predeterminadas como sus claves de traducción](#using-translation-strings-as-keys), debe pasar la traducción predeterminada de su cadena a la función `__`;

    echo __('I love programming.');

De nuevo, si la cadena de traducción no existe, la función `__` devolverá la clave de la cadena de traducción que se le haya dado.

Si está utilizando el [motor de plantillas Blade](/docs/%7B%7Bversion%7D%7D/blade), puede utilizar la sintaxis `{{ }}` echo para mostrar la cadena de traducción:

    {{ __('messages.welcome') }}

[]()

### Sustitución de parámetros en cadenas de traducción

Si lo desea, puede definir marcadores de posición en sus cadenas de traducción. Todos los marcadores de posición llevan el prefijo `:`. Por ejemplo, puede definir un mensaje de bienvenida con un nombre de marcador de posición:

    'welcome' => 'Welcome, :name',

Para sustituir los marcadores de posición al recuperar una cadena de traducción, puede pasar una array de sustituciones como segundo argumento a la función `__`:

    echo __('messages.welcome', ['name' => 'dayle']);

Si su marcador de posición contiene todas las letras mayúsculas, o sólo tiene su primera letra en mayúscula, el valor traducido se escribirá en mayúsculas en consecuencia:

    'welcome' => 'Welcome, :NAME', // Welcome, DAYLE
    'goodbye' => 'Goodbye, :Name', // Goodbye, Dayle

[]()

### Pluralización

La pluralización es un problema complejo, ya que los diferentes idiomas tienen una variedad de reglas complejas para la pluralización; sin embargo, Laravel puede ayudarle a traducir cadenas de forma diferente basándose en las reglas de pluralización que usted defina. Usando un carácter `|`, puede distinguir formas singulares y plurales de una cadena:

    'apples' => 'There is one apple|There are many apples',

Por supuesto, también se admite la pluralización cuando se utilizan cadenas de traducción [como claves](#using-translation-strings-as-keys):

```json
{
    "There is one apple|There are many apples": "Hay una manzana|Hay muchas manzanas"
}
```

Incluso puede crear reglas de pluralización más complejas que especifiquen cadenas de traducción para múltiples rangos de valores:

    'apples' => '{0} There are none|[1,19] There are some|[20,*] There are many',

Después de definir una cadena de traducción que tiene opciones de pluralización, puede utilizar la función `trans_choice` para recuperar la línea para un "recuento" dado. En este ejemplo, como la cuenta es mayor que uno, se devuelve la forma plural de la cadena de traducción:

    echo trans_choice('messages.apples', 10);

También puede definir atributos de marcador de posición en las cadenas de pluralización. Estos marcadores de posición pueden sustituirse pasando una array como tercer argumento a la función `trans_choice`:

    'minutes_ago' => '{1} :value minute ago|[2,*] :value minutes ago',

    echo trans_choice('time.minutes_ago', 5, ['value' => 5]);

Si desea mostrar el valor entero que se pasó a la función `trans_choice`, puede utilizar el marcador de posición `:count` incorporado:

    'apples' => '{0} There are none|{1} There is one|[2,*] There are :count',

[]()

## Anulación de archivos de idioma de paquetes

Algunos paquetes pueden incluir sus propios archivos de idioma. En lugar de cambiar los archivos principales del paquete para modificar estas líneas, puede anularlas colocando los archivos en el directorio `lang/vendor/{paquete}/{locale}`.

Así, por ejemplo, si necesitas anular las cadenas de traducción al inglés en `messages.php` para un paquete llamado `skyrim/hearthfire`, debes colocar un archivo de idioma en: `lang/vendor/hearthfire/en/messages.php`. Dentro de este archivo, sólo debes definir las cadenas de traducción que deseas anular. Cualquier cadena de traducción que no anules seguirá siendo cargada desde los archivos de idioma originales del paquete.
