<?php
 namespace Symfony\Component\HttpKernel\EventListener; use Psr\Container\ContainerInterface; class TestSessionListener extends AbstractTestSessionListener { private $container; public function __construct(ContainerInterface $container) { $this->container = $container; } protected function getSession() { if (!$this->container->has('session')) { return; } return $this->container->get('session'); } } 