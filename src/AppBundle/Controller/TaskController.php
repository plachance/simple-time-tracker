<?php

declare(strict_types = 1);

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\Task;
use AppBundle\Form\TaskType;
use AppBundle\Repository\TaskRepository;
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
			$em->persist($task);
			$em->flush();

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
		if($currentTask->getDateTimeEnd() === null)
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
	 * @Route("/{id}/edit", name="task_edit", requirements={"id": "\d+"})
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
	 * @Route("/{id}/delete", name="task_delete", requirements={"id": "\d+"})
	 * @Method({"GET", "DELETE"})
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function deleteAction(Request $request, Task $task)
	{
		if($task->getUser()->getId() !== $this->getUser()->getId())
		{
			throw $this->createAccessDeniedException();
		}

		$form = $this->createDeleteForm($task);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->refresh($task->getUser()); //It tries to save the user with an empty password otherwise.
			$em->remove($task);
			$em->flush();

			return $this->redirectToRoute('task_index');
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
	 * @return Form The form
	 */
	private function createDeleteForm(Task $task)
	{
		return $this->createFormBuilder()
				->setAction($this->generateUrl('task_delete', ['id' => $task->getId()]))
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
		$task = $repo->getCurrentTask($this->getUser());

		$projectRepo = $em->getRepository('AppBundle:Project');
		$pinnedProjects = $projectRepo->findBy(['user' => $this->getUser(), 'pinned' => true],
			['no' => 'asc', 'description' => 'asc']);

		$stopForm = $this->createStopForm($task);
		$restartForm = $this->createRestartForm($task);

		return $this->render('task/current.html.twig',
				[
				'task' => $task,
				'stop_form' => $stopForm->createView(),
				'restart_form' => $restartForm->createView(),
				'pinned_projects' => $pinnedProjects,
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
			$task->stop();
			$em->flush();
		}

		return $this->redirectToRoute('task_current');
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

}
