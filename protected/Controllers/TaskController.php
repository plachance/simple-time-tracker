<?php

namespace SimpleTimeTracker\Controllers;

use DateInterval;
use DateTime;
use Doctrine\ORM\Query;
use Exception;
use Prado;
use SimpleTimeTracker\Controllers\Exceptions\EntityNotFoundException;
use SimpleTimeTracker\Entities\Project;
use SimpleTimeTracker\Entities\Task;
use TInvalidDataValueException;
use TPropertyValue;

/**
 * Class TaskController.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class TaskController extends Controller
{
	/**
	 * Get current task from specified user.
	 * @param int $userId
	 * @return null|Task Current task from specified user.
	 * @throws TInvalidDataValueException if user id is null.
	 */
	public function getCurrentTask($userId)
	{
		if($userId == null)
		{
			throw new TInvalidDataValueException(Prado::localize('UserId must not be null.'));
		}

		$em = $this->getEntityManager();
		$qb = $em->createQueryBuilder();
		/* @var $q Query */
		$q = $qb->select('t')
			->from(self::ENTITY_NS . 'Task', 't')
			->innerJoin('t.user', 'u')
			->where('u.id = :userId')
			->orderBy('t.id', 'desc')
			->setMaxResults(1)
			->getQuery();
		$q->setParameter('userId', $userId);
		return $q->getOneOrNullResult();
	}

	/**
	 * Get tasks list of the specified user.
	 * @param int $userId
	 * @param int $limit
	 * @param int $offset
	 * @param int $itemCount
	 * @param string $sortExpr
	 * @param string $projectText
	 * @param DateTime $dateTimeBeginFrom
	 * @param DateTime $dateTimeBeginTo
	 * @return mixed[int][string] Tasks list of the specified user.
	 * @throws TInvalidDataValueException if user id is null.
	 */
	public function getTasks($userId, $limit, $offset, &$itemCount, $sortExpr = null, $projectText = null, DateTime $dateTimeBeginFrom = null, DateTime $dateTimeBeginTo = null)
	{
		if($userId == null)
		{
			throw new TInvalidDataValueException(Prado::localize('UserId must not be null.'));
		}

		$em = $this->getEntityManager();
		$qb = $em->createQueryBuilder();
		$e = $qb->expr();
		$where = $e->andX('u.id = :userId');
		$params = array(
			'userId' => $userId,
		);
		if($projectText != null)
		{
			if(($pos = strpos($projectText, '-')) !== false)
			{
				list($projectNo, $projectDesc) = $this->parseProjectText($projectText);

				$where->add('p.no = :projectNo');
				$where->add('ILIKE(p.description, :projectDesc) = TRUE');
				$params['projectNo'] = $projectNo;
				$params['projectDesc'] = $projectDesc;
			}
			else if(preg_match('/^\d+$/', $projectText))
			{
				$where->add('p.no = :projectNo');
				$params['projectNo'] = $projectText;
			}
			else
			{
				$where->add('ILIKE(p.description, :projectDesc) = TRUE');
				$params['projectDesc'] = $projectText;
			}
		}
		if($dateTimeBeginFrom != null)
		{
			$where->add('t.dateTimeBegin >= :dateTimeBeginFrom');
			$params['dateTimeBeginFrom'] = $dateTimeBeginFrom->format(self::PGSQL_TIMESTAMP_FORMAT);
		}
		if($dateTimeBeginTo != null)
		{
			$where->add('t.dateTimeBegin <= :dateTimeBeginTo');
			$params['dateTimeBeginTo'] = $dateTimeBeginTo->format(self::PGSQL_TIMESTAMP_FORMAT);
		}

		/* @var $q Query */
		$q = $qb->select('t.id', 'p.no', 'p.description AS project_description', 't.description', 't.dateTimeBegin', 't.dateTimeEnd', $e->diff('t.dateTimeEnd', 't.dateTimeBegin') . ' AS duration'
			)
			->from(self::ENTITY_NS . 'Task', 't')
			->innerJoin('t.project', 'p')
			->innerJoin('t.user', 'u')
			->where($where)
			->orderBy($this->getOrderBy($sortExpr))
			->setParameters($params)
			->getQuery();

		$itemCount = $qb->select($e->count('t.id'))
			->resetDQLPart('orderBy')
			->getQuery()
			->getSingleScalarResult();

		$q->setFirstResult($offset)
			->setMaxResults($limit);
		return $q->getArrayResult();
	}

	/**
	 * Get the timesheet for the specified week.
	 * @param int $userId
	 * @param DateTime $dateBegin First timesheet date.
	 * @return mixed[int][string] Timesheet for the specified week.
	 * @throws TInvalidDataValueException if user id is null.
	 */
	public function getTimeSheet($userId, DateTime $dateBegin)
	{
		if($userId == null)
		{
			throw new TInvalidDataValueException(Prado::localize('UserId must not be null.'));
		}

		$dateBegin = clone $dateBegin;

		$cnx = $this->getDbConnection();
		$cnx->setActive(true);
		$cmd = $cnx->createCommand('WITH tasks AS (
			SELECT p.description AS project_desc,
				p.no AS project_no,
				SUM(CASE WHEN t.date_time_begin::date = :date1 THEN EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600 ELSE NULL::real END) AS day1,
				SUM(CASE WHEN t.date_time_begin::date = :date2 THEN EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600 ELSE NULL::real END) AS day2,
				SUM(CASE WHEN t.date_time_begin::date = :date3 THEN EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600 ELSE NULL::real END) AS day3,
				SUM(CASE WHEN t.date_time_begin::date = :date4 THEN EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600 ELSE NULL::real END) AS day4,
				SUM(CASE WHEN t.date_time_begin::date = :date5 THEN EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600 ELSE NULL::real END) AS day5,
				SUM(CASE WHEN t.date_time_begin::date = :date6 THEN EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600 ELSE NULL::real END) AS day6,
				SUM(CASE WHEN t.date_time_begin::date = :date7 THEN EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600 ELSE NULL::real END) AS day7
			FROM task t
			INNER JOIN project p USING (project_id)
			WHERE t.user_id = :userId
			AND t.date_time_begin BETWEEN :date1 AND :date8
			GROUP BY p.no, p.description
			ORDER BY p.no, p.description
			), r AS (
			SELECT t.*,
				COALESCE(t.day1, 0) +
				COALESCE(t.day2, 0) +
				COALESCE(t.day3, 0) +
				COALESCE(t.day4, 0) +
				COALESCE(t.day5, 0) +
				COALESCE(t.day6, 0) +
				COALESCE(t.day7, 0) AS total
			FROM tasks t
			)
			SELECT *
			FROM r
			UNION ALL
			SELECT NULL,
				NULL,
				SUM(COALESCE(r.day1, 0)),
				SUM(COALESCE(r.day2, 0)),
				SUM(COALESCE(r.day3, 0)),
				SUM(COALESCE(r.day4, 0)),
				SUM(COALESCE(r.day5, 0)),
				SUM(COALESCE(r.day6, 0)),
				SUM(COALESCE(r.day7, 0)),
				SUM(COALESCE(r.total, 0))
			FROM r
			HAVING SUM(r.day1) IS NOT NULL OR
				SUM(r.day2) IS NOT NULL OR
				SUM(r.day3) IS NOT NULL OR
				SUM(r.day4) IS NOT NULL OR
				SUM(r.day5) IS NOT NULL OR
				SUM(r.day6) IS NOT NULL OR
				SUM(r.day7) IS NOT NULL OR
				SUM(r.total) IS NOT NULL');
		$cmd->bindValue(':userId', $userId);
		$cmd->bindValue(':date1', $dateBegin->format(self::PGSQL_DATE_FORMAT));
		$interval = new DateInterval('P1D');
		for($i = 2; $i < 9; $i++)
		{
			$dateBegin->add($interval);
			$cmd->bindValue(':date' . $i, $dateBegin->format(self::PGSQL_DATE_FORMAT));
		}
		return $cmd->query()->readAll();
	}

	/**
	 * Get time periods for the specified week.
	 * @param int $userId
	 * @param DateTime $dateBegin First timesheet date
	 * @return mixed[int][string] Time periods for the specified week.
	 * @throws TInvalidDataValueException if user id is null.
	 */
	public function getTimePeriods($userId, DateTime $dateBegin)
	{
		if($userId == null)
		{
			throw new TInvalidDataValueException(Prado::localize('UserId must not be null.'));
		}

		$dateBegin = clone $dateBegin;

		$cnx = $this->getDbConnection();
		$cnx->setActive(true);
		$cmd = $cnx->createCommand('WITH r AS (
				SELECT unnest(CASE WHEN (lag(date_time_end) OVER w IS NULL OR date_time_begin - lag(date_time_end) OVER w > \'15 minutes\'::interval)
						AND (lead(date_time_begin) OVER w IS NULL OR lead(date_time_begin) OVER w - date_time_end > \'15 minutes\'::interval)
						THEN ARRAY[date_time_begin, date_time_end]
					WHEN lag(date_time_end) OVER w IS NULL OR date_time_begin - lag(date_time_end) OVER w > \'15 minutes\'::interval THEN ARRAY[date_time_begin]
					WHEN lead(date_time_begin) OVER w IS NULL OR lead(date_time_begin) OVER w - date_time_end > \'15 minutes\'::interval THEN ARRAY[date_time_end]
					END) AS date_time
				FROM task
				WHERE user_id = :userId
				AND date_time_begin BETWEEN :date1 AND :date8
				WINDOW w AS (PARTITION BY date_time_begin::date ORDER BY date_time_begin)
				ORDER BY date_time_begin
			)
			SELECT date_time::date AS date, row_number() OVER (PARTITION BY date_time::date), date_time::time AS heure
			FROM r');
		$ligne = array();
		$cmd->bindValue(':userId', $userId);
		$cmd->bindValue(':date1', $dateBegin->format(self::PGSQL_DATE_FORMAT));
		$ligne[$dateBegin->format(self::PGSQL_DATE_FORMAT)] = null;
		$interval = new DateInterval('P1D');
		for($i = 2; $i < 9; $i++)
		{
			$dateBegin->add($interval);
			$ligne[$dateBegin->format(self::PGSQL_DATE_FORMAT)] = null;
		}
		unset($ligne[$dateBegin->format(self::PGSQL_DATE_FORMAT)]);
		$cmd->bindValue(':date8', $dateBegin->format(self::PGSQL_DATE_FORMAT));
		$result = $cmd->query()->readAll();
		$data = array();
		foreach($result as $row)
		{
			if(!isset($data[$row['row_number']]))
			{
				$data[$row['row_number']] = $ligne;
			}
			$data[$row['row_number']][$row['date']] = $row['heure'];
		}

		foreach($data as $key => $row)
		{
			$data[$key] = array_values($row);
		}

		return $data;
	}

	/**
	 * @param int $id
	 * @return Task|null
	 */
	public function getTask($id)
	{
		if($id == null)
		{
			return null;
		}

		return $this->getEntityManager()->find(self::ENTITY_NS . 'Task', $id);
	}

	/**
	 * Stop the specified task.
	 * @param int $id
	 * @return Task|false Stopped task or false if task is already stopped.
	 * @throws TInvalidDataValueException if id is null or does not exists.
	 */
	public function stopTask($id)
	{
		if($id == null)
		{
			throw new TInvalidDataValueException(Prado::localize('Id must not be null.'));
		}

		$em = $this->getEntityManager();
		$em->beginTransaction();
		try
		{
			$task = $em->find(self::ENTITY_NS . 'Task', $id);
			/* @var $task Task */
			if($task == null)
			{
				throw new TInvalidDataValueException(Prado::localize('Task Id "{id}" does not exists.', array('id' => $id)));
			}

			if($task->getDateTimeEnd() != null)
			{
				$em->rollback();
				return false;
			}

			$task->setDateTimeEnd(new DateTime());

			$em->flush();
			$em->commit();

			$em->detach($task);
			return $task;
		}
		catch(Exception $ex)
		{
			$em->rollback();
			throw $ex;
		}

		return false;
	}

	/**
	 * Restart the specified task.
	 * @param int $id
	 * @return Task|false Started task or false if task is not stopped.
	 * @throws TInvalidDataValueException if id is null or does not exists.
	 */
	public function restartTask($id)
	{
		if($id == null)
		{
			throw new TInvalidDataValueException(Prado::localize('Id must not be null.'));
		}

		$em = $this->getEntityManager();
		$em->beginTransaction();
		try
		{
			$task = $em->find(self::ENTITY_NS . 'Task', $id);
			/* @var $task Task */
			if($task == null)
			{
				throw new TInvalidDataValueException(Prado::localize('Task Id "{id}" does not exists.', array('id' => $id)));
			}

			if($task->getDateTimeEnd() == null)
			{
				$em->rollback();
				return false;
			}

			$newTask = new Task();
			$newTask->setProject($task->getProject());
			$newTask->setDescription($task->getDescription());
			$newTask->setDateTimeBegin(new DateTime());
			$newTask->setUser($task->getUser());
			$em->persist($newTask);

			$em->flush();
			$em->commit();

			$em->detach($newTask);
			return $newTask;
		}
		catch(Exception $ex)
		{
			$em->rollback();
			throw $ex;
		}

		return false;
	}

	/**
	 * Create a new task.
	 * @param string $projectText
	 * @param string $description
	 * @param int $userId
	 * @return Task Created task.
	 * @throws EntityNotFoundException if userId does not exists.
	 */
	public function createTask($projectText, $description, $userId)
	{
		$em = $this->getEntityManager();
		$em->beginTransaction();
		try
		{
			$user = $em->find(self::ENTITY_NS . 'User', $userId);
			if($user === null)
			{
				throw new EntityNotFoundException();
			}

			//Stop current task if needed.
			$currentTask = $this->getCurrentTask($userId);
			if($currentTask != null && $currentTask->getDateTimeEnd() == null)
			{
				$currentTask->setDateTimeEnd(new DateTime());
			}

			list($projectNo, $projectDesc) = $this->parseProjectText($projectText);

			$repProject = $em->getRepository(self::ENTITY_NS . 'Project');
			$project = $repProject->findOneBy(array(
				'no' => $projectNo,
				'description' => $projectDesc,
			));
			/* @var $project Project */
			if($project === null)
			{
				$project = new Project();
				$project->setNo($projectNo);
				$project->setDescription($projectDesc);
				$project->setUser($user);
				$em->persist($project);
			}

			$task = new Task();
			$task->setProject($project);
			$task->setDescription(TPropertyValue::ensureNullIfEmpty($description));
			$task->setDateTimeBegin(new DateTime());
			$task->setUser($user);
			$em->persist($task);

			$em->flush();
			$em->commit();
			$em->detach($task);
			return $task;
		}
		catch(Exception $ex)
		{
			$em->rollback();
			throw $ex;
		}
	}

	/**
	 * Get if the specified user own the task.
	 * @param int $taskId
	 * @param int $userId
	 * @return bool True if the specified user own the task, false otherwise.
	 * @throws TInvalidDataValueException if taskId is null or does not exists.
	 */
	public function getIsUserTask($taskId, $userId)
	{
		$task = $this->getTask($taskId);
		if($task == null)
		{
			throw new TInvalidDataValueException(Prado::localize('Task Id "{id}" does not exists.', array('id' => $taskId)));
		}

		return $task->getUser()->getId() == $userId;
	}

	/**
	 * Delete the specified task.
	 * @param int $id
	 * @throws TInvalidDataValueException if id is null or does not exists.
	 */
	public function deleteTask($id)
	{
		if($id == null)
		{
			throw new TInvalidDataValueException(Prado::localize('Id must not be null.'));
		}

		$em = $this->getEntityManager();
		$em->beginTransaction();
		try
		{
			$task = $em->find(self::ENTITY_NS . 'Task', $id);
			/* @var $task Task */
			if($task == null)
			{
				throw new TInvalidDataValueException(Prado::localize('Task Id "{id}" does not exists.', array('id' => $id)));
			}
			$em->remove($task);

			$em->flush();
			$em->commit();
		}
		catch(Exception $ex)
		{
			$em->rollback();
			throw $ex;
		}
	}

	/**
	 * Modify specified task.
	 * @param int $id
	 * @param string $projectText
	 * @param string $description
	 * @param DateTime $dateTimeBegin
	 * @param DateTime $dateTimeEnd
	 * @throws TInvalidDataValueException if id is null or does not exists.
	 */
	public function modifierTask($id, $projectText, $description, DateTime $dateTimeBegin, DateTime $dateTimeEnd = null)
	{
		if($id == null)
		{
			throw new TInvalidDataValueException(Prado::localize('Id must not be null.'));
		}

		$em = $this->getEntityManager();
		$em->beginTransaction();
		try
		{
			$task = $em->find(self::ENTITY_NS . 'Task', $id);
			/* @var $task Task */
			if($task == null)
			{
				throw new TInvalidDataValueException(Prado::localize('Task Id "{id}" does not exists.', array('id' => $id)));
			}

			if($task->getProject()->__toString() != $projectText)
			{
				list($projectNo, $projectDesc) = $this->parseProjectText($projectText);

				$repProject = $em->getRepository(self::ENTITY_NS . 'Project');
				$project = $repProject->findOneBy(array(
					'no' => $projectNo,
					'description' => $projectDesc,
				));
				/* @var $project Project */
				if($project === null)
				{
					$project = new Project();
					$project->setNo($projectNo);
					$project->setDescription($projectDesc);
					$project->setUser($task->getUser());
					$em->persist($project);
				}

				$task->setProject($project);
			}
			$task->setDescription(TPropertyValue::ensureNullIfEmpty($description));
			$task->setDateTimeBegin($dateTimeBegin);
			$task->setDateTimeEnd($dateTimeEnd);

			$em->flush();
			$em->commit();
		}
		catch(Exception $ex)
		{
			$em->rollback();
			throw $ex;
		}
	}

	/**
	 * Parse the specified project text.
	 * @param string $projectText Formatted project text (00000-Description).
	 * @return type
	 */
	protected function parseProjectText($projectText)
	{
		if(($pos = strpos($projectText, '-')) !== false)
		{
			$projectNo = substr($projectText, 0, $pos);
			$projectDesc = substr($projectText, $pos + 1);
		}
		else
		{
			$projectNo = $projectText;
			$projectDesc = null;
		}

		return array($projectNo, $projectDesc);
	}

	/**
	 * Get the summary of all work for the specified user.
	 * @param int $userId
	 * @param int $limit
	 * @param int $offset
	 * @param int $itemCount
	 * @param string $sortExpr
	 * @param int[] $years
	 * @return mixed[int][string] Summary of all work for the specified user.
	 * @throws TInvalidDataValueException if user id is null.
	 */
	public function getSummary($userId, $limit, $offset, &$itemCount, $sortExpr = null, $years = array())
	{
		if($userId == null)
		{
			throw new TInvalidDataValueException(Prado::localize('UserId must not be null.'));
		}

		$params = array(
			':userId' => $userId
		);

		$sql = ' FROM task t
			INNER JOIN project p USING (project_id)
			WHERE t.user_id = :userId
			GROUP BY p.no, p.description ';

		$cnx = $this->getDbConnection();
		$cnx->setActive(true);

		$cmdCount = $cnx->createCommand('SELECT COUNT(*) FROM (SELECT 1 '
			. $sql
			. ') AS d');
		foreach($params as $name => $value)
		{
			$cmdCount->bindValue($name, $value);
		}

		$itemCount = $cmdCount->queryScalar();

		$select = array(
			'p.description AS project_desc',
			'p.no AS project_no',
		);
		foreach($years as $i => $year)
		{
			$year = (int)$year;
			$yearI = ":year{$i}";
			$params[$yearI] = $year;
			$select[] = "SUM(CASE WHEN date_part('year', date_time_begin) = $yearI THEN "
				. "EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600 "
				. "ELSE 0 END) AS year_{$year}";
		}
		$select[] = "SUM(EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600) AS total";

		$orderBy = $this->getOrderBy($sortExpr);

		$cmd = $cnx->createCommand('SELECT '
			. implode(', ', $select)
			. $sql
			. ' ORDER BY '
			. implode(', ', $orderBy === null ? null : $orderBy->getParts())
			. ' LIMIT :limit OFFSET :offset');
		foreach($params as $name => $value)
		{
			$cmd->bindValue($name, $value);
		}
		$cmd->bindValue(':limit', (int)$limit);
		$cmd->bindValue(':offset', (int)$offset);
		return $cmd->query()->readAll();
	}

	/**
	 * @param int $userId
	 * @return int[] Work years for the specified user.
	 */
	public function getYears($userId)
	{
		$cnx = $this->getDbConnection();
		$cnx->setActive(true);
		$cmd = $cnx->createCommand('SELECT generate_series(date_part(\'year\', MIN(date_time_begin))::int, date_part(\'year\', MAX(date_time_begin))::int) as year
			FROM task t
			WHERE t.user_id = :userId');
		$cmd->bindValue(':userId', $userId);
		return $cmd->queryColumn();
	}

}
