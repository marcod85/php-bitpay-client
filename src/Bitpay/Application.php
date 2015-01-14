<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

/**
 * This class represents an application for a new merchant account.
 * see https://bitpay.com/api#resource-Applications
 *
 * @package Bitpay
 */
class Application implements ApplicationInterface
{
    /**
     * @var array
     */
    protected $users;

    /**
     * @var array
     */
    protected $orgs;

    /**
     */
    public function __construct()
    {
        $this->users = array();
        $this->orgs  = array();
    }

    /**
     * @inheritdoc
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @inheritdoc
     */
    public function getOrgs()
    {
        return $this->orgs;
    }

    /**
     * Add user to stack
     *
     * @param UserInterface $user
     *
     * @return ApplicationInterface
     */
    public function addUser(UserInterface $user)
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
     * Add org to stack
     *
     * @param OrgInterface $org
     *
     * @return ApplicationInterface
     */
    public function addOrg(OrgInterface $org)
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
