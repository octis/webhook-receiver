<?php

namespace Octis\Webhookreceiver\Plugin\GitServer;

/**
 * A class for the GitHub server specifics.
 */
class GitLab {

  /**
   * Returning the GitServer type.
   */
  public function getType() {
    return 'gitlab';
  }
}
