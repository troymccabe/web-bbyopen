<?php
/**
 * @category   BestBuy
 * @package    BestBuy\Service\BBYOpen
 * @author     Troy McCabe <troy.mccabe@geeksquad.com>
 * @copyright  Copyright (c) 2013 {@link http://geeksquad.com Geek Squad}
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @version    $Id: BBYOpenTest.php 23 2010-01-14 15:27:13Z troymccabe $
 */

namespace BestBuy\Service\BBYOpen\Tests;

use BestBuy\Service\BBYOpen\ClientMock;

/**
 * Provides test cases for {@link \BestBuy\Service\BBYOpen}
 *
 * @category   BestBuy
 * @package    BestBuy\Service\BBYOpen
 * @author     Troy McCabe <troy.mccabe@geeksquad.com>
 * @copyright  Copyright (c) 2013 {@link http://geeksquad.com Geek Squad}
 */
class BBYOpenMockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The mock object
     *
     * @var ClientMock
     */
    protected $mock;

    /**
     * Set up the testcase
     */
    public function setUp()
    {
        parent::setUp();

        $this->mock = new ClientMock('foo');
    }

    /**
     * Testing the registration of a test case
     */
    public function testRegisterTestCase()
    {
        $this->mock->registerTestCase('http://api.remix.bestbuy.com/v1/?apiKey=foo', 'baz');

        $this->assertAttributeEquals(
            array(array('uri' => 'http://api.remix.bestbuy.com/v1/?apiKey=foo', 'data' => 'baz', 'meta' => array())),
            'testCases',
            $this->mock
        );
    }

    /**
     * Test the query mock
     */
    public function testQuery()
    {
        $this->mock->registerTestCase('http://api.remix.bestbuy.com/v1/?apiKey=foo', 'baz');
        $response = $this->mock->query();

        $this->assertEquals('baz', $response);

        try {
            $mock = new ClientMock('foo');
            $mock->registerTestCase('bar', 'baz');
            $mock->query();
        } catch (\Exception $e) {
            $this->assertStringStartsWith('Failed to find test', $e->getMessage());
        }
    }
}