<?php

namespace Octis\Webhookreceiver\Plugin\GitServer;

use Symfony\Component\HttpFoundation\Request;
use Octis\Webhookreceiver\GitServerAdapterInterface;

/**
 * A class for the GitHub server specifics.
 */
class GitLabServerAdapter implements GitServerAdapterInterface {

  /**
   * The request variables.
   *
   * @var array
   */
  private $requestVars;
  /**
   * The request variables.
   *
   * @var Symfony\Component\HttpFoundation\Request
   */
  private $request;

  /**
   * Constructing.
   */
  public function __construct() {}

  /**
   * Building the request here.
   */
  public function buildRequest(Request $request) {
    $this->request = $request;
    $this->setRequestVars($this->request);
  }

  /**
   * Returning the GitServer type.
   */
  public function getType() {
    return 'gitlab';
  }

  /**
   * Getting the request vars.
   */
  public function setRequestVars($request) {
    $this->requestVars = json_decode($request->getContent());
  }

  /**
   * Check if there is a git repo url.
   */
  public function hasRepoUrl() {
    return !empty($this->requestVars->project->url) ? true : false;
  }

  /**
   * Returning the git repo url.
   */
  public function getRepoUrl() {
    return $this->requestVars->project->url;
  }

  /**
   * Check if there is a triggering branch.
   */
  public function hasTriggerBranch() {
    return !empty($this->requestVars->ref) ? true : false;
  }

  /**
   * Returning the triggering branch.
   */
  public function getTriggerBranch() {
    return $this->requestVars->ref;
  }

  /**
   * Returning the secret value.
   */
  public function getSecret() {
    if ($this->request->headers->has('X-Gitlab-Token')) {
      return $this->request->headers->get('X-Gitlab-Token');
    }
    else {
      return '';
    }
  }

  /**
   * Returning the raw request variables.
   */
  public function getRawRequestVars() {
    return $this->requestVars;
  }
}
