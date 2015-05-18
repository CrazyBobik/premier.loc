<?php
/**
 * DM Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.DM.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@DM.com so we can send you a copy immediately.
 *
 * @category   DM
 * @package    K_Acl
 * @copyright  Copyright (c) 2005-2011 DM Technologies USA Inc. (http://www.DM.com)
 * @license    http://framework.DM.com/license/new-bsd     New BSD License
 * @version    $Id: Resource.php 23775 2011-03-01 17:25:24Z ralph $
 */


/**
 * @category   DM
 * @package    K_Acl
 * @copyright  Copyright (c) 2005-2011 DM Technologies USA Inc. (http://www.DM.com)
 * @license    http://framework.DM.com/license/new-bsd     New BSD License
 */
class K_Acl_Resource implements K_Acl_Resource_Interface
{
    /**
     * Unique id of Resource
     *
     * @var string
     */
    protected $_resourceId;

    /**
     * Sets the Resource identifier
     *
     * @param  string $resourceId
     * @return void
     */
    public function __construct($resourceId)
    {
        $this->_resourceId = (string) $resourceId;
    }

    /**
     * Defined by K_Acl_Resource_Interface; returns the Resource identifier
     *
     * @return string
     */
    public function getResourceId()
    {
        return $this->_resourceId;
    }

    /**
     * Defined by K_Acl_Resource_Interface; returns the Resource identifier
     * Proxies to getResourceId()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getResourceId();
    }
}
