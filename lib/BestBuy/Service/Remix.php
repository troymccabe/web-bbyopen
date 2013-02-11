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
 * @package    BestBuy_Service_Remix
 * @author     Matt Williams <matt@mattwilliamsnyc.com>
 * @copyright  Copyright (c) 2008 {@link http://mattwilliamsnyc.com Matt Williams}
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @version    $Id: Remix.php 23 2010-01-14 15:27:13Z mattwilliamsnyc $
 */

/**
 * @see BestBuy_Service_Remix_Response
 */
require_once 'BestBuy/Service/Remix/Response.php';

/**
 * @see BestBuy_Service_Remix_Type
 */
require_once 'BestBuy/Service/Remix/Type.php';

/**
 * {@link BestBuy_Service_Remix} provides methods for interacting with (version 1 of)
 * {@link http://www.bestbuy.com Best Buy}'s {@link http://remix.bestbuy.com Remix} API
 * and is based on the publicly available {@link http://remix.bestbuy.com/docs API documentation}.
 *
 * @category   BestBuy
 * @package    BestBuy_Service_Remix
 * @author     Matt Williams <matt@mattwilliamsnyc.com>
 * @copyright  Copyright (c) 2008 {@link http://mattwilliamsnyc.com Matt Williams}
 */
class BestBuy_Service_Remix
{
    /**
     * Entry point for all API requests
     */
    const API_BASE = 'http://api.remix.bestbuy.com/v1';

    /**
     * Key used to identify a client application to the API service
     */
    protected $_apiKey;

    /**
     * Length of time (in seconds) to wait for a response to an API call
     */
    protected $_timeout = 10;

    /**
     * Query parameters to be appended to an API request URI
     */
    protected $_params = array();

    /**
     * Resource types (e.g. stores, products) to be targeted by an API call
     */
    protected $_types = array();

    /**
     * Constructor.
     *
     * Instantiates a new Remix API client with an API Key identifier
     *
     * @param string $apiKey Key used to identify a client application to the API service
     */
    public function __construct($apiKey)
    {
        $this->_apiKey = $apiKey;
    }

    /**
     * Allows query parameters to be assigned using a convenient key($value) syntax (e.g. format('json')).
     *
     * This is an alternative to setting all parameters simultaneously via the {@link params()} method.
     *
     * @param string $method Name of the query parameter to which a value will be assigned
     * @param array  $args   List of arguments; the first value is assigned to the targeted query parameter
     *
     * @return BestBuy_Service_Remix
     */
    public function __call($method, $args)
    {
        if(count($args))
        {
            if(is_array($args[0]))
            {
                $args[0] = join(',', $args[0]);
            }

            $this->_params[$method] = $args[0];

            return $this;
        }
    }

    /**
     * Targets the (optionally filtered) {@link http://remix.bestbuy.com/docs/Types/Products products}
     * resource for an API call.
     *
     * May be used in combination with {@link stores()} to check for
     * {@link http://remix.bestbuy.com/docs/Types/Store_Availability store availability}.
     *
     * @param array $filters One or more criteria used to filter results
     *
     * @return BestBuy_Service_Remix
     * @throws BestBuy_Service_Remix_Exception
     */
    public function products(array $filters =array())
    {
        $this->_types['products'] = self::_buildType('products', $filters);

        return $this;
    }

    /**
     * Targets a specific {@link http://remix.bestbuy.com/docs/Types/Products product}
     * resource (by SKU #) for an API call.
     *
     * @param string $sku    Identifier (SKU #) of the product to be targeted
     * @param string $format Desired response format ('xml' or 'json')
     *
     * @return BestBuy_Service_Remix
     * @throws BestBuy_Service_Remix_Exception
     */
    public function product($sku, $format ='xml')
    {
        $this->_types['products'] = self::_buildType('products', $sku, $format);

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
     *
     * @return BestBuy_Service_Remix
     * @throws BestBuy_Service_Remix_Exception
     */
    public function stores(array $filters =array())
    {
        $this->_types['stores'] = self::_buildType('stores', $filters);

        return $this;
    }

    /**
     * Targets a specific {@link http://remix.bestbuy.com/docs/Types/Stores store}
     * resource (by Store ID) for an API call.
     *
     * @param string $storeId Identifier (Store ID) of the store to be targeted
     * @param string $format  Desired response format ('xml' or 'json')
     *
     * @return BestBuy_Service_Remix
     * @throws BestBuy_Service_Remix_Exception
     */
    public function store($identifier, $format ='xml')
    {
        $this->_types['stores'] = new BestBuy_Service_Remix_Type('stores', $identifier, $format);

        return $this;
    }

    /**
     * Assigns one or more query parameters (as name/value pairs) to be included with an API call.
     *
     * @param array $params Name/value pairs to be assigned as query parameters
     *
     * @return BestBuy_Service_Remix
     */
    public function params(array $params)
    {
        foreach($params as $key => $value)
        {
            if(is_array($value))
            {
                $params[$key] = join(',', $value);
            }
        }

        $this->_params = $params;

        return $this;
    }

    /**
     * Clears all targeted resource types and query parameters; called after every {@link query()}.
     *
     * @return BestBuy_Service_Remix
     */
    public function clear()
    {
        $this->_types  = array();
        $this->_params = array();

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

        foreach($this->_types as $type)
        {
            $types[] = (string) $type;
        }

        $params = array_merge($this->_params, array('apiKey' => $this->_apiKey));
        $uri    = sprintf('/%s?%s', join('+', $types), urldecode(http_build_query($params, '', '&')));

        return self::API_BASE . $uri;
    }

    /**
     * Sets the amount of time (in seconds) to wait for a response to an API call.
     *
     * @param integer $timeout Length of time (in seconds) to wait before timing out
     *
     * @return BestBuy_Service_Remix
     * @throws BestBuy_Service_Remix_Exception
     */
    public function setTimeout($timeout)
    {
        if(0 >= $timeout || !(is_numeric($timeout) && ($f = floatval($timeout)) == intval($f)))
        {
            self::_throwException("Timeout ({$timeout}) must be a positive integer");
        }

        $this->_timeout = $timeout;

        return $this;
    }

    /**
     * Submits a query to the Remix API and returns the {@link BestBuy_Service_Remix_Response response}.
     *
     * @return BestBuy_Service_Remix_Response
     * @throws BestBuy_Service_Remix_Exception
     */
    public function query()
    {
        if(!count($this->_types))
        {
            self::_throwException(
                'At least one resource (e.g. products, stores) must be targeted in order to perform an API query'
            );
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->getTargetUri());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout);

        $data = curl_exec($ch);
        $meta = curl_getinfo($ch);

        curl_close($ch);

        $this->clear();

        return new BestBuy_Service_Remix_Response($data, $meta);
    }

    /**
     * Builds a resource {@link BestBuy_Service_Remix_Type type} to be targeted for an API call.
     *
     * @param string       $type   Desired resource type ('stores' or 'products')
     * @param string|array $filter Identifier or filter array used to target this type resource
     * @param string       $format Desired response format ('json' or 'xml')
     *
     * @return BestBuy_Service_Remix_Type
     */
    protected static function _buildType($type, $filter, $format ='xml')
    {
        try
        {
            $type = new BestBuy_Service_Remix_Type($type, $filter, $format);
        }
        catch(BestBuy_Service_Remix_Type_Exception $e)
        {
            self::_throwException(sprintf("Invalid '%s' target:\n%s", $type, $e->getMessage()));
        }

        return $type;
    }

    /**
     * Throws an exception with a user-supplied message.
     *
     * @param string $message Message to be provided with the exception being raised
     *
     * @throws BestBuy_Service_Remix_Exception
     */
    protected static function _throwException($message)
    {
        /** @see BestBuy_Service_Remix_Exception */
        require_once 'BestBuy/Service/Remix/Exception.php';

        throw new BestBuy_Service_Remix_Exception($message);
    }
}
