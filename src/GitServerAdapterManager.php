<?php

namespace Octis\Webhookreceiver;

use Doctrine\Common\Annotations\AnnotationReader;

/**
 * The Webhook Receiver Adapter Manager.
 */
class GitServerAdapterManager {

  private $adapters;
  private $annotationReader;

  /**
   * Constructing.
   */
  public function __construct() {
    $this->annotationReader = new AnnotationReader();
  }

  /**
   * Register all adapters in a folder.
   */
  public function registerAdapters($dir = 'Plugin/GitServer/', $namespace = 'Octis\Webhookreceiver') {

      // Get all files in e dir.
      foreach(glob($dir . "*.php") as $class) {

          // Loading all the class files.
          if (file_exists($class)) {
              include $class;
          }

          // Iterating all the classes.
          $class_name = $this->classFileToName($class, $namespace);
          if (class_exists($class_name)) {
              // Get the single class.
              $current_class = new $class_name;
              // Read the annotations.
              $current_class_annotations = $this->annotationReader->getClassAnnotations($current_class);

              // If it is a git server adapter - register it.
              if (
                  !empty($current_class_annotations['type'])
                  && $current_class_annotations['type'] == 'git_server_adapter'
                  && !empty($current_class_annotations['id'])
              ) {
                  $adapters[$current_class_annotations['id']] = $current_class;
              }
          }
      }
  }

  /**
   * Class file to class name.
   */
  public function classFileToName($class, $namespace) {
      return $namespace . "\\" . str_replace(
        '/',
        '\\',
        rtrim($class, ".php")
      );
  }

  /**
   * {@inheritDoc}
   */
  public function get($name)
  {
      if (!$this->has($name)) {
        throw new ServiceNotFoundException('Service not found: ' . $name);
      }

      return $this->adapters[$name];
  }

  /**
   * {@inheritDoc}
   */
  public function has($name)
  {
      return isset($this->adapters[$name]);
  }

  /**
   * {@inheritDoc}
   */
  public function setAdapter(GitServerAdapterInterface $adapter)
  {
      $this->adapters[$adapter->getType()] = $adapter;
  }

}
