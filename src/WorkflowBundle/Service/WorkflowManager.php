<?php

namespace App\WorkflowBundle\Service;

use App\WorkflowBundle\Entity\Workflow;
use App\WorkflowBundle\Repository\WorkflowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WorkflowManager
{
  public function __construct(
    private EntityManagerInterface $entityManager,
    private WorkflowRepository $repository,
    private ValidatorInterface $validator
  ) {}

  public function createWorkflow(string $name, array $config): Workflow
  {
    $workflow = new Workflow();
    $workflow->setName($name);
    $workflow->setConfig($config);

    $errors = $this->validator->validate($workflow);
    if (count($errors) > 0) {
      throw new \InvalidArgumentException((string) $errors);
    }

    $this->entityManager->persist($workflow);
    $this->entityManager->flush();

    return $workflow;
  }

  public function updateWorkflow(Workflow $workflow, array $data): Workflow
  {
    $workflow->setName($data['name'] ?? $workflow->getName());
    $workflow->setConfig($data['config'] ?? $workflow->getConfig());

    $this->entityManager->flush();

    return $workflow;
  }

  public function deleteWorkflow(Workflow $workflow): void
  {
    $this->entityManager->remove($workflow);
    $this->entityManager->flush();
  }
}
