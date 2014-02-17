<?php

namespace TimeBox\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ProjectRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VersionRepository extends EntityRepository
{
	public function getFileVersions($user, $fileId)
    {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT v
                FROM TimeBoxMainBundle:Version v
                JOIN v.file f
                WHERE f.user = :user
                AND v.file = :fileId
                ORDER BY v.date DESC
            ')->setParameters(array(
               'user'   => $user,
               'fileId' => $fileId
            ));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}