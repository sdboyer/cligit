<?php

class CLIGitRegistry {
  protected static $commands = array();

  /**
   * The list of commands that have been intentionally excluded from cligit.
   *
   * @var array
   */
  public static $excludedCommands = array(
    'Gitk',
    'GitCitool',
    'GitGui',
  );

  public static function commandExists($command) {

  }

  /**
   * Loads a given git command, or throws an informative exception if such a
   * command cannot be loaded.
   *
   * @param string $command
   *  The name of the git command to be loaded.
   * @return unknown_type
   */
  public static function loadCommand($command) {
    // Check to see if command has already been loaded; if not, do our checks.
    if (empty(self::$commands[$command])) {
      // Check to see if it is an excluded command; if so, throw an exception.
      if (in_array($command, self::$excludedCommands)) {
        throw new CLIGitExcludedCommandException($command);
      }
      if (!is_file(CLIGIT_SRC . "/commands/$command.inc")) {
        throw new CLIGitUnknownCommandException($command);
      }

      // Now ready to include the file.
      require_once CLIGIT_SRC . "/commands/$command.inc";
      // Store a reflected copy of the class in our registry array for later
      // checking.
      self::$commands[$command] = new ReflectionClass($command);
    }
    return self::$commands[$command];
  }
}

