<?php

namespace Octis\Webhookreceiver\Plugin\Action;

use Symfony\Component\Filesystem\Filesystem;

/**
 * A plugin simply for making a git pull.
 */
class GitPull
{

    /**
     * A callback for making a git pull.
     */
    public static function execute($arguments, $requestVars)
    {
        $fs = new Filesystem();
        if (!empty($arguments['dir']) && $fs->exists($arguments['dir'])) {
            $command = 'cd ' . $arguments['dir'] . ' && git pull origin ' . $arguments['branch'];
            return shell_exec($command);
        }
    }
}
