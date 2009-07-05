<?php

define('CLIGIT_SRC', dirname(__FILE__));

require_once CLIGIT_SRC . '/lib/cli.inc';
require_once CLIGIT_SRC . '/lib/proc.inc';
require_once CLIGIT_SRC . '/registry.inc';
require_once CLIGIT_SRC . '/exceptions.inc';
require_once CLIGIT_SRC . '/abstracts.inc';

class GitInstance extends SplFileInfo implements CLIWrapper {
  public function __construct($path, GitCommandConfig $config = NULL, $flags = 0) {
    if (!$flags & CLIWrapper::SKIP_VERIFY) {
      $this->verify($path);
    }

    parent::__construct($path);

    if (is_null($config)) {
      $this->config = new GitCommandConfig();
    } else {
      $this->config = &$config;
    }
    $this->config->attachWrapper($this);

    if (!$flags & CLIWrapper::SKIP_BUILD) {
      $this->build();
    }
  }

  protected function verify($path) {
    if (!is_dir($path)) {
      throw new InvalidArgumentException(__CLASS__ . ' requires a directory argument, but "' . $path . '" was provided.', E_RECOVERABLE_ERROR);
    }
  }

  public function getWorkingPath() {
    return (string) $this;
  }

  protected function build() {

  }

  public function getRootPath() {

  }

  public function setSubPath() {

  }

  public function appendSubPath() {

  }

  /**
   * Performs basic build-out operations that are common to all commands.
   *
   * Does all necessary searching and autoloading of relevant command object
   * files; throws errors as necessary/appropriate to indicate when something
   * goes wrong.
   *
   * For commands that do not have their own special method to handle command
   * spawning, this method is all the building they get.
   *
   * @return GitCommand
   */
  protected function buildCommand($command_class, CLIProcHandler &$proc = NULL, $defaults = self::PCUD) {
    // Load the command and retrieve a reflection class from the registry.
    $reflection = CLIGitRegistry::loadCommand($command_class);

    if (is_null($proc)) {
      // If no proc handler was explicitly specified, then first try the global
      // proc handler specified on this CLIWrapper's config object (if any).
      if (!empty($this->config->proc) && $this->config->proc instanceof CLIProcHandler) {
        $proc = &$this->config->proc;
      }
      // Lowest priority (and most common) case: no global proc handler
      // specified on this CLIWrapperConfig object; use the default specified by
      // the command. This is the most common case.
      else {
        $proc_class = $reflection->getConstant('PROC_HANDLER');
        if (!class_exists($proc_class)) {
          throw new InvalidArgumentException("The requested command specified an unknown class, '$proc_class', as the default process handler", E_RECOVERABLE_ERROR);
        }
        $proc = new $proc_class();
        $proc->attachConfig($this->config);
      }
    }
    if (!$proc instanceof CLIProcHandler) {
      throw new LogicException("Unable to create a process handler, and no valid handler was otherwise specified.", E_ERROR);
    }
    $cmd = new $command_class($this->config, $defaults);
    $proc->attachCommand($cmd);
    return $cmd;
  }

  /**
   * Implementation of php's __call magic method; used for internal routing of
   * calls to their appropriate command objects.
   *
   * May add some more complicated logic later, but for now, it simply passes
   * through to GitInstance::buildCommand().
   *
   * @param $name
   * @param $arguments
   * @return mixed
   */
  public function __call($name, $arguments) {
    $proc = array_shift($arguments);
    $defaults = array_shift($arguments);
    return $this->buildCommand('Git' . ucfirst($name), $proc, $defaults);
  }
}

class GitCommandConfig implements CLIWrapperConfig {
  /**
   *
   * @var SvnInstance
   */
  protected $instance;
  protected $env;
  public $subPath = '';
  public $usePrependPath = TRUE;

  public function attachWrapper(CLIWrapper &$wrapper) {
    $this->instance = &$wrapper;
  }

  public function authorName($author) {
    $this->env['GIT_AUTHOR_NAME'] = $author;
  }

  public function authorEmail($email) {
    $this->env['GIT_AUTHOR_EMAIL'] = $email;
  }

  public function getPrependPath() {
    return $this->instance->getPrependPath();
  }

  public function getWorkingPath() {
    return $this->instance->getWorkingPath();
  }

  public function getEnv() {
    return $this->env;
  }
}
