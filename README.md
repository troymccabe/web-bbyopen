# INTRODUCTION

`\BestBuy\Service\Remix` is a PHP library that supports interaction with
Best Buy's Remix API (<http://remix.bestbuy.com/>).

Best Buy provides the following resources to API users:

Developer Network: <http://remix.bestbuy.com/>
API Documentation: <http://remix.bestbuy.com/docs>
Discussion Forums: <http://remix.bestbuy.com/forum>

# DISCLAIMER

Matt Williams, the author, is neither affiliated with, nor endorsed by, Best Buy
Troy McCabe is affiliated with Best Buy

# SUPPORT AND BUG REPORTS

Bug reports, as well as feature requests, may be submitted at:

<https://bitbucket.org/troymccabe/web-bbyopen/issue/>

Alternatively, you may email the developer directly: <troy.mccabe@geeksquad.com>

# GETTING STARTED

    :::php
    $apiKey = '12345678'; // Your API key
    $remix  = new \BestBuy\Service\Remix($apiKey);

    // Retrieve a list of stores within 10 miles of a zip code
    $result = $remix->stores(array('area(10006,10)'))->query();

    // Result objects may be implicitly cast as strings
    echo $result;

    // Retrieve a list of Movies containing the text "Bat"
    $result = $remix->products(array('name=bat*', 'type=Movie'))->query();

    echo $result;

    // Retrieve fields from a list of Movies starting with "Bat" in JSON format
    $result = $remix->products(array('type=Movie', 'name=bat*'))
                    ->show(array('name','regularPrice','url', 'sku'))
                    ->format('json')
                    ->query();

    echo $result;

    // Check for store availability of a Playstation 3 in a given area
    $result = $remix->stores(array('area(10006,10)'))
                    ->products(array('sku=8982988'))
                    ->sort('distance')
                    ->query();

    echo $result;

# DOCUMENTATION

Documentation was generated using PhpDocumentor [<http://www.phpdoc.org/>].

Documentation may be regenerated as follows (from the base directory):

`phpdoc -d ./src -t ./docs/phpdoc`