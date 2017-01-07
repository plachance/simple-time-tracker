<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Form\ProjectType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ProjetController.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 * @Route("/project")
 */
class ProjetController extends AppController
{
	/**
	 * Lists all Project entities.
	 *
	 * @Route("", name="project_index")
	 * @Method("GET")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function indexAction()
	{
		$user = $this->getUser();

		$em = $this->getDoctrine()->getManager();
		$projects = $em->getRepository('AppBundle:Project')->findBy(['user' => $user],
			['no' => 'desc', 'description' => 'desc']);

		return $this->render('project/index.html.twig',
				[
				'projects' => $projects,
		]);
	}

	/**
	 * Creates a new Project entity.
	 *
	 * @Route("/new", name="project_new")
	 * @Method({"GET", "POST"})
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function newAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();

		$project = new Project();
		$user = $this->getUser();
		$em->refresh($user); //It tries to save the user with an empty password otherwise.
		$project->setUser($user);
		$form = $this->createForm(ProjectType::class, $project);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid())
		{
			$em->persist($project);
			$em->flush();

			if($request->query->get('project_new', false))
			{
				return $this->redirectToRoute('project_new',
						[
						'r' => $request->query->get('r'),
						'project' => $project->getId(),
				]);
			}

			$this->addFlash('success', $this->trans('Project created.'));

			return $this->redirectReturnUrlOrRoute($request, 'project_index');
		}

		return $this->render('project/new.html.twig',
				[
				'project' => $project,
				'form' => $form->createView(),
		]);
	}

	/**
	 * Displays a form to edit an existing Project entity.
	 *
	 * @Route("/{id}/edit", name="project_edit", requirements={"id": "\d+"})
	 * @Method({"GET", "POST"})
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function editAction(Request $request, Project $project)
	{
		if($project->getUser()->getId() !== $this->getUser()->getId())
		{
			throw $this->createAccessDeniedException();
		}

		$roleAdminGranted = $this->isGranted('ROLE_ADMIN');
		$form = $this->createForm(ProjectType::class, $project,
			['noDisabled' => !$roleAdminGranted]);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->refresh($project->getUser()); //It tries to save the user with an empty password otherwise.
			$em->flush();

			$this->addFlash('success', $this->trans('Project saved.'));

			return $this->redirectReturnUrlOrRoute($request, 'project_index');
		}

		return $this->render('project/edit.html.twig',
				[
				'project' => $project,
				'form' => $form->createView(),
		]);
	}

	/**
	 * Deletes a Project entity.
	 *
	 * @Route("/{id}/delete", name="project_delete", requirements={"id": "\d+"})
	 * @Method({"GET", "DELETE"})
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function deleteAction(Request $request, Project $project)
	{
		if($project->getUser()->getId() !== $this->getUser()->getId())
		{
			throw $this->createAccessDeniedException();
		}

		$form = $this->createDeleteForm($project, $request->query->get('r'));
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid())
		{
			try
			{
				$em = $this->getDoctrine()->getManager();
//				$em->refresh($project->getUser()); //It tries to save the user with an empty password otherwise.
				$em->remove($project);
				$em->flush();

				return $this->redirectReturnUrlOrRoute($request, 'project_index');
			}
			catch(ForeignKeyConstraintViolationException $ex)
			{
				$this->addFlash('danger',
					$this->trans('This project can\'t be deleted because it has associated projects.'));
			}
		}

		return $this->render('project/delete.html.twig',
				[
				'project' => $project,
				'form' => $form->createView(),
		]);
	}

	/**
	 * Creates a form to delete a Project entity.
	 *
	 * @param Project $project The Project entity
	 * @param string|null $returnUrl
	 * @return Form The form
	 */
	private function createDeleteForm(Project $project, string $returnUrl = null)
	{
		$urlParameters = [
			'id' => $project->getId(),
			'r' => $returnUrl,
		];

		return $this->createFormBuilder()
				->setAction($this->generateUrl('project_delete', $urlParameters))
				->setMethod('DELETE')
				->getForm()
		;
	}

	/**
	 * Show the project summary.
	 *
	 * @Route("/{projectNo}/summary", name="project_summary", requirements={"projectNo": "\d+"})
	 * @Method("GET")
	 * @Security("is_granted('ROLE_ADMIN')")
	 */
	public function summaryAction(Request $request, int $projectNo)
	{
		$em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Project');
		$timePerUser = $repo->getProjectTimePerUser($projectNo);
		$timePerDescription = $repo->getProjectTimePerDescription($projectNo);

		return $this->render('project/cost.html.twig',
				[
				'project_no' => $projectNo,
				'time_per_user' => $timePerUser,
				'time_per_description' => $timePerDescription,
		]);
	}

	/**
	 * Pin/Unpin the specified project.
	 *
	 * @Route("/{id}/pin/{pinned}", name="project_pin", requirements={"id": "\d+", "pinned": "\d"})
	 * @Method("POST")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function pinAction(Request $request, Project $project, int $pinned)
	{
		if($project->getUser()->getId() !== $this->getUser()->getId())
		{
			throw $this->createAccessDeniedException();
		}

		$em = $this->getDoctrine()->getManager();
		$em->refresh($project->getUser()); //It tries to save the user with an empty password otherwise.

		$project->setPinned((bool) $pinned);

		$em->flush();

		return $this->redirectToRoute('task_current');
	}

}
