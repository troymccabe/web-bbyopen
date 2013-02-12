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
 * @author     Troy McCabe <troy.mccabe@geeksquad.com>
 * @copyright  Copyright (c) 2013 {@link http://geeksquad.com Geek Squad}
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @version    $Id: RemixTest.php 23 2010-01-14 15:27:13Z troymccabe $
 */

namespace BestBuy\Service;

/**
 * Provides test cases for {@link \BestBuy\Service\Remix}
 *
 * @category   BestBuy
 * @package    BestBuy\Service\Remix
 * @author     Troy McCabe <troy.mccabe@geeksquad.com>
 * @copyright  Copyright (c) 2013 {@link http://geeksquad.com Geek Squad}
 */
class RemixTest extends \PHPUnit_Framework_TestCase
{
    /**
     * An instance of {@link \BestBuy\Service\Remix} that we can work with
     *
     * @var \BestBuy\Service\Remix
     */
    protected $remix;

    /**
     * Sets up the required things before testings
     */
    public function setUp()
    {
        $this->remix = new Remix('');
    }

    /**
     * Tests the magic method __call
     */
    public function test__Call()
    {
        $this->remix->active('false');
        $this->assertAttributeEquals(array('active' => 'false'), 'params', $this->remix);

        $this->remix->active(array('false', 'true'));
        $this->assertAttributeEquals(array('active' => 'false,true'), 'params', $this->remix);
    }

    /**
     * Tests clearing the params
     */
    public function testClear()
    {
        $this->remix->params(array('test' => 'abc'));
        $this->remix->product(123456);
        $this->remix->clear();

        $this->assertAttributeEquals(array(), 'params', $this->remix);
        $this->assertAttributeEquals(array(), 'types', $this->remix);
    }

    /**
     * Tests building the uri
     */
    public function testGetTargetUri()
    {
        $this->remix->products(array('a=b'));

        $this->assertEquals('http://api.remix.bestbuy.com/v1/products(a=b)?apiKey=', $this->remix->getTargetUri());
    }

    /**
     * Tests setting the params
     */
    public function testParams()
    {
        // test setting initially
        $params = array('a' => 'b', 'c' => 'd');
        $this->remix->params($params);
        $this->assertAttributeEquals($params, 'params', $this->remix);

        // test appending
        $newParams = array('e' => array('test', 'face'));
        $this->remix->params($newParams);
        $this->assertAttributeEquals(array('e' => 'test,face'), 'params', $this->remix);
    }

    /**
     * Tests getting a single product
     */
    public function testProduct()
    {
        $expectedUri = 'http://api.remix.bestbuy.com/v1/products/123.xml?apiKey=';
        $this->remix->product(123);

        $this->assertEquals($expectedUri, $this->remix->getTargetUri());
    }

    /**
     * Tests getting products by a filter
     */
    public function testProducts()
    {
        $expectedUri = 'http://api.remix.bestbuy.com/v1/products(sku=123)?apiKey=';
        $this->remix->products(array('sku=123'));

        $this->assertEquals($expectedUri, $this->remix->getTargetUri());
    }

    /**
     * Tests a BBYOpen query
     */
    public function testQuery()
    {
        // test the exception if we're not doing anything proper
        try {
            $this->remix->query();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\BestBuy\Service\Remix\Exception', $e);
        }

        // test valid search
        $this->remix->store(281);
        $this->assertInstanceOf('\BestBuy\Service\Remix\Response', $this->remix->query());
    }

    /**
     * Tests getting a single store
     */
    public function testStore()
    {
        $expectedUri = 'http://api.remix.bestbuy.com/v1/stores/123.xml?apiKey=';
        $this->remix->store(123);

        $this->assertEquals($expectedUri, $this->remix->getTargetUri());
    }

    /**
     * Tests getting stores by a filter
     */
    public function testStores()
    {
        $expectedUri = 'http://api.remix.bestbuy.com/v1/stores(id=123)?apiKey=';
        $this->remix->stores(array('id=123'));

        $this->assertEquals($expectedUri, $this->remix->getTargetUri());
    }

    /**
     * Tests setting the timeout
     */
    public function testSetTimeout()
    {
        // true case
        $this->remix->setTimeout(123);
        $this->assertAttributeEquals(123, 'timeout', $this->remix);

        // testing the case of failure
        try {
            $this->remix->setTimeout('abc');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\BestBuy\Service\Remix\Exception', $e);
        }
    }
}