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
 * @subpackage Type
 * @author     Matt Williams <matt@mattwilliamsnyc.com>
 * @copyright  Copyright (c) 2008 {@link http://mattwilliamsnyc.com Matt Williams}
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @version    $Id: Exception.php 6 2009-02-01 20:34:37Z mattwilliamsnyc $
 */

/**
 * @see BestBuy_Service_Remix_Exception
 */
require_once 'BestBuy/Service/Remix/Exception.php';

/**
 * Base exception class used by the BestBuy_Service_Remix package.
 *
 * @category   BestBuy
 * @package    BestBuy_Service_Remix
 * @subpackage Type
 * @author     Matt Williams <matt@mattwilliamsnyc.com>
 * @copyright  Copyright (c) 2008 {@link http://mattwilliamsnyc.com Matt Williams}
 */
class BestBuy_Service_Remix_Type_Exception extends BestBuy_Service_Remix_Exception {}
