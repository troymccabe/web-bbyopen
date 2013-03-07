<?php
/**
 * LICENSE
 *
 * This source file is subject to the BSD license bundled with this package.
 *
 * Available online: {@link http://www.opensource.org/licenses/bsd-license.php}
 *
 * If you did not receive a copy of the license, and are unable to obtain it,
 * email {@link mailto:matt@mattwilliamsnyc.com matt@mattwilliamsnyc.com},
 * and I will send you a copy.
 *
 * {@link http://mattwilliamsnyc.com Matt Williams}, the author,
 * is neither affiliated with, nor endorsed by,
 * {@link http://www.bestbuy.com/ Best Buy}.
 *
 * @category   BestBuy
 * @package    BestBuy\Service\Remix
 * @author     Matt Williams <matt@mattwilliamsnyc.com>
 * @author     Troy McCabe (v2.0+) <troy.mccabe@geeksquad.com>
 * @copyright  Copyright (c) 2008 {@link http://mattwilliamsnyc.com Matt Williams}
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @version    $Id: Remix.php 23 2010-01-14 15:27:13Z mattwilliamsnyc $
 */

namespace BestBuy\Service;

/**
 * {@link \BestBuy\Service\Remix} provides methods for interacting with (version 1 of)
 * {@link http://www.bestbuy.com Best Buy}'s {@link http://remix.bestbuy.com Remix} API
 * and is based on the publicly available {@link http://remix.bestbuy.com/docs API documentation}.
 *
 * @category   BestBuy
 * @package    BestBuy\Service\Remix
 * @author     Matt Williams <matt@mattwilliamsnyc.com>
 * @author     Troy McCabe (v2.0+) <troy.mccabe@geeksquad.com>
 * @copyright  Copyright (c) 2008 {@link http://mattwilliamsnyc.com Matt Williams}
 */
class Remix
{
    /**
     * Entry point for all API requests
     *
     * @var string
     */
    const API_BASE = 'http://api.remix.bestbuy.com/v1';

    /**
     * The constant for JSON formatted response
     *
     * @var string
     */
    const FORMAT_JSON = 'json';

    /**
     * The constant for XML formatted response
     *
     * @var string
     */
    const FORMAT_XML = 'xml';

    /**
     * Key used to identify a client application to the API service
     *
     * @var string
     */
    protected $apiKey;

    /**
     * The time of the last BBYOpen query
     *
     * @var int
     */
    protected $lastQueryTime;

    /**
     * Query parameters to be appended to an API request URI
     *
     * @var array
     */
    protected $params = array();

    /**
     * The number of requests allowed to be executed in 1 second
     *
     * @var int
     */
    protected $requestsPerSecond = 5;

    /**
     * The requests executed during the current second
     *
     * @var int
     */
    protected $requestsThisSecond = 0;

    /**
     * Length of time (in seconds) to wait for a response to an API call
     *
     * @var int
     */
    protected $timeout = 10;

    /**
     * Resource types (e.g. stores, products) to be targeted by an API call
     *
     * @var array
     */
    protected $types = array();

    /**
     * Constructor.
     *
     * Instantiates a new Remix API client with an API Key identifier
     *
     * @param string $apiKey Key used to identify a client application to the API service
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Allows query parameters to be assigned using a convenient key($value) syntax (e.g. format('json')).
     *
     * This is an alternative to setting all parameters simultaneously via the {@link params()} method.
     *
     * @param string $method Name of the query parameter to which a value will be assigned
     * @param array  $args   List of arguments; the first value is assigned to the targeted query parameter
     *
     * @return \BestBuy\Service\Remix
     */
    public function __call($method, $args)
    {
        if (count($args)) {
            if (is_array($args[0])) {
                $args[0] = join(',', $args[0]);
            }

            $this->params[$method] = $args[0];
        }

        return $this;
    }

    /**
     * Clears all targeted resource types and query parameters; called after every {@link query()}.
     *
     * @return \BestBuy\Service\Remix
     */
    public function clear()
    {
        $this->types = array();
        $this->params = array();

        return $this;
    }

    /**
     * Returns the current target URI including any assigned query parameters.
     *
     * IMPORTANT: Target URIs include the API key assigned to this client; protect this data appropriately.
     *
     * @return string
     */
    public function getTargetUri()
    {
        $types = array();

        foreach ($this->types as $type) {
            $types[] = (string)$type;
        }

        $params = array_merge($this->params, array('apiKey' => $this->apiKey));
        $uri = sprintf('/%s?%s', join('+', $types), urldecode(http_build_query($params, '', '&')));

        return (defined('BBYOPEN_URI') ? BBYOPEN_URI : self::API_BASE) . $uri;
    }

