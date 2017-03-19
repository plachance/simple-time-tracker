<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Exception\TaskAlreadyStoppedException;
use AppBundle\Entity\Project;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use AppBundle\Form\TaskType;
use AppBundle\Repository\TaskRepository;
use AppBundle\Util\Intl;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TaskController.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 * @Route("/task")
 */
class TaskController extends AppController
{
	const LATEST_TASKS_COUNT = 5;

	/**
	 * Lists all Task entities.
	 *
	 * @Route("", name="task_index")
	 * @Method("GET")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function indexAction()
	{
		$em = $this->getDoctrine()->getManager();

		$repo = $em->getRepository('AppBundle:Task');
		/* @var $repo TaskRepository */
		$user = $this->getUser();
		/* @var $user UserInterface */
		$tasks = $repo->getTasksList($user);

		return $this->render('task/index.html.twig', [
				'tasks' => $tasks,
		]);
	}

	/**
	 * Creates a new Task entity.
	 *
	 * @Route("/new", name="task_new")
	 * @Method({"GET", "POST"})
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function newAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();

		$task = new Task();
		$projectId = $request->query->get('project');
		if($projectId != null)
		{
			$project = $em->find('AppBundle:Project', $projectId);
			if($project !== null)
			{
				$task->setProject($project);
			}
		}
		$user = $this->getUser();
		$em->refresh($user); //It tries to save the user with an empty password otherwise.
		$task->setUser($user);
		$form = $this->createForm(TaskType::class, $task, ['user' => $user]);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid())
		{
			$repo = $em->getRepository('AppBundle:Task');
			/* @var $repo TaskRepository */
			$currentTask = $repo->getCurrentTask($this->getUser());
			$currentTaskStopped = false;
			if($currentTask !== null && $currentTask->getDateTimeEnd() === null)
			{
				$currentTask->stop();
				$currentTaskStopped = true;
			}

			$em->persist($task);
			$em->flush();

			if($currentTaskStopped)
			{
				$this->addFlash('success', $this->trans('Current task stopped.'));
			}
			$this->addFlash('success', $this->trans('Task created.'));

			return $this->redirectReturnUrlOrRoute($request, 'task_index');
		}

		return $this->render('task/new.html.twig',
				[
				'task' => $task,
				'form' => $form->createView(),
		]);
	}

	/**
	 * Start a new task with the specified project and return to the current task page.
	 *
	 * @Route("/quickstart/{id}", name="task_quickstart", requirements={"id": "\d+"})
	 * @Method("GET")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function quickstartAction(Request $request, Project $project)
	{
		if($project->getUser()->getId() !== $this->getUser()->getId())
		{
			throw $this->createAccessDeniedException();
		}

		$now = new DateTime();

		$em = $this->getDoctrine()->getManager();

		$user = $this->getUser();
		$em->refresh($user); //It tries to save the user with an empty password otherwise.

		$repo = $em->getRepository('AppBundle:Task');
		/* @var $repo TaskRepository */
		$currentTask = $repo->getCurrentTask($this->getUser());
		if($currentTask !== null && $currentTask->getDateTimeEnd() === null)
		{
			$currentTask->setDateTimeEnd(clone $now);
		}

		$task = new Task();
		$task->setProject($project);
		$task->setDateTimeBegin(clone $now);
		$task->setUser($user);
		$em->persist($task);
		$em->flush();

		return $this->redirectReturnUrlOrRoute($request, 'task_current');
	}

	/**
	 * Displays a form to edit an existing Task entity.
	 *
	 * @Route("/{id}/edit", name="task_edit", requirements={"id": "\d+"}, options={"expose"=true})
	 * @Method({"GET", "POST"})
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function editAction(Request $request, Task $task)
	{
		if($task->getUser()->getId() !== $this->getUser()->getId())
		{
			throw $this->createAccessDeniedException();
		}

		$form = $this->createForm(TaskType::class, $task, ['user' => $task->getUser()]);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->refresh($task->getUser()); //It tries to save the user with an empty password otherwise.
			$em->flush();

			$this->addFlash('success', $this->trans('Task saved.'));

			return $this->redirectReturnUrlOrRoute($request, 'task_index');
		}

		return $this->render('task/edit.html.twig',
				[
				'task' => $task,
				'form' => $form->createView(),
		]);
	}

	/**
	 * Deletes a Task entity.
	 *
	 * @Route("/{id}/delete", name="task_delete", requirements={"id": "\d+"}, options={"expose"=true})
	 * @Method({"GET", "DELETE"})
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function deleteAction(Request $request, Task $task)
	{
		if($task->getUser()->getId() !== $this->getUser()->getId())
		{
			throw $this->createAccessDeniedException();
		}

		$form = $this->createDeleteForm($task, $request->query->get('r'));
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->refresh($task->getUser()); //It tries to save the user with an empty password otherwise.
			$em->remove($task);
			$em->flush();

			return $this->redirectReturnUrlOrRoute($request, 'task_index');
		}

		return $this->render('task/delete.html.twig',
				[
				'task' => $task,
				'form' => $form->createView(),
		]);
	}

	/**
	 * Creates a form to delete a Task entity.
	 *
	 * @param Task $task The Task entity
	 * @param string|null $returnUrl
	 * @return Form The form
	 */
	private function createDeleteForm(Task $task, string $returnUrl = null)
	{
		$urlParameters = [
			'id' => $task->getId(),
			'r' => $returnUrl,
		];

		return $this->createFormBuilder()
				->setAction($this->generateUrl('task_delete', $urlParameters))
				->setMethod('DELETE')
				->getForm()
		;
	}

	/**
	 * Show the current action of the user.
	 *
	 * @Route("/current", name="task_current")
	 * @Method("GET")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function currentAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();

		$repo = $em->getRepository('AppBundle:Task');
		/* @var $repo TaskRepository */
		$user = $this->getUser();
		/* @var $user User */
		$task = $repo->getCurrentTask($user);
		$latestTasks = $repo->getTasksList($user, self::LATEST_TASKS_COUNT);

		$projectRepo = $em->getRepository('AppBundle:Project');
		$sortOrder = $user->getProjectsOrderByAsc() ? 'asc' : 'desc';
		$pinnedProjects = $projectRepo->findBy(['user' => $this->getUser(), 'pinned' => true],
			['no' => $sortOrder, 'description' => $sortOrder]);

		$pinForm = $task ? $this->createPinForm($task->getProject()) : null;
		$stopForm = $task ? $this->createStopForm($task) : null;
		$restartForm = $task ? $this->createRestartForm($task) : null;
		$sortProjectsForm = $this->createSortProjectsForm($user);

		return $this->render('task/current.html.twig',
				[
				'task' => $task,
				'pin_form' => $pinForm ? $pinForm->createView() : null,
				'stop_form' => $stopForm ? $stopForm->createView() : null,
				'restart_form' => $restartForm ? $restartForm->createView() : null,
				'sort_projects_form' => $sortProjectsForm->createView(),
				'pinned_projects' => $pinnedProjects,
				'latest_tasks' => $latestTasks,
		]);
	}

	/**
	 * Stop the specified task.
	 *
	 * @Route("/{id}/stop", name="task_stop", requirements={"id": "\d+"})
	 * @Method("POST")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function stopAction(Request $request, Task $task)
	{
		if($task->getUser()->getId() !== $this->getUser()->getId())
		{
			throw $this->createAccessDeniedException();
		}

		$form = $this->createStopForm($task);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->refresh($task->getUser()); //It tries to save the user with an empty password otherwise.
			try
			{
				$task->stop();
				$em->flush();
			}
			catch(TaskAlreadyStoppedException $ex)
			{
				$intl = $this->get('app.intl');
				/* @var $intl Intl */
				$this->addFlash('info',
					$this->trans('Task already stopped at %dateTimeEnd%.',
						['%dateTimeEnd%' => $intl->localizeDate($task->getDateTimeEnd())]));
			}
		}

		return $this->redirectToRoute('task_current');
	}

	/**
	 * Creates a form to stop a Task entity.
	 *
	 * @param Project $project
	 * @return Form The form
	 */
	private function createPinForm(Project $project)
	{
		return $this->createFormBuilder()
				->setAction($this->generateUrl('project_pin', [
						'id' => $project->getId(),
						'pinned' => (int) !$project->getPinned(),
				]))
				->setMethod('POST')
				->getForm()
		;
	}

	/**
	 * Creates a form to change the order of the pinned projects list.
	 *
	 * @param User $user
	 * @return Form The form
	 */
	private function createSortProjectsForm(User $user)
	{
		return $this->createFormBuilder()
				->setAction($this->generateUrl('sort_projects', [
						'orderBy' => $user->getProjectsOrderByAsc() ? 'desc' : 'asc',
				]))
				->setMethod('POST')
				->getForm()
		;
	}

	/**
	 * Creates a form to stop a Task entity.
	 *
	 * @param Task $task The Task entity
	 * @return Form The form
	 */
	private function createStopForm(Task $task)
	{
		return $this->createFormBuilder()
				->setAction($this->generateUrl('task_stop', ['id' => $task->getId()]))
				->setMethod('POST')
				->getForm()
		;
	}

	/**
	 * Create a new task from the specified task.
	 *
	 * @Route("/{id}/restart", name="task_restart", requirements={"id": "\d+"})
	 * @Method("POST")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function restartAction(Request $request, Task $task)
	{
		if($task->getUser()->getId() !== $this->getUser()->getId())
		{
			throw $this->createAccessDeniedException();
		}

		$form = $this->createRestartForm($task);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->refresh($task->getUser()); //It tries to save the user with an empty password otherwise.
			$newTask = $task->restart();
			$em->persist($newTask);
			$em->flush();
		}

		return $this->redirectToRoute('task_current');
	}

	/**
	 * Creates a form to restart a Task entity.
	 *
	 * @param Task $task The Task entity
	 * @return Form The form
	 */
	private function createRestartForm(Task $task)
	{
		return $this->createFormBuilder()
				->setAction($this->generateUrl('task_restart', ['id' => $task->getId()]))
				->setMethod('POST')
				->getForm()
		;
	}

	/**
	 * Show the summary of all user's tasks.
	 *
	 * @Route("/summary", name="task_summary")
	 * @Method("GET")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function summaryAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();

		$repo = $em->getRepository('AppBundle:Task');
		/* @var $repo TaskRepository */
		$summary = $repo->getSummary($this->getUser());

		return $this->render('task/summary.html.twig',
				[
				'summary' => $summary,
		]);
	}

	/**
	 * Show the user's timesheet.
	 *
	 * @Route("/timesheet/{date}", name="task_timesheet", requirements={"date": "\d{4}-\d{2}-\d{2}"}, defaults={"date": "now"})
	 * @Method("GET")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function timesheetAction(Request $request, DateTime $date = null)
	{
		if($date === null)
		{
			$date = new DateTime();
		}

		if($date->format('w') !== '0')
		{
			$date->modify('last sunday');
		}

		$em = $this->getDoctrine()->getManager();

		$repo = $em->getRepository('AppBundle:Task');
		/* @var $repo TaskRepository */
		$timesheet = $repo->getTimeSheet($this->getUser(), clone $date);
		$total = array_pop($timesheet);
		$hours = $repo->getTimePeriods($this->getUser(), clone $date);

		return $this->render('task/timesheet.html.twig',
				[
				'timesheet' => $timesheet,
				'total' => $total,
				'hours' => $hours,
				'date' => $date,
		]);
	}

	/**
	 * Set the sort order for the pinned projects list.
	 *
	 * @Route("/sortprojects/{orderBy}", name="sort_projects", requirements={"orderby": "asc|desc"})
	 * @Method("POST")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function sortProjectsAction(Request $request, string $orderBy)
	{
		$em = $this->getDoctrine()->getManager();

		$user = $this->getUser();
		/* @var $user User */
		$em->refresh($user); //It tries to save the user with an empty password otherwise.

		$user->setProjectsOrderByAsc('asc' === $orderBy);

		$em->flush();

		return $this->redirectToRoute('task_current');
	}
}
