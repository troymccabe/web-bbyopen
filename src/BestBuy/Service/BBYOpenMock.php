<?php
/**
 * @category   BestBuy
 * @package    BestBuy\Service\BBYOpen
 * @author     Troy McCabe <troy.mccabe@geeksquad.com>
 * @copyright  Copyright (c) 2013 {@link http://geeksquad.com Geek Squad}
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @version    $Id: BBYOpenMock.php 23 2010-01-14 15:27:13Z troymccabe $
 */

namespace BestBuy\Service;

use BestBuy\Service\BBYOpen\Exception;

/**
 * Provides a mock object for {@link \BestBuy\Service\BBYOpen}
 *
 * You can use this in your own applications to test with a fake version that doesn't actually make
 * requests to bbyopen
 *
 * @category   BestBuy
 * @package    BestBuy\Service\BBYOpen
 * @author     Troy McCabe <troy.mccabe@geeksquad.com>
 * @copyright  Copyright (c) 2013 {@link http://geeksquad.com Geek Squad}
 */
class BBYOpenMock extends BBYOpen
{
    /**
     * The list of testcases and their responses that we have registered
     *
     * @var array
     */
    protected $testCases = array();

    /**
     * Registers a test case that we can test against and provide a response for
     *
     * @param string $uri The uri to test against
     * @param string $data The data to respond with
     * @param array $meta The headers to respond with
     */
    public function registerTestCase($uri, $data, $meta = array())
    {
        $this->testCases[] = array(
            'uri' => $uri,
            'data' => $data,
            'meta' => $meta
        );
    }

    /**
     * Overrides the query() method to provide the test case responses
     *
     * @return BBYOpen\Response
     * @throws BBYOpen\Exception
     */
    public function query()
    {
        $uri = $this->getTargetUri();

        // find a testcase that matches the uri, and send that as a response
        foreach ($this->testCases as $packet) {
            if ($uri == $packet['uri']) {
                return new BBYOpen\Response($packet['data'], $packet['meta']);
            }
        }

        // explode if we didn't find any
        throw new Exception('Failed to find test case matching any registered URIs. URI expected: ' . $uri);
    }
}