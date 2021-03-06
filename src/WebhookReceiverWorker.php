<?php

namespace Octis\Webhookreceiver;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Request;

/**
 * The main class that receives the webhook requests.
 */
class WebhookReceiverWorker
{

    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Config from the YML file.
     *
     * @var array
     */
    private $config;

    /**
     * Request vars.
     *
     * @var array
     */
    private $requestVars;

    /**
     * Filesystem instance.
     *
     * @var Filesystem
     */
    private $fs;
    private $request;
    private $output = 'Nothing executed.';

    /**
    * {@inheritdoc}
    */
    public function __construct($ymlFile = '')
    {
        $this->fs = new Filesystem();
        if (!empty($ymlFile)) {
          $this->buildFromYml($ymlFile);
        }
        $this->request = Request::createFromGlobals();
    }

    /**
     * Building the Receiver from yml.
     */
    public function buildFromYml($pathToYmlFile)
    {
        if ($this->fs->exists($pathToYmlFile)) {
            $this->config = Yaml::parse(file_get_contents($pathToYmlFile));
        } else {
            throw new \Exception('YML file does not exist');
        }
    }

    /**
     * Building the Receiver from array.
     */
    public function buildFromArray($configArray)
    {
        if (!empty($configArray)) {
            $this->config = $configArray;
        } else {
            throw new \Exception('Config array does not exist.');
        }
    }

    /**
     * Sets a logger.
     *
     * @param LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * Ensure early headers so that the hook runner don't timeout the callback.
     */
    private function ensureEarlyHeaders()
    {
      // ob_end_clean();
      ignore_user_abort();
      ob_start();
      header("Connection: close");
      header("Content-Length: " . ob_get_length());
      ob_end_flush();
      flush();
      return 'Running hook callback ...';
    }

    /**
     * The actual creation of the API point.
     */
    public function createApiPoint()
    {

        if (!empty($this->logger)) {
            $this->logger->log(
              100,
              print_r($this->request, true)
            );
        }

        // Check if there are any defined repos.
        if (count($this->config['repos']) > 0) {

            foreach ($this->config['repos'] as $repo) {

                // Load the adapter.
                if (!empty($repo['git_server_adapter'])) {
                  $gitServerAdapter = new $repo['git_server_adapter'];

                  if (
                    is_subclass_of($gitServerAdapter, 'Octis\Webhookreceiver\GitServerAdapterInterface')
                  ) {
                    $gitServerAdapter->buildRequest(
                      $this->request
                    );
                  }
                  else {
                    throw new \Exception(
                      'You have to define a git server adapter class.'
                    );
                  }
                }

                // Check if current runner is with a declared git repo.
                if (
                  $gitServerAdapter->hasRepoUrl()
                  && $gitServerAdapter->getRepoUrl() == $repo['git_url']
                ) {
                    // Comparing the secret token if on.
                    if (
                      !empty($repo['secret_token'])
                      && ($repo['secret_token'] == $gitServerAdapter->getSecret())
                    ) {
                        // Comparing the branch.
                        foreach ($repo['actions'] as $callback) {
                            if (
                              $gitServerAdapter->hasTriggerBranch()
                              && $gitServerAdapter->getTriggerBranch() == $callback['trigger_branch']
                            ) {
                              // Calling the callback function.
                              $this->output = $callback['callback'](
                                $callback['arguments'],
                                $gitServerAdapter->getRawRequestVars()
                              );
                            } else {
                                throw new \Exception(
                                  'Branch does not match.'
                                );
                            }
                        }
                    } else {
                        throw new \Exception(
                          'Secret is false or not set.'
                        );
                    }
                }
            }
        } else {
            throw new \Exception(
              'There are no repos declared.'
            );
        }

        print $this->output;
    }
}
