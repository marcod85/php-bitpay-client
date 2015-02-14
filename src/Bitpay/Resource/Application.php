<?php
/**
 * PHP Client Library for the new cryptographically secure BitPay API.
 *
 * @copyright  Copyright 2011-2014 BitPay, Inc.
 * @author     Integrations Development Team <integrations@bitpay.com>
 * @license    https://raw.githubusercontent.com/bitpay/php-bitpay-client/master/LICENSE The MIT License (MIT)
 * @see        https://github.com/bitpay/php-bitpay-client
 * @package    Bitpay
 * @since      2.0.0
 * @version    2.2.2
 * @filesource
 */

namespace Bitpay\Resource;

/**
 * This class represents an application for a new merchant account.
 * A single application may include multiple merchant contact users
 * and multiple organizations.
 * @see https://bitpay.com/api#resource-Applications
 *
 * @package Bitpay
 */
class Application extends Resource
{
    /**
     * @var array
     */
    private $users;

    /**
     * @var array
     */
    private $orgs;

    /**
     * Public constructor method to initialize class properties.
     *
     * @param array $users  An array of User objects
     * @param array $orgs   An array of Org objects
     */
    public function __construct($users = null, $orgs = null)
    {
    	if (true === isset($users) && false === empty($users) && true === is_array($users)) {
    		$this->users = $users;
    	} else {
    		$this->users = array();
    	}

    	if (true === isset($users) && false === empty($users) && true === is_array($users)) {
    		$this->orgs  = $orgs;
    	} else {
            $this->orgs  = array();
    	}
    }

    /**
     * Returns the array of users
     *
     * @return array
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Returns the array of organizations
     *
     * @return array
     */
    public function getOrgs()
    {
        return $this->orgs;
    }

    /**
     * Add user to stack if not in array and
     * returns the Application object.
     *
     * @param  User        $user
     * @return Application $this
     */
    public function addUser(User $user)
    {
        if (true === isset($user) && false === empty($user)) {
            /*
             * Use scrict type checking to see if this $user is
             * already in the $users array and add if not.
             */
            if (false === in_array($user, $this->users, true)) {
                $this->users[] = $user;
            }
        }

        return $this;
    }

    /**
     * Add organization to stack if not in array
     * and returns the Application object.
     *
     * @param OrgInterface $org
     * @return Application $this
     */
    public function addOrg(Org $org)
    {
        if (true === isset($org) && false === empty($org)) {
            /*
             * Use scrict type checking to see if this $org is
             * already in the $orgs array and add if not.
             */
            if (false === in_array($org, $this->orgs, true)) {
                $this->orgs[] = $org;
            }
        }

        return $this;
    }
}
