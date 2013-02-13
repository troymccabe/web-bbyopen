<?php
/**
 * @category   BestBuy
 * @package    BestBuy\Service\Remix
 * @subpackage Response
 * @author     Troy McCabe <troy.mccabe@geeksquad.com>
 * @copyright  Copyright (c) 2013 {@link http://geeksquad.com Geek Squad}
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @version    $Id: RemixTest.php 23 2010-01-14 15:27:13Z troymccabe $
 */

namespace BestBuy\Service\Remix;

/**
 * Provides test cases for {@link \BestBuy\Service\Remix\Response}
 *
 * @category   BestBuy
 * @package    BestBuy\Service\Remix
 * @subpackage Response
 * @author     Troy McCabe <troy.mccabe@geeksquad.com>
 * @copyright  Copyright (c) 2013 {@link http://geeksquad.com Geek Squad}
 */

use BestBuy\Service\Remix;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * A response used for testing
     *
     * @var \BestBuy\Service\Remix\Response
     */
    protected $response;

    /**
     * Sets up an initial valid response for testing
     */
    public function setUp()
    {
        $remix = new Remix('uh592qch6yz3fwvskezru7cr');
        $this->response = $remix->store(281)->query();
    }

    /**
     * Tests getting properties and array elements from meta
     */
    public function test__Get()
    {
        // testing data (the response body)
        $this->assertRegExp('/.*<storeId>281<\/storeId>.*/m', $this->response->data);

        // testing data available in meta
        $this->assertEquals('200', $this->response->http_code);

        // testing data that doesn't exist
        $this->assertNull(@$this->response->fake_key);
    }

    /**
     * Tests verifying that properties exist and meta elements exist
     */
    public function test__Isset()
    {
        // checking for data
        $this->assertEquals(true, isset($this->response->data));

        // checking for meta keys
        $this->assertEquals(true, isset($this->response->http_code));
    }

    /**
     * Tests getting the string representation of the response
     */
    public function test__ToString()
    {
        // testing data (the response body)
        $this->assertRegExp('/.*<storeId>281<\/storeId>.*/m', (string)$this->response);
    }

    /**
     * Tests getting the data
     */
    public function testGetData()
    {
        // testing data (the response body)
        $this->assertRegExp('/.*<storeId>281<\/storeId>.*/m', $this->response->getData());
    }

    /**
     * Tests the error checking for the reponse
     */
    public function testIsError()
    {
        // testing that we don't have an error
        $this->assertEquals(false, $this->response->isError());

        // make a new remix to test failure
        $remix = new Remix('uh592qch6yz3fwvskezru7cr');
        $response = $remix->stores(array('thiskeydoesntexist=abc'))->query();
        $this->assertEquals(true, $response->isError());
    }

    /**
     * Tests the conversion to a simplexml element
     */
    public function testToSimpleXml()
    {
        // test success
        $this->assertInstanceOf('\SimpleXMLElement', $this->response->toSimpleXml());

        // test failure
        $remix = new Remix('uh592qch6yz3fwvskezru7cr');
        $response = $remix->store(281, \BestBuy\Service\Remix::FORMAT_JSON)->query();
        $this->assertEquals(false, $response->toSimpleXml());
    }

    /**
     * Tests the merging of meta and data
     */
    public function testToArray()
    {
        // make sure the key exists
        $this->assertArrayHasKey('data', $this->response->toArray());
    }
}