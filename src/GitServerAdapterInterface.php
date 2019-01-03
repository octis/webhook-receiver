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
   * Check if the git repo URL paramether exists.
   *
   * @return bool
   *   True if the paramether exists.
   */
  public function hasRepoUrl();

  /**
   * Retrieve a parameter from the Git server response.
   *
   * @return string
   *   The Git repository URL.
   */
  public function getRepoUrl();

  /**
   * Check if the git repo trigger branch paramether exists.
   *
   * @return bool
   *   True if the paramether exists.
   */
  public function hasTriggerBranch();

  /**
   * Retrieve a parameter from the Git server response.
   *
   * @return string
   *   The Git repository trigger branch.
   */
  public function getTriggerBranch();

}
