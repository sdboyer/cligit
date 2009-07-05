<?php

/**
 * @file exceptions.inc CLIGit Exceptions
 *
 * Contains special exception classes used throughout CLIGit.
 */

class CLIGitInvalidCommandException extends RuntimeException {

}

class CLIGitExcludedCommandException extends CLIGitInvalidCommandException {

}

class CLIGitUnknownCommandException extends CLIGitInvalidCommandException {

}