<?php

namespace Octis\Webhookreceiver\Plugin\GitServer;

use Symfony\Component\HttpFoundation\Request;

/**
 * A class for the GitHub server specifics.
 */
class GitLabAdapter {

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
  public function __construct(Request $request) {
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
   * Return the git repo url.
   */
  public function getGitUrl() {
    return $this->requestVars->project->url;
  }

  /**
   * Return the triggering branch.
   */
  public function getTriggerBranch() {
    return $this->requestVars->ref;
  }

  /**
   * Returning the secret value.
   */
  public function getSecret() {
    return 'secret';
  }

  /**
   * Returning the raw request variables.
   */
  public function getRawRequestVars() {
    return $this->requestVars;
  }
}
