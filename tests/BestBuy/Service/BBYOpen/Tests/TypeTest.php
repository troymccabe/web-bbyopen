<?php
/**
 * @category   BestBuy
 * @package    BestBuy\Service\BBYOpen
 * @subpackage Type
 * @author     Troy McCabe <troy.mccabe@geeksquad.com>
 * @copyright  Copyright (c) 2013 {@link http://geeksquad.com Geek Squad}
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @version    $Id: TypeTest.php 23 2010-01-14 15:27:13Z troymccabe $
 */

namespace BestBuy\Service\BBYOpen\Tests;

use BestBuy\Service\BBYOpen\Client;
use BestBuy\Service\BBYOpen\Type;
use BestBuy\Service\BBYOpen\Type\Exception;

/**
 * Provides test cases for {@link \BestBuy\Service\BBYOpen\Type}
 *
 * @category   BestBuy
 * @package    BestBuy\Service\BBYOpen
 * @subpackage Type
 * @author     Troy McCabe <troy.mccabe@geeksquad.com>
 * @copyright  Copyright (c) 2013 {@link http://geeksquad.com Geek Squad}
 */
class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests assigning the filter to the uri
     */
    public function testAssignFilter()
    {
        // testing scalar
        $scalarType = new Type('products', 123, Client::FORMAT_XML);
        $this->assertAttributeEquals(123, 'identifier', $scalarType);

        // testing vector
        $params = array('id=123');
        $vectorType = new Type('stores', $params, Client::FORMAT_XML);
        $this->assertAttributeEquals($params, 'params', $vectorType);

        // testing error
        $params = new \StdClass();
        try {
            new Type('stores', $params, Client::FORMAT_XML);
        } catch (\Exception $e) {
            $this->assertInstanceOf('\BestBuy\Service\BBYOpen\Type\Exception', $e);
        }
    }

    /**
     * Tests getting the string representation of the query
     */
    public function testToString()
    {
        // testing scalar
        $scalarType = new Type('products', 123, Client::FORMAT_XML);
        $this->assertEquals('products/123.xml', $scalarType->toString());

        // testing vector
        $params = array('id=123');
        $vectorType = new Type('stores', $params, Client::FORMAT_XML);
        $this->assertEquals('stores(id=123)', $vectorType->toString());

        // testing empty vector
        $params = array();
        $emptyVectorType = new Type('stores', $params, Client::FORMAT_XML);
        $this->assertEquals('stores', $emptyVectorType->toString());
    }

    /**
     * Tests validating the format
     */
    public function testValidateFormat()
    {
        // testing success
        $type = new Type('products', 123, Client::FORMAT_XML);
        $this->assertAttributeEquals(Client::FORMAT_XML, 'format', $type);

        // testing failure
        try {
            new Type('products', 123, 'badformat');
        } catch (Exception $e) {
            $this->assertInstanceOf('\BestBuy\Service\BBYOpen\Type\Exception', $e);
        }
    }

    /**
     * Tests validing the type of query (store, product, etc.)
     */
    public function testValidateType()
    {
        // testing success
        $type = new Type('products', 123, Client::FORMAT_XML);
        $this->assertAttributeEquals('products', 'type', $type);

        // testing failure
        try {
            new Type('badtype', 123, Client::FORMAT_XML);
        } catch (Exception $e) {
            $this->assertInstanceOf('\BestBuy\Service\BBYOpen\Type\Exception', $e);
        }
    }
}