<?php

/**
 * @category   DM
 * @package    K_Acl
 */
class K_Acl_Acl {
    /**
     * Rule type: allow
     */
    const TYPE_ALLOW = 'TYPE_ALLOW';

    /**
     * Rule type: deny
     */
    const TYPE_DENY = 'TYPE_DENY';

    /**
     * Rule operation: add
     */
    const OP_ADD = 'OP_ADD';

    /**
     * Rule operation: remove
     */
    const OP_REMOVE = 'OP_REMOVE';

    /**
     * Role registry
     *
     * @var K_Acl_Role_Registry
     */
    protected $_roleRegistry = null;

    /**
     * Resource tree
     *
     * @var array
     */
    protected $_resources = array();

    /**
     * @var K_Acl_Role_Interface
     */
    protected $_isAllowedRole = null;

    /**
     * @var K_Acl_Resource_Interface
     */
    protected $_isAllowedResource = null;

    /**
     * @var String
     */
    protected $_isAllowedPrivilege = null;


    /**
     * @var String
     */
    public $lastResource = null;
    /**
     * ACL rules; whitelist (deny everything to all) by default
     *
     * @var array
     */
    protected $_rules = array( 'allResources' => array( 'allRoles' => array( 'allPrivileges' => array( 'type' => self::TYPE_DENY, 'assert' => null ), 'byPrivilegeId' => array() ), 'byRoleId' => array() ), 'byResourceId' => array() );

    /**
     * Adds a Role having an identifier unique to the registry
     *
     * The $parents parameter may be a reference to, or the string identifier for,
     * a Role existing in the registry, or $parents may be passed as an array of
     * these - mixing string identifiers and objects is ok - to indicate the Roles
     * from which the newly added Role will directly inherit.
     *
     * In order to resolve potential ambiguities with conflicting rules inherited
     * from different parents, the most recently added parent takes precedence over
     * parents that were previously added. In other words, the first parent added
     * will have the least priority, and the last parent added will have the
     * highest priority.
     *
     * @param  K_Acl_Role_Interface              $role
     * @param  K_Acl_Role_Interface|string|array $parents
     * @uses   K_Acl_Role_Registry::add()
     * @return K_Acl Provides a fluent interface
     */
    public function addRole( $role, $parents = null ) {
        if ( is_string( $role ) ) {
            $role = new K_Acl_Role( $role );
        }

        if ( ! $role instanceof K_Acl_Role_Interface ) {

            throw new K_Acl_Exception( 'addRole() expects $role to be of type K_Acl_Role_Interface' );
        }


        $this->_getRoleRegistry()->add( $role, $parents );

        return $this;
    }

    /**
     * Returns the identified Role
     *
     * The $role parameter can either be a Role or Role identifier.
     *
     * @param  K_Acl_Role_Interface|string $role
     * @uses   K_Acl_Role_Registry::get()
     * @return K_Acl_Role_Interface
     */
    public function getRole( $role ) {
        return $this->_getRoleRegistry()->get( $role );
    }

    /**
     * Returns true if and only if the Role exists in the registry
     *
     * The $role parameter can either be a Role or a Role identifier.
     *
     * @param  K_Acl_Role_Interface|string $role
     * @uses   K_Acl_Role_Registry::has()
     * @return boolean
     */
    public function hasRole( $role ) {
        return $this->_getRoleRegistry()->has( $role );
    }

    /**
     * Returns true if and only if $role inherits from $inherit
     *
     * Both parameters may be either a Role or a Role identifier. If
     * $onlyParents is true, then $role must inherit directly from
     * $inherit in order to return true. By default, this method looks
     * through the entire inheritance DAG to determine whether $role
     * inherits from $inherit through its ancestor Roles.
     *
     * @param  K_Acl_Role_Interface|string $role
     * @param  K_Acl_Role_Interface|string $inherit
     * @param  boolean                        $onlyParents
     * @uses   K_Acl_Role_Registry::inherits()
     * @return boolean
     */
    public function inheritsRole( $role, $inherit, $onlyParents = false ) {
        return $this->_getRoleRegistry()->inherits( $role, $inherit, $onlyParents );
    }

