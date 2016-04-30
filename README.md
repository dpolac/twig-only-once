OnlyOnce Twig Extension
=======================
Twig test which returns true only once per value.

Made to aid in looping through unique values.

```twig
    {% for article in articles if article.category is onlyOnce %}
        * {{ article.category }}
    {% endfor %}
```

Installation
------------
 * Install via Composer:
```bash
    composer require dpolac/twig-only-once
```

 * Add the extension to Twig:
```php
    $twig->addExtension(new \DPolac\OnlyOnce\OnlyOnceExtension());
```

------------------------------------------------

Usage
-----
Extension provides two tests `onlyOnce` and `onlyOnceWhenOccurs`.

###`is onlyOnce`
```twig
    {% if value is onlyOnce %} ... {% endif %}
    {% if value is onlyOnce(space) %} ... {% endif %}
```
Test returns true only when it's called for the first time with given `value` and `space` pair.

#### Arguments

 * **value**: can be value of any type
    * number - is converted to string, so `12` and `'12'` are treated as same value,
    * string,
    * object - are compared by reference, so `new \stdClass()` and `new \stdClass()` are treated as different value,
    * array - to arrays are treated as same value when have exactly same key-value set; order of keys in hashes is ignored,
 * **space**: can be any string (default: `'default'`)

###`is onlyOnceWhenOccurs`
```twig
    {% if value is onlyOnceWhenOccurs(n) %} ... {% endif %}
    {% if value is onlyOnceWhenOccurs(n, space) %} ... {% endif %}
```
Test returns true only when it's called for the n-th time with given `value` and `space` pair.
Note that `onlyOnce` is an alias to `onlyOnceWhenOccurs(1)`, so they share internal occurrences' counter.

#### Arguments

 * **value**: as in `onlyOnce`,
 * **n**: number of occurrences after which test returns true,
 * **space**: can be any string (default: `'default'`).

Examples
--------
Check tests in `./Tests/Fixtures/` directory for more examples.
