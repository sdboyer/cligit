<?php

require_once 'PHPUnit/Framework.php';
require_once CLIGIT_SRC . '/cligit.php';

class GitInstanceTest extends PHPUnit_Framework_TestCase {
  public function setUp() {
    $this->config = new GitCommandConfig();
    $this->instance = new GitInstance(substr(CLIGIT_SRC, 0, -4), $this->config);
  }

  public function testGitStatus() {
    $status = $this->instance->status();
    $status->execute();
  }
}