<?php
/**
 * Bgy Library
 *
 * LICENSE
 *
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 *
 * @category    Bgy
 * @package     Bgy\Auth
 * @subpackage  Adapter
 * @author      Boris Guéry <guery.b@gmail.com>
 * @license     http://sam.zoy.org/wtfpl/COPYING
 * @link        http://borisguery.github.com/bgylibrary
 *
 */
namespace Bgy\Auth\Adapter;

use Doctrine\ORM\EntityManager;

class Doctrine2 implements \Zend_Auth_Adapter_Interface
{
    /**
     * The entity manager
     *
     * @var Doctrine\ORM\EntityManager
     */
    protected $_entityManager;

    /**
     * @var string
     */
    protected $_entityClassName;

    /**
     * @var string
     */
    protected $_identityField;

    /**
     * @var string
     */
    protected $_credentialField;

    /**
     * @var Closure
     */
    protected $_credentialTreatment;

    /**
     * @var string
     */
    protected $_identity;

    /**
     * @var string
     */
    protected $_credential;

    /**
     * @var array
     */
    protected $_authenticateResultInfo;

    /**
     * __construct() - Sets configuration options
     *
     * @param  \Doctrine\ORM\EntityManager  $entityManager The Entity Manager
     * @param  string                       $enitityClassName The Entity Class name
     * @param  string                       $identityField The identity property
     * @param  string                       $credentialField The credential property
     * @param  Closure                      $credentialTreatment Optional credential function streatment
     * @return void
     */
    public function __construct(EntityManager $entityManager = null, $entityClassName = null,
        $identityField = null, $credentialField = null,
        \Closure $credentialTreatment = null)
    {
        $this->setEntityManager($entityManager);

        if (null !== $entityClassName) {
            $this->setEntityClassName($entityClassName);
        }

        if (null !== $identityField) {
            $this->setIdentityField($identityField);
        }

        if (null !== $credentialField) {
            $this->setCredentialField($credentialField);
        }

        if (null !== $credentialTreatment) {
            $this->setCredentialTreatment($credentialTreatment);
        }
    }

    /**
     * authenticate() - defined by Zend_Auth_Adapter_Interface.  This method is called to
     * attempt an authentication.  Previous to this call, this adapter would have already
     * been configured with all necessary information to successfully connect to a database
     * table and attempt to find a record matching the provided identity.
     *
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        $this->_authenticateSetup();

        $query = $this->_authenticateCreateQuery();
        $resultIdentities = $query->setParameter(1, $this->getIdentity())
            ->execute();

        $authResult = $this->_authenticateValidateResultSet($resultIdentities);

        return $authResult;
    }

    /**
     * @return Doctrine\ORM\EntityManager $_entityManager
     */
    public function getEntityManager()
    {
        return $this->_entityManager;
    }

    /**
     * @param Doctrine\ORM\EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->_entityManager = $entityManager;

        return $this;
    }

    /**
     * @return the $_entityClassName
     */
    public function getEntityClassName()
    {
        return $this->_entityClassName;
    }

    /**
     * @param field_type $_entityClassName
     */
    public function setEntityClassName($entityClassName)
    {
        if (null === $entityClassName) {
            throw new \InvalidArgumentException('EntityClassName cannot be null');
        }
        $this->_entityClassName = $entityClassName;

        return $this;
    }

    /**
     * @return the $_identityField
     */
    public function getIdentityField()
    {
        return $this->_identityField;
    }

    /**
     * @param field_type $_identityField
     */
    public function setIdentityField($identityField)
    {
        if (null === $identityField) {
            throw new \InvalidArgumentException('IdentityField cannot be null');
        }
        $this->_identityField = $identityField;

        return $this;
    }

    /**
     * @return the $_credentialField
     */
    public function getCredentialField()
    {
        return $this->_credentialField;
    }

    /**
     * @param field_type $_credentialField
     */
    public function setCredentialField($credentialField)
    {
        if (null === $credentialField) {
            throw new \InvalidArgumentException('CredentialField cannot be null');
        }
        $this->_credentialField = $credentialField;

        return $this;
    }

    /**
     * @return the $_credentialTreatment
     */
    public function getCredentialTreatment()
    {
        return $this->_credentialTreatment;
    }

    /**
     * @param Closure $_credentialTreatment
     */
    public function setCredentialTreatment(\Closure $credentialTreatment)
    {
        $this->_credentialTreatment = $credentialTreatment;

        return $this;
    }

