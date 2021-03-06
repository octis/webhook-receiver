<?php

namespace Octis\Webhookreceiver\Plugin\GitServer;

use Octis\Webhookreceiver\GitServerAdapterInterface;

/**
 * A class for the GitHub server specifics.
 */
class GitHubServerAdapter implements GitServerAdapterInterface {

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
