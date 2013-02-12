<?php
/**
 * LICENSE
 *
 * This source file is subject to the BSD license bundled with this package.
 *
 * Available online: {@link http://www.opensource.org/licenses/bsd-license.php}
 *
 * @category   BestBuy
 * @package    BestBuy\Service\Remix
 * @subpackage Type
 * @author     Troy McCabe <troy.mccabe@geeksquad.com>
 * @copyright  Copyright (c) 2013 {@link http://geeksquad.com Geek Squad}
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @version    $Id: RemixTest.php 23 2010-01-14 15:27:13Z troymccabe $
 */

namespace BestBuy\Service\Remix;

/**
 * Provides test cases for {@link \BestBuy\Service\Remix}
 *
 * @category   BestBuy
 * @package    BestBuy\Service\Remix
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
        $scalarType = new Type('products', 123, \BestBuy\Service\Remix::FORMAT_XML);

        $params = array('id=123');
        $vectorType = new Type('stores', $params, \BestBuy\Service\Remix::FORMAT_XML);

        $this->assertAttributeEquals(123, 'identifier', $scalarType);
        $this->assertAttributeEquals($params, 'params', $vectorType);
    }

    /**
     * Tests getting the string representation of the query
     */
    public function testToString()
    {
        $scalarType = new Type('products', 123, \BestBuy\Service\Remix::FORMAT_XML);

        $params = array('id=123');
        $vectorType = new Type('stores', $params, \BestBuy\Service\Remix::FORMAT_XML);

        $this->assertEquals('products/123.xml', $scalarType->toString());
        $this->assertEquals('stores(id=123)', $vectorType->toString());
    }

    /**
     * Tests validating the format
     */
    public function testValidateFormat()
    {
        $type = new Type('products', 123, \BestBuy\Service\Remix::FORMAT_XML);
        $this->assertAttributeEquals(\BestBuy\Service\Remix::FORMAT_XML, 'format', $type);

        try {
            new Type('products', 123, 'badformat');
        } catch (Exception $e) {
            $this->assertInstanceOf('\BestBuy\Service\Remix\Type\Exception', $e);
        }
    }

    /**
     * Tests validing the type of query (store, product, etc.)
     */
    public function testValidateType()
    {
        $type = new Type('products', 123, \BestBuy\Service\Remix::FORMAT_XML);
        $this->assertAttributeEquals('products', 'type', $type);

        try {
            new Type('badtype', 123, \BestBuy\Service\Remix::FORMAT_XML);
        } catch (Exception $e) {
            $this->assertInstanceOf('\BestBuy\Service\Remix\Type\Exception', $e);
        }
    }
}