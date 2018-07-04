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

    /**
    * {@inheritdoc}
    */
    public function __construct($ymlFile)
    {
        $this->fs = new Filesystem();
        $this->buildFromYml($ymlFile);
        
        $this->getRequestVars();
    }

    /**
     * Getting the variables from the webhook request.
     */
    private function getRequestVars()
    {
        $request = Request::createFromGlobals();

        // @todo create the adapter manager

        // @todo register adapters

        // @todo get adapter

        // @todo create an adapter manager.
        // Implementing the adapter.
        if (!empty($request->getContent())) {
          $this->requestVars = new Octis\Webhookreceiver\Plugin\GitServer\GitLabAdapter($request);
        }

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
            $this->logger->log('status', $this->requestVars);
        }

        $output = 'Nothing executed.';

        // Check if there are any defined repos.
        if ((count($this->config['repos']) > 0)) {
            if (!empty($this->requestVars->project->url)) {
                // Check if current runner is with a declared git repo.
                if (in_array($this->requestVars->project->url, array_keys($this->config['repos']))) {
                    // Comparing the secret token if on.
                    if (!empty($this->config['repos'][$this->requestVars->project->url]['secret_token'])
                      && $this->config['repos'][$this->requestVars->project->url]['secret_token'] == $_GET['token'] {
                        // Comparing the branch.
                        foreach ($this->config['repos'][$this->requestVars->project->url]['actions'] as $callback) {
                            if ($this->requestVars->ref == $callback['trigger_branch']) {
                              // Calling the callback function.
                              $output = $callback['callback'](
                                $callback['arguments'],
                                $this->requestVars
                              );
                            } else {
                                throw new \Exception(
                                  'Branch does not match.'
                                );
                            }
                        }
                    } else {
                        throw new \Exception(
                          'Secret is false.'
                        );
                    }
                } else {
                    throw new \Exception(
                      'The repo that requested this file does not exist in the configuration.'
                    );
                }
            } else {
                throw new \Exception(
                    'There is no request. This API point is supposed to be requested by a git webhook.'
                );
            }
        } else {
            throw new \Exception(
              'There are no repos declared.'
            );
        }

        print $output;
    }
}
