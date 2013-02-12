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
 * @subpackage Response
 * @author     Matt Williams <matt@mattwilliamsnyc.com>
 * @author     Troy McCabe (v2.0+) <troy.mccabe@geeksquad.com>
 * @copyright  Copyright (c) 2008 {@link http://mattwilliamsnyc.com Matt Williams}
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @version    $Id: Response.php 6 2009-02-01 20:34:37Z mattwilliamsnyc $
 */

namespace BestBuy\Service\Remix;

/**
 * BestBuy\Service\Remix\Response represents a response to a
 * {@link http://remix.bestbuy.com Remix} API call.
 *
 * In addition to the $data property (accessible via $response->data or $response->getData()),
 * a BestBuy\Service\Remix\Response provides the following HTTP metadata collected by
 * {@link http://curl.haxx.se/ cURL}:
 *
 * <ul>
 *   <li>
 *     <b>url</b>
 *     (Last effective URL)
 *   </li>
 *   <li>
 *     <b>content_type</b>
 *     (Response Content-type, NULL indicates server did not send a valid Content-Type)
 *   </li>
 *   <li>
 *     <b>http_code</b>
 *     (Last received HTTP code)
 *   </li>
 *   <li>
 *     <b>header_size</b>
 *     (Total size of all headers received)
 *   </li>
 *   <li>
 *     <b>request_size</b>
 *     (Total size of issued requests)
 *   </li>
 *   <li>
 *     <b>filetime</b>
 *     (Remote time of the retrieved document, if unknown, -1 is returned)
 *   </li>
 *   <li>
 *     <b>ssl_verify_result</b>
 *     (Result of SSL certification verification)
 *   </li>
 *   <li>
 *     <b>redirect_count</b>
 *     (Number of redirection steps before final transaction was started)
 *   </li>
 *   <li>
 *     <b>total_time</b>
 *     (Total transaction time in seconds for last transfer)
 *   </li>
 *   <li>
 *     <b>namelookup_time</b>
 *     (Time in seconds until name resolving was complete)
 *   </li>
 *   <li>
 *     <b>connect_time</b>
 *     (Time in seconds it took to establish the connection)
 *   </li>
 *   <li>
 *     <b>pretransfer_time</b>
 *     (Time in seconds from start until just before file transfer begins)
 *   </li>
 *   <li>
 *     <b>size_upload</b>
 *     (Total number of bytes uploaded)
 *   </li>
 *   <li>
 *     <b>size_download</b>
 *     (Total number of bytes downloaded)
 *   </li>
 *   <li>
 *     <b>speed_download</b>
 *     (Average download speed)
 *   </li>
 *   <li>
 *     <b>speed_upload</b>
 *     (Average upload speed)
 *   </li>
 *   <li>
 *     <b>download_content_length</b>
 *     (Content-length of download, read from Content-Length: field)
 *   </li>
 *   <li>
 *     <b>upload_content_length</b>
 *     (Specified size of upload)
 *   </li>
 *   <li>
 *     <b>starttransfer_time</b>
 *     (Time in seconds until the first byte is about to be transferred)
 *   </li>
 *   <li>
 *     <b>redirect_time</b>
 *     (Time in seconds of all redirection steps before final transaction was started)
 *   </li>
 * </ul>
 *
 * @category   BestBuy
 * @package    BestBuy\Service\Remix
 * @subpackage Response
 * @author     Matt Williams <matt@mattwilliamsnyc.com>
 * @author     Troy McCabe (v2.0+) <troy.mccabe@geeksquad.com>
 * @copyright  Copyright (c) 2008 {@link http://mattwilliamsnyc.com Matt Williams}
 */
class Response
{
    /**
     * Metadata related to the HTTP response collected by cURL
     *
     * @var array
     */
    protected $metadata = array();

    /**
     * Response body (if any) returned by Remix
     *
     * @var string
     */
    protected $data = '';

    /**
     * Creates a new \BestBuy\Service\Remix\Response object.
     *
     * @param string $data Response body (if any) returned by Remix
     * @param array  $meta HTTP {@link http://us3.php.net/curl_getinfo curl_getinfo() metadata}
     */
    public function __construct($data, array $meta)
    {
        $this->data = $data;
        $this->metadata = $meta;
    }

    /**
     * Enables read access to data and curl metadata via object properties (e.g. $response->http_code).
     *
     * @param string $name Name of the property to be accessed
     *
     * @return mixed
     */
    public function __get($name)
    {
        if ('data' == $name) {
            return $this->data;
        } else {
            if (isset($this->metadata[$name])) {
                return $this->metadata[$name];
            } else {
                trigger_error(sprintf('Trying to access non-existant property "%s"', $name), E_USER_WARNING);

                return null;
            }
        }
    }

    /**
     * Enables verification of metadata accessible via object properties (e.g. $response->http_code).
     *
     * @param string $name Name of the property whose presence will be verified
     *
     * @return boolean
     */
    public function __isset($name)
    {
        if ('data' == $name) {
            return isset($this->data);
        }

        return isset($this->metadata[$name]);
    }

    /**
     * Allows casting of this response object to a string; returns raw response body.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->data;
    }

    /**
     * Returns the content body (if any) returned by Remix.
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Checks the HTTP status code of the response for 4xx or 5xx class errors.
     *
     * @return boolean
     */
    public function isError()
    {
        return (4 == ($type = floor($this->metadata['http_code'] / 100)) || 5 == $type);
    }

    /**
     * Returns response document wrapped in a {@link http://us2.php.net/simplexml SimpleXml} object.
     *
     * @return \SimpleXmlElement|boolean
     */
    public function toSimpleXml()
    {
        try {
            $xml = @new \SimpleXmlElement($this->data);
        } catch (\Exception $e) {
            return false;
        }

        return $xml;
    }

    /**
     * Returns response data (and metadata) as an associative array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge($this->metadata, array('data' => $this->data));
    }
}
