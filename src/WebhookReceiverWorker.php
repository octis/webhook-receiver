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

    /**
    * {@inheritdoc}
    */
    public function __construct($ymlFile)
    {
        $this->fs = new Filesystem();
        $this->buildFromYml($ymlFile);
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
     * Sets a logger.
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * The actual creation of the API point.
     */
    public function createApiPoint()
    {

        if (!empty($this->logger)) {
            $this->logger->log('status', $this->request);
        }

        $output = 'Nothing executed.';

        // Check if there are any defined repos.
        if (count($this->config['repos']) > 0) {

            foreach ($this->config['repos'] as $repo) {

                // Load the adapter.
                if (
                  is_subclass_of($repo['git_server_adapter'], 'GitServerAdapterInterface')
                ) {
                  $gitServerAdapter = new $repo['git_server_adapter'];
                  $currentRepoRequest = $gitServerAdapter->buildRequest($this->request);
                }

                // Check if current runner is with a declared git repo.
                if ($currentRepoRequest->getRepoUrl() == $repo['git_url']) {
                    // Comparing the secret token if on.
                    if (
                      !empty($repo['secret_token'])
                      && ($repo['secret_token'] == $currentRepoRequest->getSecret())
                    ) {
                        // Comparing the branch.
                        foreach ($repo['actions'] as $callback) {
                            if (
                              $currentRepoRequest->getTriggerBranch() == $callback['trigger_branch']
                            ) {
                              // Calling the callback function.
                              $output = $callback['callback'](
                                $callback['arguments'],
                                $currentRepoRequest
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

        print $output;
    }
}
