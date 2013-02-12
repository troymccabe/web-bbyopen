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
 * @subpackage Type
 * @author     Matt Williams <matt@mattwilliamsnyc.com>
 * @author     Troy McCabe (v2.0+) <troy.mccabe@geeksquad.com>
 * @copyright  Copyright (c) 2008 {@link http://mattwilliamsnyc.com Matt Williams}
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @version    $Id: Type.php 14 2009-02-08 22:06:10Z mattwilliamsnyc $
 */

namespace BestBuy\Service\Remix;

/**
 * {@link \BestBuy\Service\Remix\Type} represents a resource type to be targeted by an API call.
 * Remix currently includes 2 types of information:
 * {@link http://remix.bestbuy.com/docs/Types/Stores stores} and
 * {@link http://remix.bestbuy.com/docs/Types/Products products}.
 *
 * {@link http://remix.bestbuy.com/docs/Types/Store_Availability Store Availability} information
 * is available by "joining" the store and product types.
 *
 * @category   BestBuy
 * @package    BestBuy\Service\Remix
 * @subpackage Type
 * @author     Matt Williams <matt@mattwilliamsnyc.com>
 * @author     Troy McCabe (v2.0+) <troy.mccabe@geeksquad.com>
 * @copyright  Copyright (c) 2008 {@link http://mattwilliamsnyc.com Matt Williams}
 */
class Type
{
    /**
     * Unique identifier (Store ID or Product SKU); only used when targeting an individual resource
     *
     * @var string
     */
    protected $identifier;

    /**
     * Filters; only used when targeting a collection of stores or products
     *
     * @var array
     */
    protected $params;

    /**
     * Desired response format; only used when targeting an individual store or product
     *
     * @var string
     */
    protected $format;

    /**
     * Primary type of the targeted resource ("stores" or "products")
     *
     * @var string
     */
    protected $type;

    /**
     * Supported resource formats
     *
     * @var array
     */
    protected static $formats = array(\BestBuy\Service\Remix::FORMAT_XML, \BestBuy\Service\Remix::FORMAT_JSON);

    /**
     * Supported resource types
     *
     * @var array
     */
    protected static $types = array('stores', 'products');

    /**
     * Constructor.
     *
     * Instantiates a new resource type using either an identifier (for an indvidual store or product),
     * or an array containing zero or more filters (to target a collection of stores or products).
     *
     * When targeting an individual store or product, $filter should contain a string identifier
     * (Store ID or Product SKU). When targeting a collection of stores or products, $filter should
     * contain an array of filtering statements (e.g. array('name=bat*', 'itemUpdateDate<2008-09-03')).
     *
     * Filter parameters may use any valid comparison type:
     * <ul>
     *   <li><b>=</b> ("equal")</li>
     *   <li><b>!= OR <></b> ("not equal")</li>
     *   <li><b>< OR <=</b> ("less than" or "less than or equal to")</li>
     *   <li><b>> OR >=</b> ("greater than" or "greater than or equal to")</li>
     *   <li><b>&</b> ("and")</li>
     *   <li><b>*</b> ("wildcard")</li>
     * </ul>
     *
     * Additionally, function-style parameters are supported (e.g. area(postalCode,distance)).
     *
     * Examples of valid parameters:
     *
     * <ul>
     *   <li>name=bat*</li>
     *   <li>salePrice<=99.99</li>
     *   <li>itemUpdateDate>2008-09-03</li>
     *   <li>area(11201,10)</li>
     *   <li>area(38.89,-77.03,10)</li>
     * </ul>
     *
     * @param string       $type   'stores' or 'products'
     * @param array|string $filter String identifier (Store ID/Product SKU) or array of filters
     * @param string       $format 'xml' or 'json'; Only used when targeting an individual store or product
     *
     * @throws \BestBuy\Service\Remix\Type\Exception
     */
    public function __construct($type, $filter, $format = \BestBuy\Service\Remix::FORMAT_XML)
    {
        // validate and set the format
        $format = strtolower(trim($format));
        $this->validateFormat($format);
        $this->format = $format;

        // validate and set the type
        $type = strtolower(trim($type));
        $this->validateType($type);
        $this->type = $type;

        $this->assignFilter($filter);
    }

    /**
     * Allows this type to be cast to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Formats this type as a URI fragment.
     *
     * Examples:
     * <ul>
     *   <li>"stores/4.json"</li>
     *   <li>"products(manufacturer='canon'&salePrice<33)"</li>
     * </ul>
     *
     * @return string
     */
    public function toString()
    {
        if (strlen($this->identifier)) {
            return sprintf('%s/%s.%s', $this->type, $this->identifier, $this->format);
        } else {
            if (count($this->params)) {
                return sprintf('%s(%s)', $this->type, join('&', $this->params));
            }
        }

        return $this->type;
    }

    /**
     * Assigns the filter for the construction of the request uri
     *
     * @param string|array $filter
     * @throws Type\Exception
     */
    protected function assignFilter($filter)
    {
        // Assign filter input as identifier (Store ID/Product SKU) or filter parameters
        if (is_scalar($filter)) {
            $this->identifier = trim((string)$filter);
        } else {
            if (is_array($filter)) {
                $this->params = $filter;
            } else {
                throw new Type\Exception('$filter must be a string (identifier) or an array (parameters)');
            }
        }
    }

    /**
     * Validates that the format requested is a valid format
     *
     * @param string $format \BestBuy\Service\Remix::FORMAT_XML or \BestBuy\Service\Remix::FORMAT_JSON
     * @throws Type\Exception
     */
    protected function validateFormat($format)
    {
        // Assign format ('xml' or 'json')
        if (!in_array($format, self::$formats)) {
            throw new Type\Exception(sprintf('Invalid format "%s" (%s)', $format, join(', ', self::$formats)));
        }
    }

    /**
     * Validates that the type provided is a valid type
     *
     * @param string $type 'stores' or 'products'
     * @throws Type\Exception
     */
    protected function validateType($type)
    {
        // Assign type ('stores' or 'products')
        if (!in_array($type, self::$types)) {
            throw new Type\Exception(sprintf('Invalid type "%s" (%s)', $type, join(', ', self::$types)));
        }
    }
}
