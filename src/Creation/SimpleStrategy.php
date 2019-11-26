<?php

namespace Arth\Util\Doctrine\Creation;

use Arth\Util\Doctrine\CreationStrategy;
use Arth\Util\Doctrine\GetManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;

class SimpleStrategy implements CreationStrategy
{
  use GetManager;

  private $idMap = [];

  public function __construct(ManagerRegistry $registry)
  {
    $this->registry = $registry;
  }
  public function create(ClassMetadata $meta, ?array $id, $entity = null, $data = null)
  {
    $className = $meta->getName();
    $em        = $this->getManager($className);
    if ($id) {
      $key    = implode('|', $id);
      $entity = $this->idMap[$className][$key] ?? $em->getRepository($className)->findOneBy($id) ?? $entity;
    }

    $entity = $entity ?? $meta->newInstance();
    if (isset($key)) {
      $this->idMap[$className][$key] = $entity;
    }

    return $entity;
  }
  public function clearState(): void
  {
    $this->idMap = [];
  }
}
