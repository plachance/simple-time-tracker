<?php

declare(strict_types = 1);

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Class ProjectRepository.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class ProjectRepository extends EntityRepository
{
	/**
	 * Get the time per user for the specified project.
	 *
	 * @param int $projectNo
	 *
	 * @return mixed[string][]
	 */
	public function getProjectTimePerUser(int $projectNo)
	{
		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('username', 'username');
		$rsm->addScalarResult('time', 'time', 'float');

		return $this->getEntityManager()
			->createNativeQuery('SELECT u.username, t.time
				FROM (
				SELECT t.user_id,
					SUM(EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600) AS time
				FROM task t
				INNER JOIN project p USING (project_id)
				WHERE p.no = :no
				GROUP BY ROLLUP (t.user_id)) AS t
				LEFT JOIN "user" u USING(user_id)
				ORDER BY u.username', $rsm)
			->setParameter('no', $projectNo)
			->execute();
	}

	/**
	 * Get the time per description for the specified project.
	 *
	 * @param int $projectNo
	 *
	 * @return mixed[string][]
	 */
	public function getProjectTimePerDescription(int $projectNo)
	{
		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('description', 'description');
		$rsm->addScalarResult('time', 'time', 'float');

		return $this->getEntityManager()
			->createNativeQuery('SELECT p.description,
					SUM(EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600) AS time
				FROM task t
				INNER JOIN project p USING (project_id)
				WHERE p.no = :no
				GROUP BY ROLLUP (p.description)
				ORDER BY p.description', $rsm)
			->setParameter('no', $projectNo)
			->execute();
	}

}