    /**
     * Assigns one or more query parameters (as name/value pairs) to be included with an API call.
     *
     * @param array $params Name/value pairs to be assigned as query parameters
     *
     * @return \BestBuy\Service\Remix
     */
    public function params(array $params)
    {
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $params[$key] = join(',', $value);
            }
        }

        $this->params = $params;

        return $this;
    }

    /**
     * Targets a specific {@link http://remix.bestbuy.com/docs/Types/Products product}
     * resource (by SKU #) for an API call.
     *
     * @param string $sku    Identifier (SKU #) of the product to be targeted
     * @param string $format Desired response format (FORMAT_XML or FORMAT_JSON)
     *
     * @return \BestBuy\Service\Remix
     * @throws \BestBuy\Service\Remix\Type\Exception
     */
    public function product($sku, $format = Remix::FORMAT_XML)
    {
        $this->types['products'] = new Remix\Type('products', $sku, $format);

        return $this;
    }

    /**
     * Targets the (optionally filtered) {@link http://remix.bestbuy.com/docs/Types/Products products}
     * resource for an API call.
     *
     * May be used in combination with {@link stores()} to check for
     * {@link http://remix.bestbuy.com/docs/Types/Store_Availability store availability}.
     *
     * @param array $filters One or more criteria used to filter results
     * @param string $format Desired response format (FORMAT_XML or FORMAT_JSON)
     *
     * @return \BestBuy\Service\Remix
     * @throws \BestBuy\Service\Remix\Type\Exception
     */
    public function products(array $filters = array(), $format = Remix::FORMAT_XML)
    {
        $this->types['products'] = new Remix\Type('products', $filters, $format);

        return $this;
    }

    /**
     * Submits a query to the Remix API and returns the {@link \BestBuy\Service\Remix\Response response}.
     *
     * @return \BestBuy\Service\Remix\Response
     * @throws \BestBuy\Service\Remix\Exception
     */
    public function query()
    {
        if (!count($this->types)) {
            throw new Remix\Exception('At least one resource (e.g. products) must be targeted to perform an API query');
        }

        // if the last query time is the same (in the same second) and we're at the max requests per second
        // sleep for a second and resent the requests this second
        $queryTime = time();
        if ($this->lastQueryTime == $queryTime && $this->requestsThisSecond == $this->requestsPerSecond) {
            sleep(1);
            $this->requestsThisSecond = 0;
        }

        // increment this for next time
        $this->lastQueryTime = $queryTime;
        $this->requestsThisSecond++;

        // make the request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, preg_replace('/\s+/', '%20', $this->getTargetUri()));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        $data = curl_exec($ch);
        $meta = curl_getinfo($ch);
        curl_close($ch);

        // clear the request stuff
        $this->clear();

        return new Remix\Response($data, $meta);
    }

    /**
     * Sets the requests per second able to be executed
     *
     * @param int $requestsPerSecond The requests per second
     */
    public function setRequestsPerSecond($requestsPerSecond)
    {
        $this->requestsPerSecond = $requestsPerSecond;
    }

    /**
     * Targets a specific {@link http://remix.bestbuy.com/docs/Types/Stores store}
     * resource (by Store ID) for an API call.
     *
     * @param string $storeId Identifier (Store ID) of the store to be targeted
     * @param string $format  Desired response format (FORMAT_XML or FORMAT_JSON)
     *
     * @return \BestBuy\Service\Remix
     * @throws \BestBuy\Service\Remix\Type\Exception
     */
    public function store($storeId, $format = Remix::FORMAT_XML)
    {
        $this->types['stores'] = new Remix\Type('stores', $storeId, $format);

        return $this;
    }

    /**
     * Targets the (optionally filtered) {@link http://remix.bestbuy.com/docs/Types/Stores stores}
     * resource for an API call.
     *
     * May be used in combination with {@link products()} to check for
     * {@link http://remix.bestbuy.com/docs/Types/Store_Availability store availability}.
     *
     * @param array $filters One or more criteria used to filter results
     * @param string $format Desired response format (FORMAT_XML or FORMAT_JSON)
     *
     * @return \BestBuy\Service\Remix
     * @throws \BestBuy\Service\Remix\Type\Exception
     */
    public function stores(array $filters = array(), $format = Remix::FORMAT_XML)
    {
        $this->types['stores'] = new Remix\Type('stores', $filters, $format);

        return $this;
    }

    /**
     * Sets the amount of time (in seconds) to wait for a response to an API call.
     *
     * @param integer $timeout Length of time (in seconds) to wait before timing out
     *
     * @return \BestBuy\Service\Remix
     * @throws \BestBuy\Service\Remix\Exception
     */
    public function setTimeout($timeout)
    {
        if (0 >= $timeout || !(is_numeric($timeout) && ($f = floatval($timeout)) == intval($f))) {
            throw new Remix\Exception("Timeout ({$timeout}) must be a positive integer");
        }

        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Sets the page that we're currently retrieving
     *
     * @param int $page
     * @return \BestBuy\Service\Remix
     */
    public function page($page)
    {
        $this->params['page'] = $page;

        return $this;
    }

    /**
     * Sets the size of the page to retrieve
     *
     * @param int $pageSize
     * @return \BestBuy\Service\Remix
     */
    public function pageSize($pageSize)
    {
        $this->params['pageSize'] = $pageSize;

        return $this;
    }

    /**
     * Sets which details are shown in the response
     *
     * @param int $pageSize
     * @return \BestBuy\Service\Remix
     */
    public function show($show)
    {
        $this->params['show'] = $show;

        return $this;
    }
}
