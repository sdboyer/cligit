# cligit #

The PHP Git CLI library (cligit) is an all-userspace PHP interface that
facilitates easy interaction with the git binary directly from php code. The
philosophy behind the library is to replicate command line-style behavior as
much as possible; in other words, everything you know about using git from a
shell/command line will apply to cligit.



## Intentionally Excluded Commands ##

For some git commands, it doesn't really make much sense to make them available
in PHP-land. This is a list of the commands that have been intentionally
excluded, along with the reasons why. Note that all of these are subject to
change, provided a good enough argument :)

 * gitk -- cligit isn't intended for interactive operation. If you want this,
   you're probably better off invoking it directly in your shell.
   
 * git-citool -- cligit isn't intended for interactive operation. If you want
   this, you're probably better off invoking it directly in your shell.

 * git-gui -- cligit isn't intended for interactive operation. If you want this,
   you're probably better off invoking it directly in your shell.
