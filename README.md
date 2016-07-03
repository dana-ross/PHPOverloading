# PHP Overloading [![Build Status](https://travis-ci.org/daveross/PHPOverloading.svg?branch=master)](https://travis-ci.org/daveross/PHPOverloading) [![Latest Stable Version](https://poser.pugx.org/daveross/phpoverloading/v/stable)](https://packagist.org/packages/daveross/phpoverloading) [![Total Downloads](https://poser.pugx.org/daveross/phpoverloading/downloads)](https://packagist.org/packages/daveross/phpoverloading) [![License](https://poser.pugx.org/daveross/phpoverloading/license)](https://packagist.org/packages/daveross/phpoverloading)

[PHP trait](http://php.net/manual/en/language.oop5.traits.php) providing a form of method overloading in PHP. Useful when porting code from languages which support method overloading, and that's about the only time you should seriously consider using this library.

Seriously, if you're thinking of using it for new PHP code, ask yourself why. There's no reason to add this kind of overhead, not to mention the cognitize load of weird method names, just to overload methods. PHP has gone this long without native method overloading. It can go longer without it.

Just stop. Think of the children.

## License

[MIT](http://daveross.mit-license.org)

See [why I contribute to open source software](https://medium.com/@csixty4/why-i-write-open-source-software-6d3569c85e64#.hq00fw10b).

[Buy me a coffee](https://cash.me/$daveross/5)

## Contributing

Pull requests are welcome. Unit tests are encouraged but not required.

## Installation

### Using composer

Put the require statement for `phpoverloading` in your `composer.json` file and run `composer install` or `php composer.phar install`:

```json
{
    "require": {
        "daveross/phpoverloading": "~1.0"
    }
}
```

### Manually

Include the file in the `src` directory, or individual files as needed:

```php
<?php
include 'path/to/phpoverloading/src/OverloadedMethods.php';
```

## Using

Begin by applying the trait to your class by having ```use \DaveRoss\PHPOverloading\OverloadedMethods;``` inside the class body.

### Supported parameter types

All of [PHP's native types](http://php.net/manual/en/language.types.intro.php) are recognized:

* boolean
* integer
* float
* string
* array
* object

In addition, the trait recognizes class names and class hierarchies.

### A note on method naming

PHP doesn't allow functions or methods to have the same name. That's probably the biggest obstacle to actual method overloading. So you'll need to append the parameter types to each function's name.

For example, if you had two methods named ```example```, one taking a string and one taking an array, you'd name them ```example_string``` and ```example_array```, but when you called then you'd just reference ```$foo->example()``` and pass a string or array and *magic* happens.

If a method takes more than one parameter, their types are concatenated together in the function name, separated by underscores as in ```example_string_integer_float```. Overloaded methods can take different numbers of parameters.

Constructors are like any other functions. You can have ```__construct_string``` and ```__construct_object``` if you want. But there's also a ```_default``` suffix (i.e. ```__construct_default```) for a default constructor, called when there aren't any parameters passed to the constructor.

### The class hierarchy

Say you have these classes:

```php
class A {}
class B extends A{};

class Foo {
    function __construct_a() {
        ...
    }
}
```

If you call Foo's constructor and pass an instance of class B, the trait won't find a matching ```__construct_b``` function, so it'll follow the object hierarchy and call ```__construct_a``` instead, passing the instance of B.

If there wasn't a ```__construct_a``` method, it would look for ```__construct_object``` as a fallback.

### Constructor overloading

```php
class Example {

    use \DaveRoss\PHPOverloading\OverloadedMethods;

    public $string, $integer;

    public function __construct_string($val) {
        $this->string = $val;
    }

    public function __construct_integer($val) {
        $this->integer = $val;
    }

}

$strExample = new Example("hello world");
$intExample = new Example(5);
```

### Method overloading

```php
class Example {

    use \DaveRoss\PHPOverloading\OverloadedMethods;

    public $string, $integer;

    public function example_string($val) {
        echo 'the string is ' . $val;
    }

    public function example_integer($val) {
        echo 'the integer is ' . $val;
    }

}

$x = new Example();
$x->example("hello world"); // echoes "the string is hello world"
$x->example(5); // echoes "the integer is 5"
```