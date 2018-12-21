<?php

namespace Octis\Webhookreceiver\Plugin\GitServer;

/**
 * A class for the GitHub server specifics.
 * @type = 'git_adapter'
 */
class GitHubServerAdapter {

    private $requestVars;

    /**
     * Returning the GitServer type.
     */
    public function getType() {
      return 'github';
    }

    /**
     * Getting the request vars.
     */
    public function getRequestVars() {
      $this->requestVars = json_decode(file_get_contents('php://input'));
    }

    // @todo get git url
    public function getGitUrl(){
      return $this->requestVars->project->url;
    }


    // @todo get trigger branch

    // @todo get secret

    // @todo get raw request array
}
