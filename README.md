# INTRODUCTION

## [DEPRECATED: use https://github.com/BestBuyAPIs/bestbuy-sdk-php]

`\BestBuy\Service\BBYOpen` is a PHP library that supports interaction with
Best Buy's BBYOpen API (<https://bbyopen.com/>).

Best Buy provides the following resources to API users:

Developer Network: <https://bbyopen.com/>

API Documentation: <https://bbyopen.com/bbyopen-apis-overview>

Discussion Forums: <https://bbyopen.com/forum>

# DISCLAIMER

Matt Williams, the author, is neither affiliated with, nor endorsed by, Best Buy

Troy McCabe is affiliated with Best Buy

# SUPPORT AND BUG REPORTS

Bug reports, as well as feature requests, may be submitted at:

<https://bitbucket.org/troymccabe/web-bbyopen/issues/>

Alternatively, you may email the developer directly: <troy.mccabe@geeksquad.com>

# GETTING STARTED

    :::php
    $apiKey = '12345678'; // Your API key
    $bbyOpen  = new \BestBuy\Service\BBYOpen\Client($apiKey);

    // Retrieve a list of stores within 10 miles of a zip code
    $result = $bbyOpen->stores(array('area(10006,10)'))->query();

    // Result objects may be implicitly cast as strings
    echo $result;

    // Retrieve a list of Movies containing the text "Bat"
    $result = $bbyOpen->products(array('name=bat*', 'type=Movie'))->query();

    echo $result;

    // Retrieve fields from a list of Movies starting with "Bat" in JSON format
    $result = $bbyOpen->products(array('type=Movie', 'name=bat*'))
                    ->show(array('name','regularPrice','url', 'sku'))
                    ->format('json')
                    ->query();

    echo $result;

    // Check for store availability of a Playstation 3 in a given area
    $result = $bbyOpen->stores(array('area(10006,10)'))
                    ->products(array('sku=8982988'))
                    ->sort('distance')
                    ->query();

    echo $result;

To override the root URI, you can define `BBYOPEN_URI`, which will replace `BBYOpen::API_BASE` in the built URL.

# DOCUMENTATION

Documentation was generated using PhpDocumentor [<http://www.phpdoc.org/>].

Documentation may be regenerated as follows (from the base directory):

`phpdoc`

# TESTS

Tests are written for PHPUnit [<https://github.com/sebastianbergmann/phpunit/>]

Tests can be run as follows (from the base directory):

`phpunit`