    /**
     * Removes the Role from the registry
     *
     * The $role parameter can either be a Role or a Role identifier.
     *
     * @param  K_Acl_Role_Interface|string $role
     * @uses   K_Acl_Role_Registry::remove()
     * @return K_Acl Provides a fluent interface
     */
    public function removeRole( $role ) {
        $this->_getRoleRegistry()->remove( $role );

        if ( $role instanceof K_Acl_Role_Interface ) {
            $roleId = $role->getRoleId();
        } else {
            $roleId = $role;
        }

        foreach ( $this->_rules['allResources']['byRoleId'] as $roleIdCurrent => $rules ) {
            if ( $roleId === $roleIdCurrent ) {
                unset( $this->_rules['allResources']['byRoleId'][$roleIdCurrent] );
            }
        }
        foreach ( $this->_rules['byResourceId'] as $resourceIdCurrent => $visitor ) {
            if ( array_key_exists( 'byRoleId', $visitor ) ) {
                foreach ( $visitor['byRoleId'] as $roleIdCurrent => $rules ) {
                    if ( $roleId === $roleIdCurrent ) {
                        unset( $this->_rules['byResourceId'][$resourceIdCurrent]['byRoleId'][$roleIdCurrent] );
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Removes all Roles from the registry
     *
     * @uses   K_Acl_Role_Registry::removeAll()
     * @return K_Acl Provides a fluent interface
     */
    public function removeRoleAll() {
        $this->_getRoleRegistry()->removeAll();

        foreach ( $this->_rules['allResources']['byRoleId'] as $roleIdCurrent => $rules ) {
            unset( $this->_rules['allResources']['byRoleId'][$roleIdCurrent] );
        }
        foreach ( $this->_rules['byResourceId'] as $resourceIdCurrent => $visitor ) {
            foreach ( $visitor['byRoleId'] as $roleIdCurrent => $rules ) {
                unset( $this->_rules['byResourceId'][$resourceIdCurrent]['byRoleId'][$roleIdCurrent] );
            }
        }

        return $this;
    }

    /**
     * Adds a Resource having an identifier unique to the ACL
     *
     * The $parent parameter may be a reference to, or the string identifier for,
     * the existing Resource from which the newly added Resource will inherit.
     *
     * @param  K_Acl_Resource_Interface|string $resource
     * @param  K_Acl_Resource_Interface|string $parent
     * @throws K_Acl_Exception
     * @return K_Acl Provides a fluent interface
     */
    public function addResource( $resource, $parent = null, $deny_action = null ) {
        
        if ( is_string( $resource ) ) {
            $resource = new K_Acl_Resource( $resource );
        }

        if ( ! $resource instanceof K_Acl_Resource_Interface ) {

            throw new K_Acl_Exception( 'addResource() expects $resource to be of type K_Acl_Resource_Interface' );
        }
        
        $resourceId = $resource->getResourceId();

        if ( $this->has( $resourceId ) ) {

            throw new K_Acl_Exception( "Resource id '$resourceId' already exists in the ACL" );
        }

        $resourceParent = null;

        if ( null !== $parent ) {
          
                if ( $parent instanceof K_Acl_Resource_Interface ) {
                    $resourceParentId = $parent->getResourceId();
                } else {
                    $resourceParentId = $parent;
                }
                $resourceParent = $this->get( $resourceParentId );
                
            if($resourceParent==false) {

                throw new K_Acl_Exception( "Parent Resource id '$resourceParent' does not exist" );
            }
            $this->_resources[$resourceParentId]['children'][$resourceId] = $resource;
        }

        $this->_resources[$resourceId] = array(
            'instance' => $resource,
            'parent' => $resourceParent,
            'children' => array(),
            'denyaction' => $deny_action );

        return $this;
    }

    /**
     * Adds a Resource having an identifier unique to the ACL
     *
     * The $parent parameter may be a reference to, or the string identifier for,
     * the existing Resource from which the newly added Resource will inherit.
     *
     * @deprecated in version 1.9.1 and will be available till 2.0.  New code
     *             should use addResource() instead.
     *
     * @param  K_Acl_Resource_Interface        $resource
     * @param  K_Acl_Resource_Interface|string $parent
     * @throws K_Acl_Exception
     * @return K_Acl Provides a fluent interface
     */
    public function add( K_Acl_Resource_Interface $resource, $parent = null, $deny_action = null ) {
        return $this->addResource( $resource, $parent, $deny_action );
    }

    /**
     * Returns the identified Resource
     *
     * The $resource parameter can either be a Resource or a Resource identifier.
     *
     * @param  K_Acl_Resource_Interface|string $resource
     * @throws K_Acl_Exception
     * @return K_Acl_Resource_Interface
     */
    public function get( $resource ) {
        if ( $resource instanceof K_Acl_Resource_Interface ) {
            $resourceId = $resource->getResourceId();
        } else {
            $resourceId = ( string )$resource;
        }

        if ( ! $this->has( $resource ) ) {
           return false;
        }

        return $this->_resources[$resourceId]['instance'];
    }

    /**
     * Returns true if and only if the Resource exists in the ACL
     *
     * The $resource parameter can either be a Resource or a Resource identifier.
     *
     * @param  K_Acl_Resource_Interface|string $resource
     * @return boolean
     */
    public function has( $resource ) {
        if ( $resource instanceof K_Acl_Resource_Interface ) {
            $resourceId = $resource->getResourceId();
        } else {
            $resourceId = ( string )$resource;
        }

        return isset( $this->_resources[$resourceId] );
    }

    /**
     * Returns true if and only if $resource inherits from $inherit
     *
     * Both parameters may be either a Resource or a Resource identifier. If
     * $onlyParent is true, then $resource must inherit directly from
     * $inherit in order to return true. By default, this method looks
     * through the entire inheritance tree to determine whether $resource
     * inherits from $inherit through its ancestor Resources.
     *
     * @param  K_Acl_Resource_Interface|string $resource
     * @param  K_Acl_Resource_Interface|string $inherit
     * @param  boolean                            $onlyParent
     * @throws K_Acl_Resource_Registry_Exception
     * @return boolean
     */
    public function inherits( $resource, $inherit, $onlyParent = false ) {
      
            $res=$this->get( $resource );
            $inhr=$this->get( $inherit );
               if (!$res or !$inhr){
           
                 throw new K_Acl_Exception( 'Нет ресурсов' );
            }
            $resourceId = $this->get( $resource )->getResourceId();
            $inheritId = $this->get( $inherit )->getResourceId();
 
        if ( null !== $this->_resources[$resourceId]['parent'] ) {
            $parentId = $this->_resources[$resourceId]['parent']->getResourceId();
            if ( $inheritId === $parentId ) {
                return true;
            } else
                if ( $onlyParent ) {
                    return false;
                }
        } else {
            return false;
        }

        while ( null !== $this->_resources[$parentId]['parent'] ) {
            $parentId = $this->_resources[$parentId]['parent']->getResourceId();
            if ( $inheritId === $parentId ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Removes a Resource and all of its children
     *
     * The $resource parameter can either be a Resource or a Resource identifier.
     *
     * @param  K_Acl_Resource_Interface|string $resource
     * @throws K_Acl_Exception
     * @return K_Acl Provides a fluent interface
     */
    public function remove( $resource ) {
     
       $res = $this->get( $resource );
       
        if ($res == false) {
         return $this;
        }
 
       $resourceId = $res->getResourceId();
 
        $resourcesRemoved = array( $resourceId );
        if ( null !== ( $resourceParent = $this->_resources[$resourceId]['parent'] ) ) {
            unset( $this->_resources[$resourceParent->getResourceId()]['children'][$resourceId] );
        }
        foreach ( $this->_resources[$resourceId]['children'] as $childId => $child ) {
            $this->remove( $childId );
            $resourcesRemoved[] = $childId;
        }
          // удаляет и правила для рессурса
        foreach ( $resourcesRemoved as $resourceIdRemoved ) {
            foreach ( $this->_rules['byResourceId'] as $resourceIdCurrent => $rules ) {
                if ( $resourceIdRemoved === $resourceIdCurrent ) {
                    unset( $this->_rules['byResourceId'][$resourceIdCurrent] );
                }
            }
        }

        unset( $this->_resources[$resourceId] );


        return $this;
    }

    public function getDeneyAction($last_resourse) {

        $resource = $this->get( $last_resourse );
        
        
        if (!$resource==false){
        $deny_action = $this->_resources[$resource->getResourceId()]['denyaction'];


        while ( $deny_action == null and $this->_resources[$resource->getResourceId()]['parent'] ) {
            // try next Resource
            $resource = $this->_resources[$resource->getResourceId()]['parent'];

            $deny_action = $this->_resources[$resource->getResourceId()]['denyaction'];
        } // loop terminates at 'allResources' pseudo-parent

        return $deny_action;
        }
        else
        return false;
    }

    /**
     * Removes all Resources
     *
     * @return K_Acl Provides a fluent interface
     */
    public function removeAll() {
        foreach ( $this->_resources as $resourceId => $resource ) {
            foreach ( $this->_rules['byResourceId'] as $resourceIdCurrent => $rules ) {
                if ( $resourceId === $resourceIdCurrent ) {
                    unset( $this->_rules['byResourceId'][$resourceIdCurrent] );
                }
            }
        }

        $this->_resources = array();

        return $this;
    }

    /**
     * Adds an "allow" rule to the ACL
     *
     * @param  K_Acl_Role_Interface|string|array     $roles
     * @param  K_Acl_Resource_Interface|string|array $resources
     * @param  string|array                             $privileges
     * @param  K_Acl_Assert_Interface                $assert
     * @uses   K_Acl::setRule()
     * @return K_Acl Provides a fluent interface
     */
    public function allow( $roles = null, $resources = null, $privileges = null, K_Acl_Assert_Interface $assert = null ) {
        return $this->setRule( self::OP_ADD, self::TYPE_ALLOW, $roles, $resources, $privileges, $assert );
    }

    /**
     * Adds a "deny" rule to the ACL
     *
     * @param  K_Acl_Role_Interface|string|array     $roles
     * @param  K_Acl_Resource_Interface|string|array $resources
     * @param  string|array                             $privileges
     * @param  K_Acl_Assert_Interface                $assert
     * @uses   K_Acl::setRule()
     * @return K_Acl Provides a fluent interface
     */
    public function deny( $roles = null, $resources = null, $privileges = null, K_Acl_Assert_Interface $assert = null ) {
        return $this->setRule( self::OP_ADD, self::TYPE_DENY, $roles, $resources, $privileges, $assert );
    }

    /**
     * Removes "allow" permissions from the ACL
     *
     * @param  K_Acl_Role_Interface|string|array     $roles
     * @param  K_Acl_Resource_Interface|string|array $resources
     * @param  string|array                             $privileges
     * @uses   K_Acl::setRule()
     * @return K_Acl Provides a fluent interface
     */
    public function removeAllow( $roles = null, $resources = null, $privileges = null ) {
        return $this->setRule( self::OP_REMOVE, self::TYPE_ALLOW, $roles, $resources, $privileges );
    }

    /**
     * Removes "deny" restrictions from the ACL
     *
     * @param  K_Acl_Role_Interface|string|array     $roles
     * @param  K_Acl_Resource_Interface|string|array $resources
     * @param  string|array                             $privileges
     * @uses   K_Acl::setRule()
     * @return K_Acl Provides a fluent interface
     */
    public function removeDeny( $roles = null, $resources = null, $privileges = null ) {
        return $this->setRule( self::OP_REMOVE, self::TYPE_DENY, $roles, $resources, $privileges );
    }

    /**
     * Performs operations on ACL rules
     *
     * The $operation parameter may be either OP_ADD or OP_REMOVE, depending on whether the
     * user wants to add or remove a rule, respectively:
     *
     * OP_ADD specifics:
     *
     *      A rule is added that would allow one or more Roles access to [certain $privileges
     *      upon] the specified Resource(s).
     *
     * OP_REMOVE specifics:
     *
     *      The rule is removed only in the context of the given Roles, Resources, and privileges.
     *      Existing rules to which the remove operation does not apply would remain in the
     *      ACL.
     *
     * The $type parameter may be either TYPE_ALLOW or TYPE_DENY, depending on whether the
     * rule is intended to allow or deny permission, respectively.
     *
     * The $roles and $resources parameters may be references to, or the string identifiers for,
     * existing Resources/Roles, or they may be passed as arrays of these - mixing string identifiers
     * and objects is ok - to indicate the Resources and Roles to which the rule applies. If either
     * $roles or $resources is null, then the rule applies to all Roles or all Resources, respectively.
     * Both may be null in order to work with the default rule of the ACL.
     *
     * The $privileges parameter may be used to further specify that the rule applies only
     * to certain privileges upon the Resource(s) in question. This may be specified to be a single
     * privilege with a string, and multiple privileges may be specified as an array of strings.
     *
     * If $assert is provided, then its assert() method must return true in order for
     * the rule to apply. If $assert is provided with $roles, $resources, and $privileges all
     * equal to null, then a rule having a type of:
     *
     *      TYPE_ALLOW will imply a type of TYPE_DENY, and
     *
     *      TYPE_DENY will imply a type of TYPE_ALLOW
     *
     * when the rule's assertion fails. This is because the ACL needs to provide expected
     * behavior when an assertion upon the default ACL rule fails.
     *
     * @param  string                                   $operation
     * @param  string                                   $type
     * @param  K_Acl_Role_Interface|string|array     $roles
     * @param  K_Acl_Resource_Interface|string|array $resources
     * @param  string|array                             $privileges
     * @param  K_Acl_Assert_Interface                $assert
     * @throws K_Acl_Exception
     * @uses   K_Acl_Role_Registry::get()
     * @uses   K_Acl::get()
     * @return K_Acl Provides a fluent interface
     */
    public function setRule( $operation, $type, $roles = null, $resources = null, $privileges = null, K_Acl_Assert_Interface $assert = null ) {
        // ensure that the rule type is valid; normalize input to uppercase
        $type = strtoupper( $type );
        if ( self::TYPE_ALLOW !== $type && self::TYPE_DENY !== $type ) {

            throw new K_Acl_Exception( "Unsupported rule type; must be either '" . self::TYPE_ALLOW . "' or '" . self::TYPE_DENY . "'" );
        }

        // ensure that all specified Roles exist; normalize input to array of Role objects or null
        if ( ! is_array( $roles ) ) {
            $roles = array( $roles );
        } else
            if ( 0 === count( $roles ) ) {
                $roles = array( null );
            }
        $rolesTemp = $roles;
        $roles = array();
        foreach ( $rolesTemp as $role ) {
            if ( null !== $role ) {
                $roles[] = $this->_getRoleRegistry()->get( $role );
            } else {
                $roles[] = null;
            }
        }
        unset( $rolesTemp );

        // ensure that all specified Resources exist; normalize input to array of Resource objects or null
        if ( $resources !== null ) {
            if ( ! is_array( $resources ) ) {
                $resources = array( $resources );
            } else
                if ( 0 === count( $resources ) ) {
                    $resources = array( null );
                }
            $resourcesTemp = $resources;
            $resources = array();
            foreach ( $resourcesTemp as $resource ) {
                if ( null !== $resource ) {
                    $res=$this->get( $resource );
                        if($res==false){
                            throw new K_Acl_Exception( "Resurce" );
                        }
                    $resources[] = $res;
                    
                } else {
                    $resources[] = null;
                }
            }
            unset( $resourcesTemp, $resource );
        } else {
            $allResources = array(); // this might be used later if resource iteration is required
            foreach ( $this->_resources as $rTarget ) {
                $allResources[] = $rTarget['instance'];
            }
            unset( $rTarget );
        }

        // normalize privileges to array
        if ( null === $privileges ) {
            $privileges = array();
        } else
            if ( ! is_array( $privileges ) ) {
                $privileges = array( $privileges );
            }

        switch ( $operation ) {

                // add to the rules
            case self::OP_ADD:
                if ( $resources !== null ) {
                    // this block will iterate the provided resources
                    foreach ( $resources as $resource ) {
                        foreach ( $roles as $role ) {
                            $rules = &$this->_getRules( $resource, $role, true );
                            if ( 0 === count( $privileges ) ) {
                                $rules['allPrivileges']['type'] = $type;
                                $rules['allPrivileges']['assert'] = $assert;
                                if ( ! isset( $rules['byPrivilegeId'] ) ) {
                                    $rules['byPrivilegeId'] = array();
                                }
                            } else {
                                foreach ( $privileges as $privilege ) {
                                    $rules['byPrivilegeId'][$privilege]['type'] = $type;
                                    $rules['byPrivilegeId'][$privilege]['assert'] = $assert;
                                }
                            }
                        }
                    }
                } else {
                    // this block will apply to all resources in a global rule
                    foreach ( $roles as $role ) {
                        $rules = &$this->_getRules( null, $role, true );
                        if ( 0 === count( $privileges ) ) {
                            $rules['allPrivileges']['type'] = $type;
                            $rules['allPrivileges']['assert'] = $assert;
                        } else {
                            foreach ( $privileges as $privilege ) {
                                $rules['byPrivilegeId'][$privilege]['type'] = $type;
                                $rules['byPrivilegeId'][$privilege]['assert'] = $assert;
                            }
                        }
                    }
                }
                break;

                // remove from the rules
            case self::OP_REMOVE:
                if ( $resources !== null ) {
                    // this block will iterate the provided resources
                    foreach ( $resources as $resource ) {
                        foreach ( $roles as $role ) {
                            $rules = &$this->_getRules( $resource, $role );
                            if ( null === $rules ) {
                                continue;
                            }
                            if ( 0 === count( $privileges ) ) {
                                if ( null === $resource && null === $role ) {
                                    if ( $type === $rules['allPrivileges']['type'] ) {
                                        $rules = array( 'allPrivileges' => array( 'type' => self::TYPE_DENY, 'assert' => null ), 'byPrivilegeId' => array() );
                                    }
                                    continue;
                                }

                                if ( isset( $rules['allPrivileges']['type'] ) && $type === $rules['allPrivileges']['type'] ) {
                                    unset( $rules['allPrivileges'] );
                                }
                            } else {
                                foreach ( $privileges as $privilege ) {
                                    if ( isset( $rules['byPrivilegeId'][$privilege] ) && $type === $rules['byPrivilegeId'][$privilege]['type'] ) {
                                        unset( $rules['byPrivilegeId'][$privilege] );
                                    }
                                }
                            }
                        }
                    }
                } else {
                    // this block will apply to all resources in a global rule
                    foreach ( $roles as $role ) {
                        /**
                         * since null (all resources) was passed to this setRule() call, we need
                         * clean up all the rules for the global allResources, as well as the indivually
                         * set resources (per privilege as well)
                         */
                        foreach ( array_merge( array( null ), $allResources ) as $resource ) {
                            $rules = &$this->_getRules( $resource, $role, true );
                            if ( null === $rules ) {
                                continue;
                            }
                            if ( 0 === count( $privileges ) ) {
                                if ( null === $role ) {
                                    if ( $type === $rules['allPrivileges']['type'] ) {
                                        $rules = array( 'allPrivileges' => array( 'type' => self::TYPE_DENY, 'assert' => null ), 'byPrivilegeId' => array() );
                                    }
                                    continue;
                                }

                                if ( isset( $rules['allPrivileges']['type'] ) && $type === $rules['allPrivileges']['type'] ) {
                                    unset( $rules['allPrivileges'] );
                                }
                            } else {
                                foreach ( $privileges as $privilege ) {
                                    if ( isset( $rules['byPrivilegeId'][$privilege] ) && $type === $rules['byPrivilegeId'][$privilege]['type'] ) {
                                        unset( $rules['byPrivilegeId'][$privilege] );
                                    }
                                }
                            }
                        }
                    }
                }
                break;

            default:

                throw new K_Acl_Exception( "Unsupported operation; must be either '" );
        }

        return $this;
    }

    /**
     * Returns true if and only if the Role has access to the Resource
     *
     * The $role and $resource parameters may be references to, or the string identifiers for,
     * an existing Resource and Role combination.
     *
     * If either $role or $resource is null, then the query applies to all Roles or all Resources,
     * respectively. Both may be null to query whether the ACL has a "blacklist" rule
     * (allow everything to all). By default, K_Acl creates a "whitelist" rule (deny
     * everything to all), and this method would return false unless this default has
     * been overridden (i.e., by executing $acl->allow()).
     *
     * If a $privilege is not provided, then this method returns false if and only if the
     * Role is denied access to at least one privilege upon the Resource. In other words, this
     * method returns true if and only if the Role is allowed all privileges on the Resource.
     *
     * This method checks Role inheritance using a depth-first traversal of the Role registry.
     * The highest priority parent (i.e., the parent most recently added) is checked first,
     * and its respective parents are checked similarly before the lower-priority parents of
     * the Role are checked.
     *
     * @param  K_Acl_Role_Interface|string     $role
     * @param  K_Acl_Resource_Interface|string $resource
     * @param  string                             $privilege
     * @uses   K_Acl::get()
     * @uses   K_Acl_Role_Registry::get()
     * @return boolean
     */
    public function isAllowed( $role = null, $resource = null, $privileges = null ) {
        
        $this->lastResource = $this->get( $resource );
         
        if (!$this->lastResource==false or $resource==null){
            $roles = ( ! is_array( $role ) ) ? array( $role ) : $role;
            $privileges = ( ! is_array( $privileges ) ) ? array( $privileges ) : $privileges;
            foreach ( $roles as $role ) {
                 foreach ( $privileges as $privilege ) {
                    if ( $this->_isAllowed( $role, $resource, $privilege ) ) return true;
                }
            }
        }
        // none of given Roles were granted privilege to ressource...
        return false;
    }

    public function _isAllowed( $role = null, $resource = null, $privilege = null ) {

        // reset role & resource to null
        $this->_isAllowedRole = null;
        $this->_isAllowedResource = null;
        $this->_isAllowedPrivilege = null;

        if ( null !== $role ) {
            // keep track of originally called role
            $this->_isAllowedRole = $role;
            $role = $this->_getRoleRegistry()->get( $role );
            if ( ! $this->_isAllowedRole instanceof K_Acl_Role_Interface ) {
                $this->_isAllowedRole = $role;
            }
        }

        if ( null !== $resource ) {
            // keep track of originally called resource
            $this->_isAllowedResource = $resource;
            $resource = $this->get( $resource );
            
            
             if ( ! $this->_isAllowedResource instanceof K_Acl_Resource_Interface ) {
                $this->_isAllowedResource = $resource;
            }
        }
        //var_dump($privilege);
        if ( null === $privilege ) {
            // query on all privileges
            do {
                // depth-first search on $role if it is not 'allRoles' pseudo-parent
                if ( null !== $role && null !== ( $result = $this->_roleDFSAllPrivileges( $role, $resource, $privilege ) ) ) {
                    return $result;
                }

                // look for rule on 'allRoles' psuedo-parent
                if ( null !== ( $rules = $this->_getRules( $resource, null ) ) ) {
                    foreach ( $rules['byPrivilegeId'] as $privilege => $rule ) {
                        if ( self::TYPE_DENY === ( $ruleTypeOnePrivilege = $this->_getRuleType( $resource, null, $privilege ) ) ) {
                            return false;
                        }
                    }
                    if ( null !== ( $ruleTypeAllPrivileges = $this->_getRuleType( $resource, null, null ) ) ) {
                        return self::TYPE_ALLOW === $ruleTypeAllPrivileges;
                    }
                }

                // try next Resource
                $resource = $this->_resources[$resource->getResourceId()]['parent'];
           } while ( true );
        }else if (true === $privilege) { // проверяеть есть ли хоть одна привелегия для этого ресурса или ноды 
              // query on all privileges
            do {   
                // var_dump($role );
                
                // var_dump($resource);
                // look for rule on 'allRoles' psuedo-parent
                if ( null !== ( $rules = $this->_getRules( $resource, $role ) ) ) {
                   // var_dump($rules);
                   if($rules['allPrivileges']["type"]==self::TYPE_ALLOW){
                      return true;
                      
                   } else if ($rules['allPrivileges']["type"]==self::TYPE_DENY) {
                      return false;   
                   }
                   
                   $denay=false;
                   foreach ( $rules['byPrivilegeId'] as $privilege => $rule ) {
                   //    var_dump($this->_getRuleType( $resource, $role, $privilege ));
                        if (self::TYPE_ALLOW == $rule["type"]) {
                            return true;
                        }
                        else if ( self::TYPE_DENY == $rule["type"]) {
                            $denay=true;   
                        }
                    }
           
                    if($denay){
                         return false;
                    }
                    
                  }
                
            } while ( $resource = $this->_resources[$resource->getResourceId()]['parent']); // loop terminates at 'allResources' pseudo-parent
        }
        else
         {
            $this->_isAllowedPrivilege = $privilege;
            // query on one privilege
            do {
                // depth-first search on $role if it is not 'allRoles' pseudo-parent
                if ( null !== $role && null !== ( $result = $this->_roleDFSOnePrivilege( $role, $resource, $privilege ) ) ) {
                    return $result;
                }

                // look for rule on 'allRoles' pseudo-parent
                if ( null !== ( $ruleType = $this->_getRuleType( $resource, null, $privilege ) ) ) {
                    return self::TYPE_ALLOW === $ruleType;
                } else
                    if ( null !== ( $ruleTypeAllPrivileges = $this->_getRuleType( $resource, null, null ) ) ) {
                        return self::TYPE_ALLOW === $ruleTypeAllPrivileges;
                    }

                // try next Resource
                $resource = $this->_resources[$resource->getResourceId()]['parent'];

            } while ( true ); // loop terminates at 'allResources' pseudo-parent
        }
        
         return false;
    }

    /**
     * Returns the Role registry for this ACL
     *
     * If no Role registry has been created yet, a new default Role registry
     * is created and returned.
     *
     * @return K_Acl_Role_Registry
     */
    protected function _getRoleRegistry() {
        if ( null === $this->_roleRegistry ) {
            $this->_roleRegistry = new K_Acl_Role_Registry();
        }
        return $this->_roleRegistry;
    }

    /**
     * Performs a depth-first search of the Role DAG, starting at $role, in order to find a rule
     * allowing/denying $role access to all privileges upon $resource
     *
     * This method returns true if a rule is found and allows access. If a rule exists and denies access,
     * then this method returns false. If no applicable rule is found, then this method returns null.
     *
     * @param  K_Acl_Role_Interface     $role
     * @param  K_Acl_Resource_Interface $resource
     * @return boolean|null
     */
    protected function _roleDFSAllPrivileges( K_Acl_Role_Interface $role, K_Acl_Resource_Interface $resource = null ) {
        $dfs = array( 'visited' => array(), 'stack' => array() );

        if ( null !== ( $result = $this->_roleDFSVisitAllPrivileges( $role, $resource, $dfs ) ) ) {
            return $result;
        }

        while ( null !== ( $role = array_pop( $dfs['stack'] ) ) ) {
            if ( ! isset( $dfs['visited'][$role->getRoleId()] ) ) {
                if ( null !== ( $result = $this->_roleDFSVisitAllPrivileges( $role, $resource, $dfs ) ) ) {
                    return $result;
                }
            }
        }

        return null;
    }

    /**
     * Visits an $role in order to look for a rule allowing/denying $role access to all privileges upon $resource
     *
     * This method returns true if a rule is found and allows access. If a rule exists and denies access,
     * then this method returns false. If no applicable rule is found, then this method returns null.
     *
     * This method is used by the internal depth-first search algorithm and may modify the DFS data structure.
     *
     * @param  K_Acl_Role_Interface     $role
     * @param  K_Acl_Resource_Interface $resource
     * @param  array                  $dfs
     * @return boolean|null
     * @throws K_Acl_Exception
     */
    protected function _roleDFSVisitAllPrivileges( K_Acl_Role_Interface $role, K_Acl_Resource_Interface $resource = null, &$dfs = null ) {
        if ( null === $dfs ) {
            /**
             * @see K_Acl_Exception
             */

            throw new K_Acl_Exception( '$dfs parameter may not be null' );
        }

        if ( null !== ( $rules = $this->_getRules( $resource, $role ) ) ) {
            foreach ( $rules['byPrivilegeId'] as $privilege => $rule ) {
                if ( self::TYPE_DENY === ( $ruleTypeOnePrivilege = $this->_getRuleType( $resource, $role, $privilege ) ) ) {
                    return false;
                }
            }
            if ( null !== ( $ruleTypeAllPrivileges = $this->_getRuleType( $resource, $role, null ) ) ) {
                return self::TYPE_ALLOW === $ruleTypeAllPrivileges;
            }
        }

        $dfs['visited'][$role->getRoleId()] = true;
        foreach ( $this->_getRoleRegistry()->getParents( $role ) as $roleParentId => $roleParent ) {
            $dfs['stack'][] = $roleParent;
        }

        return null;
    }

    /**
     * Performs a depth-first search of the Role DAG, starting at $role, in order to find a rule
     * allowing/denying $role access to a $privilege upon $resource
     *
     * This method returns true if a rule is found and allows access. If a rule exists and denies access,
     * then this method returns false. If no applicable rule is found, then this method returns null.
     *
     * @param  K_Acl_Role_Interface     $role
     * @param  K_Acl_Resource_Interface $resource
     * @param  string                      $privilege
     * @return boolean|null
     * @throws K_Acl_Exception
     */
    protected function _roleDFSOnePrivilege( K_Acl_Role_Interface $role, K_Acl_Resource_Interface $resource = null, $privilege = null ) {
        if ( null === $privilege ) {

            throw new K_Acl_Exception( '$privilege parameter may not be null' );
        }

        $dfs = array( 'visited' => array(), 'stack' => array() );

        if ( null !== ( $result = $this->_roleDFSVisitOnePrivilege( $role, $resource, $privilege, $dfs ) ) ) {
            return $result;
        }

        while ( null !== ( $role = array_pop( $dfs['stack'] ) ) ) {
            if ( ! isset( $dfs['visited'][$role->getRoleId()] ) ) {
                if ( null !== ( $result = $this->_roleDFSVisitOnePrivilege( $role, $resource, $privilege, $dfs ) ) ) {
                    return $result;
                }
            }
        }

        return null;
    }

    /**
     * Visits an $role in order to look for a rule allowing/denying $role access to a $privilege upon $resource
     *
     * This method returns true if a rule is found and allows access. If a rule exists and denies access,
     * then this method returns false. If no applicable rule is found, then this method returns null.
     *
     * This method is used by the internal depth-first search algorithm and may modify the DFS data structure.
     *
     * @param  K_Acl_Role_Interface     $role
     * @param  K_Acl_Resource_Interface $resource
     * @param  string                      $privilege
     * @param  array                       $dfs
     * @return boolean|null
     * @throws K_Acl_Exception
     */
    protected function _roleDFSVisitOnePrivilege( K_Acl_Role_Interface $role, K_Acl_Resource_Interface $resource = null, $privilege = null, &$dfs = null ) {
        if ( null === $privilege ) {
            /**
             * @see K_Acl_Exception
             */

            throw new K_Acl_Exception( '$privilege parameter may not be null' );
        }

        if ( null === $dfs ) {
            /**
             * @see K_Acl_Exception
             */

            throw new K_Acl_Exception( '$dfs parameter may not be null' );
        }

        if ( null !== ( $ruleTypeOnePrivilege = $this->_getRuleType( $resource, $role, $privilege ) ) ) {
            return self::TYPE_ALLOW === $ruleTypeOnePrivilege;
        } else
            if ( null !== ( $ruleTypeAllPrivileges = $this->_getRuleType( $resource, $role, null ) ) ) {
                return self::TYPE_ALLOW === $ruleTypeAllPrivileges;
            }

        $dfs['visited'][$role->getRoleId()] = true;
        foreach ( $this->_getRoleRegistry()->getParents( $role ) as $roleParentId => $roleParent ) {
            $dfs['stack'][] = $roleParent;
        }

        return null;
    }

    /**
     * Returns the rule type associated with the specified Resource, Role, and privilege
     * combination.
     *
     * If a rule does not exist or its attached assertion fails, which means that
     * the rule is not applicable, then this method returns null. Otherwise, the
     * rule type applies and is returned as either TYPE_ALLOW or TYPE_DENY.
     *
     * If $resource or $role is null, then this means that the rule must apply to
     * all Resources or Roles, respectively.
     *
     * If $privilege is null, then the rule must apply to all privileges.
     *
     * If all three parameters are null, then the default ACL rule type is returned,
     * based on whether its assertion method passes.
     *
     * @param  K_Acl_Resource_Interface $resource
     * @param  K_Acl_Role_Interface     $role
     * @param  string                      $privilege
     * @return string|null
     */
    protected function _getRuleType( K_Acl_Resource_Interface $resource = null, K_Acl_Role_Interface $role = null, $privilege = null ) {
        // get the rules for the $resource and $role
        if ( null === ( $rules = $this->_getRules( $resource, $role ) ) ) {
            return null;
        }

        // follow $privilege
        if ( null === $privilege ) {
            if ( isset( $rules['allPrivileges'] ) ) {
                $rule = $rules['allPrivileges'];
            } else {
                return null;
            }
        } else
            if ( ! isset( $rules['byPrivilegeId'][$privilege] ) ) {
                return null;
            } else {
                   $rule = $rules['byPrivilegeId'][$privilege];
                   if ( true === $privilege ){
                            return $rule['type'];
                   }
            }
           

            // check assertion first
            if ( $rule['assert'] ) {
                $assertion = $rule['assert'];
                $assertionValue = $assertion->assert( $this, ( $this->_isAllowedRole instanceof K_Acl_Role_Interface ) ? $this->_isAllowedRole : $role, ( $this->_isAllowedResource instanceof K_Acl_Resource_Interface ) ? $this->_isAllowedResource : $resource, $this->_isAllowedPrivilege );
            }

        if ( null === $rule['assert'] || $assertionValue ) {
            return $rule['type'];
        } else
            if ( null !== $resource || null !== $role || null !== $privilege ) {
                return null;
            } else
                if ( self::TYPE_ALLOW === $rule['type'] ) {
                    return self::TYPE_DENY;
                } else {
                    return self::TYPE_ALLOW;
                }
    }

    /**
     * Returns the rules associated with a Resource and a Role, or null if no such rules exist
     *
     * If either $resource or $role is null, this means that the rules returned are for all Resources or all Roles,
     * respectively. Both can be null to return the default rule set for all Resources and all Roles.
     *
     * If the $create parameter is true, then a rule set is first created and then returned to the caller.
     *
     * @param  K_Acl_Resource_Interface $resource
     * @param  K_Acl_Role_Interface     $role
     * @param  boolean                     $create
     * @return array|null
     */
    protected function &_getRules( K_Acl_Resource_Interface $resource = null, K_Acl_Role_Interface $role = null, $create = false ) {
        // create a reference to null
        $null = null;
        $nullRef = &$null;

        // follow $resource
        do {
            if ( null === $resource ) {
                $visitor = &$this->_rules['allResources'];
                break;
            }
            $resourceId = $resource->getResourceId();
            if ( ! isset( $this->_rules['byResourceId'][$resourceId] ) ) {
                if ( ! $create ) {
                    return $nullRef;
                }
                $this->_rules['byResourceId'][$resourceId] = array();
            }
            $visitor = &$this->_rules['byResourceId'][$resourceId];
        } while ( false );


        // follow $role
        if ( null === $role ) {
            if ( ! isset( $visitor['allRoles'] ) ) {
                if ( ! $create ) {
                    return $nullRef;
                }
                $visitor['allRoles']['byPrivilegeId'] = array();
            }
            return $visitor['allRoles'];
        }
        $roleId = $role->getRoleId();
        if ( ! isset( $visitor['byRoleId'][$roleId] ) ) {
            if ( ! $create ) {
                return $nullRef;
            }
            $visitor['byRoleId'][$roleId]['byPrivilegeId'] = array();
            $visitor['byRoleId'][$roleId]['allPrivileges'] = array( 'type' => null, 'assert' => null );
        }
        return $visitor['byRoleId'][$roleId];
    }
    
    
    /**
     * Получение всех массивов с информацием из класса, для раздельного хранения 
     * 
     * 
     * 
     *     
    */
    public function grabRoles() {
        return $this->_getRoleRegistry()->getRoles();
    }

    public function grabResources() {
        return  $this->_resources;
    }
    
    public function grabRules() {
        return  $this->_rules;
    }
    
    /**
     * Устанавливаем массивы по отдельности 
     * 
     * 
     * 
     *     
    */
    
    public function putRoles($roles) {
        
        if(!is_array($roles)){
            return false;
        }        
         $this->_getRoleRegistry()->setRoles($roles);
       return true;
    }

    public function putResources($resources) {
        
        if(!is_array($resources)){
            return false;
        }   
          $this->_resources = $resources;
        return true;
    }
    
    public function putRules($rules) {
        
        if(!!is_array($rules)){
            return false;
        }   
         $this->_rules = $rules;
        return true;
    }
    

    /**
     * Returns an array of registered roles.
     *
     * Note that this method does not return instances of registered roles,
     * but only the role identifiers.
     *
     * @return array of registered roles
     */
    public function getRoles() {
        return array_keys( $this->_getRoleRegistry()->getRoles() );
    }

    /**
     * @return array of registered resources
     */
    public function getResources() {
        return array_keys( $this->_resources );
    }

}
