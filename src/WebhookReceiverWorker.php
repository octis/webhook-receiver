<?php

namespace Webham\Devops;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

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
    public function __construct()
    {
        $this->fs = new Filesystem();
        $this->getRequestVars();
    }

    /**
     * Getting the variables from the webhook request.
     */
    private function getRequestVars()
    {
        $this->requestVars = json_decode(file_get_contents('php://input'));
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
                    // Comparing the secret token and the branch.
                    if ($this->config['repos'][$this->requestVars->project->url]['secret_token'] == $_GET['token'] &&
                      $this->requestVars->refs == $this->config['repos'][$this->requestVars->project->url]['branch']) {
                        $branch = $this->config['repos'][$this->requestVars->project->url]['branch'];
                        foreach ($this->config['repos'][$this->requestVars->project->url]['callbacks'] as $callback) {
                            // Calling the callback function.
                            $output = $callback(
                              $this->config['repos'][$this->requestVars->project->url],
                              $this->requestVars
                            );
                        }
                    } else {
                        throw new \Exception('Secret is false.');
                    }
                } else {
                    throw new \Exception('The repo that requested this file does not exist in the configuration.');
                }
            } else {
                throw new \Exception(
                    'There is no request. This API point is supposed to be requested by a git webhook.'
                );
            }
        } else {
            throw new \Exception('There are no repos declared.');
        }

        print $output;
    }
}
