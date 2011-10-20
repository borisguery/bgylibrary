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
 * @package     Bgy\Doctrine
 * @author      Boris Guéry <guery.b@gmail.com>
 * @license     http://sam.zoy.org/wtfpl/COPYING
 * @link        http://borisguery.github.com/bgylibrary
 * @see         https://gist.github.com/1034079#file_serializable_entity.php
 */

namespace Bgy\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata,
    Doctrine\Common\Util\Inflector,
    Doctrine\ORM\EntityManager,
    Exception;

class EntitySerializer
{

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $_em;

    public function __construct($em)
    {
        $this->setEntityManager($em);
    }

    /**
     *
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->_em;
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->_em = $em;

        return $this;
    }

    protected function _serializeEntity($entity)
    {
        $className = get_class($entity);
        $metadata = $this->_em->getClassMetadata($className);

        $data = array();

        foreach ($metadata->fieldMappings as $field => $mapping) {
            $value = $metadata->reflFields[$field]->getValue($entity);
            $field = Inflector::tableize($field);
            if ($value instanceof \DateTime) {
                // We cast DateTime to array to keep consistency with array result
                $data[$field] = (array)$value;
            } elseif (is_object($value)) {
                $data[$field] = (string)$value;
            } else {
                $data[$field] = $value;
            }
        }

        foreach ($metadata->associationMappings as $field => $mapping) {
            $key = Inflector::tableize($field);
            if ($mapping['isCascadeDetach']) {
                $data[$key] = $metadata->reflFields[$field]->getValue($entity);
                if (null !== $data[$key]) {
                    $data[$key] = $this->_serializeEntity($data[$key]);
                }
            } elseif ($mapping['isOwningSide'] && $mapping['type'] & ClassMetadata::TO_ONE) {
                if (null !== $metadata->reflFields[$field]->getValue($entity)) {
                    $data[$key] = $this->getEntityManager()
                        ->getUnitOfWork()
                        ->getEntityIdentifier(
                            $metadata->reflFields[$field]
                                ->getValue($entity)
                            );
                } else {
                    // In some case the relationship may not exist, but we want
                    // to know about it
                    $data[$key] = null;
                }
            }
        }

        return $data;
    }

    /**
     * Serialize an entity to an array
     *
     * @param The entity $entity
     * @return array
     */
    public function toArray($entity)
    {
        return $this->_serializeEntity($entity);
    }


    /**
     * Convert an entity to a JSON object
     *
     * @param The entity $entity
     * @return string
     */
    public function toJson($entity)
    {
        return json_encode($this->toArray($entity));
    }

    /**
     * Convert an entity to XML representation
     *
     * @param The entity $entity
     * @throws Exception
     */
    public function toXml($entity)
    {
        throw new Exception('Not yet implemented');
    }
}

