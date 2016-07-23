<?php

namespace AppBundle\Entity\Manager;

use AppBundle\Classes\LikeQueryHelpers;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

abstract class BaseEntityManager
{
    use LikeQueryHelpers;

    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param string          $class
     * @param ManagerRegistry $registry
     */
    public function __construct($class, ManagerRegistry $registry)
    {
        $this->registry = $registry;
        $this->class    = $class;
    }

    /**
     * Get entity manager.
     *
     * @throws \RuntimeException
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        $manager = $this->registry->getManagerForClass($this->class);

        if (!$manager) {
            throw new \RuntimeException(sprintf('Unable to find the mapping information for the class %s.'
                . " Please check the 'auto_mapping' option (http://symfony.com/doc/current/reference/configuration/doctrine.html#configuration-overview)"
                . " or add the bundle to the 'mappings' section in the doctrine configuration.", $this->class));
        }

        return $manager;
    }

    /**
     * Return the Entity class name.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Find all entities in the repository.
     *
     * @return array
     */
    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Find entities by a set of criteria.
     *
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @throws \RuntimeException
     *
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Find a single entity by a set of criteria.
     *
     * @param array      $criteria
     * @param array|null $orderBy
     *
     * @throws \RuntimeException
     *
     * @return object|null
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier
     *
     * @throws \RuntimeException
     *
     * @return object
     */
    public function find($id)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * Create an empty Entity instance.
     *
     * @return object
     */
    public function create()
    {
        return new $this->class();
    }

    /**
     * Save an Entity.
     *
     * @param object $entity   The Entity to save
     * @param bool   $andFlush Flush the EntityManager after saving the object?
     *
     * @throws \RuntimeException
     */
    public function save($entity, $andFlush = true)
    {
        $this->checkObject($entity);

        $this->getEntityManager()->persist($entity);

        if ($andFlush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Delete an Entity.
     *
     * @param object $entity   The Entity to delete
     * @param bool   $andFlush Flush the EntityManager after deleting the object?
     */
    public function delete($entity, $andFlush = true)
    {
        $this->checkObject($entity);

        $this->getEntityManager()->remove($entity);

        if ($andFlush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Get the related table name.
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->getEntityManager()->getClassMetadata($this->class)->table['name'];
    }

    /**
     * Returns the related Object Repository.
     *
     * @param EntityManager|null $em
     *
     * @return EntityRepository
     */
    protected function getRepository($em = null)
    {
        if ($em === null) {
            $em = $this->getEntityManager();
        }

        return $em->getRepository($this->class);
    }

    /**
     * @param $object
     *
     * @throws \InvalidArgumentException
     */
    protected function checkObject($object)
    {
        if (!$object instanceof $this->class) {
            throw new \InvalidArgumentException(sprintf(
                'Object must be instance of %s, %s given',
                $this->class, is_object($object) ? get_class($object) : gettype($object)
            ));
        }
    }
}
