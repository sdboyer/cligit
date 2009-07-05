<?php

/**
 * @file abstracts.inc
 *
 * Contains abstract class declarations that are used throughout cligit, and as
 * such are excluded from the selective loader mechanism.
 */

abstract class GitCommand implements CLICommand {
  public $internalSwitches = 0;
  public $cmdSwitches = 0;
  protected $switchInfo = array();
  public $cmds = array();
  protected $cmdOpts = array();

  // internal state switches
  const PREPARED      = 0x001;
  const CACHE_OUTPUT  = 0x002;

  public function __construct(CLIWrapperConfig $config, $defaults) {
    $this->config = $config;
    $this->setSwitches();
  }

  abstract public function setSwitches();

  public function attachProcHandler(CLIProcHandler $proc) {
    $this->proc = $proc;
    return $this;
  }

  public function prepare() {
    $this->internalSwitches |= self::PREPARED;
    $cmds = array();

    foreach ($this->switchInfo as $switch => $info) {
      if ($this->cmdSwitches & $switch) {
        $cmds[$switch] = $info;
      }
    }
    ksort($cmds);

    $opts = array();
    $this->processOpts($opts, $this->cmdOpts);
    asort($opts, SORT_NUMERIC);
    $cmds = array_merge($cmds, array_keys($opts));
    array_unshift($cmds, 'git', $this->command);
    return $cmds;
  }

  /**
   * Helper function for GitCommand::prepare().
   *
   * Recursive function that builds an array containing the shell strings of
   * all the queued opts (as keys) and their ordinal position (as values).
   *
   * @param array $opts
   * @param mixed $arg
   * @return void
   */
  protected function processOpts(&$opts, $arg) {
    if (is_array($arg)) {
      foreach ($arg as $obj) {
        $this->processOpts($opts, $obj);
      }
    }
    else {
      $opts[$arg->getShellString()] = $arg->getOrdinal();
    }
  }

  public function execute($fluent = FALSE) {
    if (!$fluent) {
      return $this->proc->execute();
    }
    $this->proc->execute();
    return $this;
  }

  public function getShellString() {
    if (!($this->internalSwitches & self::PREPARED)) {
      $this->shellString = implode(' ', $this->prepare());
    }
    return $this->shellString;
  }
}
