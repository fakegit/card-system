<?php
 namespace Doctrine\Common\Persistence\Mapping\Driver; use Doctrine\Common\Persistence\Mapping\ClassMetadata; interface MappingDriver { public function loadMetadataForClass($className, ClassMetadata $metadata); public function getAllClassNames(); public function isTransient($className); } 