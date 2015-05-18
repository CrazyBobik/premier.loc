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
 * @version    $Id: Role.php 23775 2011-03-01 17:25:24Z ralph $
 */
class K_Acl_Role implements K_Acl_Role_Interface
{
    /**
     * Unique id of Role
     *
     * @var string
     */
    protected $_roleId;

    /**
     * Sets the Role identifier
     *
     * @param  string $roleId
     * @return void
     */
    public function __construct($roleId)
    {
        $this->_roleId = (string) $roleId;
    }

    /**
     * Defined by K_Acl_Role_Interface; returns the Role identifier
     *
     * @return string
     */
    public function getRoleId()
    {
        return $this->_roleId;
    }

    /**
     * Defined by K_Acl_Role_Interface; returns the Role identifier
     * Proxies to getRoleId()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getRoleId();
    }
}
