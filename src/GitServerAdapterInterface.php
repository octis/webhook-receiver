<?php

namespace Octis\Webhookreceiver;

/**
 * The strucutre of our Git Adapter Interface.
 */
interface GitServerAdapterInterface {

  /**
   * Retrieve a parameter from the Git server response.
   *
   * @return string
   *   The type of the Git server adapter.
   */
  public function getType();

  /**
   * Retrieve a parameter from the Git server response.
   *
   * @return string
   *   The Git repository URL.
   */
  public function getRepoUrl();

  /**
   * Retrieve a parameter from the Git server response.
   *
   * @return string
   *   The Git repository trigger branch.
   */
  public function getTriggerBranch();

}
