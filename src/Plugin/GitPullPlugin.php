<?php

namespace Octis\Webhookreceiver\Plugin;

use Symfony\Component\Filesystem\Filesystem;

/**
 * A plugin simply for making a git pull.
 */
class GitPullPlugin
{

    /**
     * A callback for the thing that the Plugin does.
     */
    public static function execute($config, $requestVars)
    {
        $fs = new Filesystem();
        // Predefined command - pull (pulls the remote branch).
        if (!empty($config['dir']) && $fs->exists($config['dir'])) {
            $command = 'cd ' . $config['dir'] . ' && git pull origin ' . $config['branch'];
            return shell_exec($command);
        }
    }
}
