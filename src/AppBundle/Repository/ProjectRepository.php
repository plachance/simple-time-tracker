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
	 * @return string Database server version.
	 */
	protected function getServerVersion()
	{
		return $this->getEntityManager()->getConnection()->getWrappedConnection()->getServerVersion();
	}

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

		$version = $this->getServerVersion();
		if(version_compare($version, '9.5', '>='))
		{
			$sql = 'WITH time AS (
					SELECT t.user_id,
						SUM(EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600) AS time
					FROM task t
					INNER JOIN project p USING (project_id)
					WHERE p.no = :no
					GROUP BY ROLLUP (t.user_id)
				)
				SELECT u.username, t.time
				FROM time t
				LEFT JOIN "user" u USING(user_id)
				ORDER BY u.username;';
		}
		else
		{
			$sql = 'WITH time AS (
					SELECT t.user_id,
						SUM(EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600) AS time
					FROM task t
					INNER JOIN project p USING (project_id)
					WHERE p.no = :no
					GROUP BY t.user_id
				)
				(SELECT u.username, t.time
				FROM time t
				LEFT JOIN "user" u USING(user_id)
				ORDER BY u.username)
				UNION ALL
				(SELECT null, SUM(t.time)
				FROM time t);';
		}

		return $this->getEntityManager()
				->createNativeQuery($sql, $rsm)
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

		$version = $this->getServerVersion();
		if(version_compare($version, '9.5', '>='))
		{
			$sql = 'SELECT p.description,
					SUM(EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600) AS time
				FROM task t
				INNER JOIN project p USING (project_id)
				WHERE p.no = :no
				GROUP BY ROLLUP (p.description)
				ORDER BY p.description';
		}
		else
		{
			$sql = 'WITH time AS (
					SELECT p.description,
						SUM(EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600) AS time
					FROM task t
					INNER JOIN project p USING (project_id)
					WHERE p.no = :no
					GROUP BY p.description
					ORDER BY p.description
				)
				SELECT * FROM time
				UNION ALL
				SELECT null, SUM(t.time)
				FROM time t';
		}

		return $this->getEntityManager()
				->createNativeQuery($sql, $rsm)
				->setParameter('no', $projectNo)
				->execute();
	}

}
