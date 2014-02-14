<?php

namespace TimeBox\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ProjectRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FileRepository extends EntityRepository
{
    public function getRootFiles($user)
    {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT f.name, f.type, MAX(v.date) as date, v.size
                FROM TimeBoxMainBundle:File f
                JOIN f.version v
                WHERE f.user = :user
                GROUP BY f.id
              ')->setParameter('user', $user);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}