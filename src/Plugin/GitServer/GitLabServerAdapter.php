<?php

namespace Octis\Webhookreceiver\Plugin\GitServer;

use Symfony\Component\HttpFoundation\Request;

/**
 * A class for the GitHub server specifics.
 *
 * @Info(
 *   id = "gitlab",
 *   type = "git_server_adapter",
 * )
 */
class GitLabServerAdapter {

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
   * Returning the git repo url.
   */
  public function getRepoUrl() {
    return $this->requestVars->project->url;
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
