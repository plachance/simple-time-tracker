<?php

declare(strict_types = 1);

namespace AppBundle\Repository;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use AppBundle\Util\DateTime as AppDateTime;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use InvalidArgumentException;

/**
 * Class TaskRepository.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class TaskRepository extends EntityRepository
{
	/**
	 * Get the tasks list for the specified user.
	 *
	 * @param User $user
	 * @return mixed[string][]
	 */
	public function getTasksList(User $user)
	{
		$qb = $this->createQueryBuilder('t');

		return $qb->select('t.id', 'p.no', 'p.description AS project_description',
					't.dateTimeBegin', 't.dateTimeEnd',
					$qb->expr()->diff('t.dateTimeEnd', 't.dateTimeBegin') . ' AS duration')
				->innerJoin('t.project', 'p')
				->where('t.user = :user')
				->orderBy('t.dateTimeEnd', 'DESC')
				->setParameter('user', $user)
				->getQuery()
				->execute();
	}

	/**
	 * Get current task from specified user.
	 *
	 * @param User $user
	 * @return null|Task Current task from specified user.
	 */
	public function getCurrentTask(User $user)
	{
		return $this->createQueryBuilder('t')
				->where('t.user = :user')
				->orderBy('t.id', 'desc')
				->setMaxResults(1)
				->setParameter('user', $user)
				->getQuery()
				->getOneOrNullResult();
	}

	/**
	 * Get the time spent in all project for the specified user.
	 *
	 * @param User $user
	 * @param int[] $years
	 * @return mixed[int][string] Summary of all work for the specified user.
	 */
	public function getSummary(User $user, array $years = null)
	{
		if($years === null)
		{
			$years = $this->getYears($user);
		}

		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('project_desc', 'project_desc');
		$rsm->addScalarResult('project_no', 'project_no', 'integer');

		$params = [
			':userId' => $user->getId(),
		];

		$select = [
			'p.description AS project_desc',
			'p.no AS project_no',
		];
		foreach($years as $i => $year)
		{
			$year = (int) $year;
			$yearI = ":year{$i}";
			$params[$yearI] = $year;
			$select[] = "SUM(CASE WHEN date_part('year', date_time_begin) = $yearI THEN "
				. 'EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600 '
				. "ELSE 0 END) AS year_{$year}";
			$rsm->addScalarResult("year_{$year}", "year_{$year}", 'float');
		}
		$select[] = 'SUM(EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600) AS total';
		$rsm->addScalarResult('total', 'total', 'float');

		$sql = 'SELECT '
			. implode(', ', $select)
			. ' FROM task t
				INNER JOIN project p USING (project_id)
				WHERE t.user_id = :userId
				GROUP BY p.no, p.description
				ORDER BY p.no, p.description';

		return $this->getEntityManager()->createNativeQuery($sql, $rsm)
				->setParameters($params)
				->execute();
	}

	/**
	 * @param User $user
	 * @return int[] Work years for the specified user.
	 */
	public function getYears(User $user)
	{
		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('year', 'year', 'integer');

		return array_column($this->getEntityManager()
				->createNativeQuery('SELECT generate_series(date_part(\'year\', MIN(date_time_begin))::int, date_part(\'year\', MAX(date_time_begin))::int) as year
					FROM task t
					WHERE t.user_id = :userId', $rsm)
				->setParameter('userId', $user->getId())
				->execute(), 'year');
	}

	/**
	 * Get a table containing time spent for each tasks for the specified user and week.
	 *
	 * @param User $user
	 * @param DateTime $dateBegin First timesheet date.
	 * @return mixed[int][string] Timesheet for the specified week.
	 */
	public function getTimeSheet(User $user, DateTime $dateBegin)
	{
		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('project_desc', 'project_desc');
		$rsm->addScalarResult('project_no', 'project_no', 'integer');
		$rsm->addScalarResult('day_1',
			'day_' . $dateBegin->format(AppDateTime::PGSQL_DATE), 'float');

		$q = $this->getEntityManager()->createNativeQuery('WITH tasks AS (
			SELECT p.description AS project_desc,
				p.no AS project_no,
				SUM(CASE WHEN t.date_time_begin::date = :date1 THEN EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600 ELSE NULL::real END) AS day_1,
				SUM(CASE WHEN t.date_time_begin::date = :date2 THEN EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600 ELSE NULL::real END) AS day_2,
				SUM(CASE WHEN t.date_time_begin::date = :date3 THEN EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600 ELSE NULL::real END) AS day_3,
				SUM(CASE WHEN t.date_time_begin::date = :date4 THEN EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600 ELSE NULL::real END) AS day_4,
				SUM(CASE WHEN t.date_time_begin::date = :date5 THEN EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600 ELSE NULL::real END) AS day_5,
				SUM(CASE WHEN t.date_time_begin::date = :date6 THEN EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600 ELSE NULL::real END) AS day_6,
				SUM(CASE WHEN t.date_time_begin::date = :date7 THEN EXTRACT(epoch FROM t.date_time_end - t.date_time_begin)/3600 ELSE NULL::real END) AS day_7
			FROM task t
			INNER JOIN project p USING (project_id)
			WHERE t.user_id = :userId
			AND t.date_time_begin BETWEEN :date1 AND :date8
			GROUP BY p.no, p.description
			ORDER BY p.no, p.description
			), r AS (
			SELECT t.*,
				COALESCE(t.day_1, 0) +
				COALESCE(t.day_2, 0) +
				COALESCE(t.day_3, 0) +
				COALESCE(t.day_4, 0) +
				COALESCE(t.day_5, 0) +
				COALESCE(t.day_6, 0) +
				COALESCE(t.day_7, 0) AS total
			FROM tasks t
			)
			SELECT *
			FROM r
			UNION ALL
			SELECT NULL,
				NULL,
				COALESCE(SUM(r.day_1), 0),
				COALESCE(SUM(r.day_2), 0),
				COALESCE(SUM(r.day_3), 0),
				COALESCE(SUM(r.day_4), 0),
				COALESCE(SUM(r.day_5), 0),
				COALESCE(SUM(r.day_6), 0),
				COALESCE(SUM(r.day_7), 0),
				COALESCE(SUM(r.total), 0)
			FROM r', $rsm)
			->setParameter('userId', $user->getId())
			->setParameter('date1', $dateBegin->format(AppDateTime::PGSQL_DATE));
		$interval = new DateInterval('P1D');
		for($i = 2; $i < 9; ++$i)
		{
			$dateBegin->add($interval);
			$q->setParameter(':date' . $i, $dateBegin->format(AppDateTime::PGSQL_DATE));
			$rsm->addScalarResult('day_' . $i,
				'day_' . $dateBegin->format(AppDateTime::PGSQL_DATE), 'float');
		}
		$rsm->addScalarResult('total', 'total', 'float');

		return $q->execute();
	}

	/**
	 * Get the arrival and departure times for the specified user.
	 * 
	 * Gaps between tasks of less than 15 minutes are ignored.
	 *
	 * @param User $user
	 * @param DateTime $dateBegin First timesheet date
	 * @param int $days Number of days to include in the timesheet.
	 * @throws InvalidArgumentException if $days is less than 1.
	 * @return mixed[int][string] Time periods for the specified week.
	 */
	public function getTimePeriods(User $user, DateTime $dateBegin, int $days = 7)
	{
		if($days < 1)
		{
			throw new InvalidArgumentException('Days must be greater than zero.');
		}

		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('date', 'date');
		$rsm->addScalarResult('row_number', 'row_number', 'integer');
		$rsm->addScalarResult('heure', 'heure');

		$q = $this->getEntityManager()->createNativeQuery('WITH r AS (
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
			FROM r', $rsm)
			->setParameter('userId', $user->getId())
			->setParameter('date1', $dateBegin->format(AppDateTime::PGSQL_DATE));

		$ligne = [];
		$ligne[$dateBegin->format(AppDateTime::PGSQL_DATE)] = null;
		$interval = new DateInterval('P1D');
		for($i = 2; $i < $days + 2; ++$i)
		{
			$dateBegin->add($interval);
			$ligne[$dateBegin->format(AppDateTime::PGSQL_DATE)] = null;
		}
		unset($ligne[$dateBegin->format(AppDateTime::PGSQL_DATE)]);

		$q->setParameter('date8', $dateBegin->format(AppDateTime::PGSQL_DATE));
		$result = $q->execute();

		$data = [];
		foreach($result as $row)
		{
			$i = $row['row_number'] - 1;
			if(!isset($data[$i]))
			{
				$data[$i] = $ligne;
			}
			$data[$i][$row['date']] = $row['heure'];
		}
		foreach($data as $key => $row)
		{
			$data[$key] = array_values($row);
		}

		return $data;
	}

	/**
	 * Calculate the departure time of the user based on the time periods of the specified day.
	 * 
	 * If the day length is over, it returns the last arrival or departure time 
	 * of the day. Otherwise, it returns the remaining time added to : the last 
	 * arrival time if the task ongoing or now if not.
	 *
	 * @param User $user
	 * @param DateTime $date
	 * @param float $dayLength Work day length, in hours. Default's to the user's preference if not set.
	 * @throws InvalidArgumentException if $dayLength is less or equal to zero.
	 * @return DateTime
	 */
	public function getDepartureTime(User $user, DateTime $date = null,
		float $dayLength = null)
	{
		$dayLength = $dayLength ?? $user->getDayLength();
		if($dayLength <= 0)
		{
			throw new InvalidArgumentException('DayLength must be greater than zero.');
		}
		$date = $date === null ? new DateTime() : clone $date;

		$secondsLeft = (int) ($dayLength * 3600);

		$periods = $this->getTimePeriods($user, $date, 1);

		$prevDate = null;
		$curDate = null;
		$i = -1;
		foreach($periods as $i => $hours)
		{
			$hour = $hours[0];
			$curDate = new DateTime($hour);

			if($i % 2 !== 0)
			{
				$diff = $curDate->getTimestamp() - $prevDate->getTimestamp();
				$secondsLeft -= $diff;
			}

			$prevDate = $curDate;
		}

		if($secondsLeft > 0)
		{
			$departureTime = ($i % 2 === 0) ? $curDate : new DateTime();
			/* @var $departureTime DateTime */
			$departureTime->setTimestamp($departureTime->getTimestamp() + $secondsLeft);
		}
		else
		{
			$departureTime = $curDate; //Overtime, return last hour, arrival or departure.
		}

		return $departureTime;
	}

}
