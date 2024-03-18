# IBAN

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

beerntea/iban is a PHP 8.0+ library for validating IBAN bank account numbers.
It currently supports IBAN validation of 99 countries.

It's based on [cmpayments/iban](https://github.com/cmpayments/iban/), which seems abandoned.
Added support for php > 8.0, updated dependencies and unit tests.

## Installation
To install beerntea/iban just require it with composer
```
composer require beerntea/iban
```

## Usage example

```php
<?php
require 'vendor/autoload.php';

use Beerntea\IBAN;

$iban = new IBAN('NL58ABNA0000000001');

// validate the IBAN
if (!$iban->validate($error)) {
    echo "IBAN is not valid, error: " . $error;
}

// pretty print IBAN
echo $iban->format();
```

## Submitting bugs and feature requests
Bugs and feature request are tracked on [GitHub](https://github.com/beerntea/iban/issues)

## Copyright and license
The edits in the beerntea/iban library is copyright © [Beerntea](https://github.com/beerntea/) and licensed for use under the MIT License (MIT).
The cmpayment/iban library is copyright © [Bas Peters](https://github.com/baspeters/) and licensed for use under the MIT License (MIT). Please see [LICENSE][] for more information.