    /**
     * @return the $identity
     */
    public function getIdentity()
    {
        return $this->_identity;
    }

    /**
     * @param field_type $identity
     */
    public function setIdentity($identity)
    {
        $this->_identity = $identity;

        return $this;
    }

    /**
     * @return the $_credential
     */
    public function getCredential()
    {
        return $this->_credential;
    }

    /**
     * @param field_type $credential
     */
    public function setCredential($credential)
    {
        $this->_credential = $credential;

        return $this;
    }

    /**
     * _authenticateSetup() - This method abstracts the steps involved with
     * making sure that this adapter was indeed setup properly with all
     * required pieces of information.
     *
     * @throws Zend_Auth_Adapter_Exception - in the event that setup was not done properly
     * @return true
     */
    protected function _authenticateSetup()
    {
        $exception = null;

        if ($this->getEntityClassName() == '') {
            $exception = 'A table must be supplied for the Zend_Auth_Adapter_DbTable authentication adapter.';
        } elseif ($this->getIdentityField() == '') {
            $exception = 'An identity column must be supplied for the Zend_Auth_Adapter_DbTable authentication adapter.';
        } elseif ($this->getCredentialField() == '') {
            $exception = 'A credential column must be supplied for the Zend_Auth_Adapter_DbTable authentication adapter.';
        } elseif ($this->getIdentity() == '') {
            $exception = 'A value for the identity was not provided prior to authentication with Zend_Auth_Adapter_DbTable.';
        } elseif ($this->getCredential() === null) {
            $exception = 'A credential value was not provided prior to authentication with Zend_Auth_Adapter_DbTable.';
        }

        if (null !== $exception) {
            /**
             * @see Zend_Auth_Adapter_Exception
             */
            require_once 'Zend/Auth/Adapter/Exception.php';
            throw new \Zend_Auth_Adapter_Exception($exception);
        }

        $this->_authenticateResultInfo = array(
            'code'     => \Zend_Auth_Result::FAILURE,
            'identity' => $this->_identity,
            'messages' => array()
            );

        return true;
    }

    /**
     * Create the doctrine query
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function _authenticateCreateQuery()
    {
        $qb = $this->getEntityManager()->createQuery(
            'SELECT entity.' . $this->getCredentialField() . ', entity ' .
             ' FROM '. $this->getEntityClassName() .' entity ' .
             ' WHERE entity.' . $this->getIdentityField() . ' = ?1 '
        );

        return $qb;
    }

    /**
     * _authenticateCreateAuthResult() - Creates a Zend_Auth_Result object from
     * the information that has been collected during the authenticate() attempt.
     *
     * @return Zend_Auth_Result
     */
    protected function _authenticateCreateAuthResult()
    {
        return new \Zend_Auth_Result(
            $this->_authenticateResultInfo['code'],
            $this->_authenticateResultInfo['identity'],
            $this->_authenticateResultInfo['messages']
            );
    }

    /**
     * _authenticateValidateResultSet() - This method attempts to make
     * certain that only one record was returned in the resultset
     *
     * @param array $resultIdentities
     * @return true|Zend_Auth_Result
     */
    protected function _authenticateValidateResultSet(array $resultIdentities)
    {
        if (count($resultIdentities) < 1) {
            $this->_authenticateResultInfo['code'] = \Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
            $this->_authenticateResultInfo['messages'][] = 'A record with the supplied identity could not be found.';
        } elseif (count($resultIdentities) > 1) {
            $this->_authenticateResultInfo['code'] = \Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS;
            $this->_authenticateResultInfo['messages'][] = 'More than one record matches the supplied identity.';
        } else {
            $credential = $this->getCredential();
            if ($this->getCredentialTreatment() instanceof \Closure) {
                $closure = $this->getCredentialTreatment();
                $credential = $closure($credential);
            }

            if ($resultIdentities[0][$this->getCredentialField()] !== $credential) {
                $this->_authenticateResultInfo['code'] = \Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
                $this->_authenticateResultInfo['messages'][] = 'Supplied credential is invalid.';
            } else {
                $this->_authenticateResultInfo['code'] = \Zend_Auth_Result::SUCCESS;
                $this->_authenticateResultInfo['messages'][] = 'Authentication successful.';
            }
        }

        return $this->_authenticateCreateAuthResult();
    }
}

