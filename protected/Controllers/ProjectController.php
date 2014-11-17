<?php

namespace SimpleTimeTracker\Controllers;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\ResultSetMapping;
use TInvalidDataValueException;

/**
 * Class ProjectController.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class ProjectController extends Controller
{
	/**
	 * Get the list of last projects from specified user.
	 * @param int $userId
	 * @param int $limit
	 * @return mixed[int][string] List of last projects from specified user.
	 * @throws TInvalidDataValueException if userId is null.
	 */
	public function getLastProjects($userId, $limit = 20)
	{
		if($userId == null)
		{
			throw new TInvalidDataValueException('UserId must not be null.');
		}

		$em = $this->getEntityManager();
		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('project_id', 'project_id', Type::INTEGER);
		$rsm->addScalarResult('no', 'no', Type::INTEGER);
		$rsm->addScalarResult('description', 'description', Type::STRING);

		return $em->createNativeQuery('SELECT p.project_id, p.no, p.description
			FROM project p
			INNER JOIN (SELECT project_id, MAX(date_time_begin) AS date_time_begin
				FROM task
				WHERE user_id = :userId
				GROUP BY project_id
				ORDER BY date_time_begin DESC
				LIMIT :limit) t USING (project_id)
			ORDER BY p.no DESC, p.description DESC', $rsm)
				->setParameter('userId', $userId)
				->setParameter('limit', $limit)
				->getResult();
	}

}
