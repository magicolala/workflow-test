<?php

namespace App\WorkflowBundle\Controller;

use App\WorkflowBundle\Entity\Workflow;
use App\WorkflowBundle\Form\WorkflowType;
use App\WorkflowBundle\Service\WorkflowManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/workflows')]
class WorkflowController extends AbstractController
{
	public function __construct(
		private WorkflowManager $workflowManager
	) {}

	#[Route('', name: 'workflow_index', methods: ['GET'])]
	public function index(): Response
	{
		$workflows = $this->workflowManager->getRepository()->findAll();

		return $this->render('@Workflow/workflow/index.html.twig', [
			'workflows' => $workflows
		]);
	}

	#[Route('/new', name: 'workflow_new', methods: ['GET', 'POST'])]
	public function new(Request $request): Response
	{
		$form = $this->createForm(WorkflowType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$data = $form->getData();
			$this->workflowManager->createWorkflow($data->getName(), $data->getConfig());

			$this->addFlash('success', 'Workflow created successfully');
			return $this->redirectToRoute('workflow_index');
		}

		return $this->render('@Workflow/workflow/new.html.twig', [
			'form' => $form->createView()
		]);
	}
}
