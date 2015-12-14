<?php
/*
 * Session Management for PHP3
 *
 * (C) Copyright 1999-2000 NetUSE GmbH
 *                    Kristian Koehntopp
 *
 * $Id: template.class.php,v 1.9 2008/06/26 00:26:43 blaine Exp $
 *
 */

/*
 * Change log since version 7.2c
 *
 * Bug fixes to version 7.2c compiled by Richard Archer <rha@juggernaut.com.au>:
 * (credits given to first person to post a diff to phplib mailing list)
 *
 * Normalised all comments and whitespace (rha)
 * replaced "$handle" with "$varname" and "$h" with "$v" throughout (from phplib-devel)
 * added braces around all one-line if statements in: get_undefined, loadfile and halt (rha)
 * set_var was missing two sets of braces (rha)
 * added a couple of "return TRUE" statements (rha)
 * set_unknowns had "keep" as default instead of "remove" (from phplib-devel)
 * set_file failed to check for empty strings if passed an array of filenames (phplib-devel)
 * remove @ from call to preg_replace in subst -- report errors if there are any (NickM)
 * set_block unnecessarily required a newline in the template file (Marc Tardif)
 * pparse now calls this->finish to replace undefined vars (Layne Weathers)
 * get_var now checks for unset varnames (NickM & rha)
 * get_var when passed an array used the array key instead of the value (rha)
 * get_vars now uses a call to get_var rather than this->varvals to prevent undefined var warning (rha)
 * in finish, the replacement string referenced an unset variable (rha)
 * loadfile would try to load a file if the varval had been set to "" (rha)
 * in get_undefined, only match non-whitespace in variable tags as in finish (Layne Weathers & rha)
 * more elegant fix to the problem of subst stripping '$n', '\n' and '\\' strings (rha)
 *
 *
 * Changes in functionality which go beyond bug fixes:
 *
 * changed debug handling so set, get and internals can be tracked separately (rha)
 * added debug statements throughout to track most function calls (rha)
 * debug output contained raw HTML -- is now escaped with htmlentities (rha)
 * Alter regex in set_block to remove more whitespace around BEGIN/END tags to improve HTML layout (rha)
 * Add "append" option to set_var, works just like append in parse (dale at linuxwebpro.com, rha)
 * Altered parse so that append is honored if passed an array (Brian)
 * Converted comments and documentation to phpdoc style (rha)
 * Added clear_var to set the value of variables to "" (rha)
 * Added unset_var to usset variables (rha)
 *
 */

/**
 * The template class allows you to keep your HTML code in some external files
 * which are completely free of PHP code, but contain replacement fields.
 * The class provides you with functions which can fill in the replacement fields
 * with arbitrary strings. These strings can become very large, e.g. entire tables.
 *
 * Note: If you think that this is like FastTemplates, read carefully. It isn't.
 *
 */

define(TEMPLATE_VERSION, '2.3.1');

/**
* This should be the only Geeklog-isms in the file.  Didn't want to "infect"
* the class but it was necessary.  These options are global to all templates.
*/
global $TEMPLATE_OPTIONS;
$TEMPLATE_OPTIONS = Array(
    'path_cache' => "{$_CONF['path_data']}layout_cache/",   // location of template cache
    'path_prefixes' => Array(                               // used to strip directories off file names. Order is important here.
                        $_CONF['path_themes'],  // this is not path_layout. When stripping directories, you want files in different themes to end up in different directories.
                        $_CONF['path'],
                        '/'                     // this entry must always exist and must always be last
                                                // on Windows it should be whatever drive your webroot is in such as 'd:/'
                       ),
    'incl_phpself_header' => TRUE,          // set this to TRUE if your template cache exists within your web server's docroot.
    'cache_by_language' => TRUE,            // create cache directories for each language. Takes extra space but moves all $LANG variable text directly into the cached file
    'default_vars' => Array(                                // list of vars found in all templates.
                        'site_url'       => $_CONF['site_url'],
                        'site_admin_url' => $_CONF['site_admin_url'],
                        'layout_url'     => $_CONF['layout_url'],
                        'xhtml'          => (defined('XHTML') ? XHTML : ''),
                      ),
    'hook' => Array(),
    'override' => '',			// set this to the default theme name if you want to enable 'override' feature
);

/**
* Functions for prior to PHP-4.3.0
*/
if (!is_callable('file_get_contents')) {
  function file_get_contents($filename) {
    $retval = FALSE;
    
    if (($fp = @fopen($filename, 'rb')) !== FALSE) {
      $retval = '';
      
      while (!feof($fp)) {
        $retval .= fread($fp, 8192);
      }
      
      fclose($fp);
    }
    
    return $retval;
  }
}

if (!is_callable('ob_get_clean')) {
  function ob_get_clean() {
    $retval = ob_get_contents();
    ob_end_clean();
    return $retval;
  }
}

class KZ_Template
{
 /**
  * Determines how much debugging output Template will produce.
  * This is a bitwise mask of available debug levels:
  * 0 = no debugging
  * 1 = debug variable assignments
  * 2 = debug calls to get variable
  * 4 = debug internals (outputs all function calls with parameters).
  *
  * Note: setting $this->debug = TRUE will enable debugging of variable
  * assignments only which is the same behaviour as versions up to release 7.2d.
  *
  * @var       int
  * @access    public
  */
  var $debug    = 0;

 /**
  * The base directory array from which template files are loaded. When
  * attempting to open a file, the paths in this array are searched one at
  * a time. As soon as a file exists, the array search stops.
  *
  * @var       string
  * @access    private
  * @see       set_root
  */
  var $root     = array();

 /**
  * A hash of strings forming a translation table which translates variable names
  * into names of files containing the variable content.
  * $file[varname] = "filename";
  *
  * @var       array
  * @access    private
  * @see       set_file
  */
  var $file     = array();

 /**
  * A hash of strings forming a translation table which translates variable names
  * into regular expressions for themselves.
  * $varkeys[varname] = "/varname/"
  *
  * @var       array
  * @see       set_var
  */
  var $_varkeys = array();

 /**
  * A hash of strings forming a translation table which translates variable names
  * into values for their respective varkeys.
  * $varvals[varname] = "value"
  *
  * @var       array
  * @access    private
  * @see       set_var
  */
  var $varvals  = array();

 /**
  * Determines how to output variable tags with no assigned value in templates.
  *
  * @var       string
  * @access    private
  * @see       set_unknowns
  */
  var $unknowns = 'remove';

 /**
  * Determines how Template handles error conditions.
  * "yes"      = the error is reported, then execution is halted
  * "report"   = the error is reported, then execution continues by returning "FALSE"
  * "no"       = errors are silently ignored, and execution resumes reporting "FALSE"
  *
  * @var       string
  * @access    public
  * @see       halt
  */
  var $halt_on_error  = 'yes';

 /**
  * The last error message is retained in this variable.
  *
  * @var       string
  * @access    public
  * @see       halt
  */
  var $last_error     = '';

 /**
  * Variable name patter
  *
  * @var       string (regxp)
  * @access    public
  */
  var $var_name_pattern = '[_a-z][_a-zA-Z0-9]*';

 /******************************************************************************
  * Class constructor. May be called with two optional parameters.
  * The first parameter sets the template directory the second parameter
  * sets the policy regarding handling of unknown variables.
  *
  * usage: KZ_Template([string $root = array()], [string $unknowns = "remove"])
  *
  * @param     $root        path to template directory
  * @param     $string      what to do with undefined variables
  * @see       set_root
  * @see       set_unknowns
  * @access    public
  * @return    void
  */
  function KZ_Template($root = array(), $unknowns = 'remove') {
    global $TEMPLATE_OPTIONS;
    
    if ($this->debug & 4) {
      echo "<p><strong>Template:</strong> root = $root, unknowns = $unknowns</p>\n";
    }
    $this->set_root($root);
    $this->set_unknowns($unknowns);

    if (array_key_exists('default_vars', $TEMPLATE_OPTIONS)
     AND is_array($TEMPLATE_OPTIONS['default_vars'])) {
      foreach ($TEMPLATE_OPTIONS['default_vars'] as $k => $v) {
        $this->set_var($k, $v);
      }
    }
  }


 /******************************************************************************
  * Checks that $root is a valid directory and if so sets this directory as the
  * base directory from which templates are loaded by storing the value in
  * $this->root. Relative filenames are prepended with the path in $this->root.
  *
  * Returns TRUE on success, FALSE on error.
  *
  * usage: set_root(string $root)
  *
  * @param     $root         string containing new template directory
  * @see       root
  * @access    public
  * @return    boolean
  */
  function set_root($root) {
    global $TEMPLATE_OPTIONS;
    
    if (!is_array($root)) {
        $root = array($root);
    }
    if ($this->debug & 4) {
      echo "<p><strong>set_root:</strong> root = array(" . (count($root) > 0 ? '"' . implode('","', $root) . '"' : '') .")</p>\n";
    }
    
    $this->root = array();
    foreach ($root as $r) {
        if (empty($r)) continue;
        $r = rtrim($r, "/\\");
        if (!is_dir($r)) {
            // Check if "overrride" feature is enabled
            if ($TEMPLATE_OPTIONS['override'] == '') {
              $this->halt("set_root: $r is not a directory.");
              return FALSE;
            }
        }
        $this->root[] = $r;
    }

    if (count($this->root) == 0) {
        $this->halt("set_root: at least on existing directory must be set as root.");
        return FALSE;
    }
    return TRUE;
  }


 /******************************************************************************
  * Sets the policy for dealing with unresolved variable names.
  *
  * unknowns defines what to do with undefined template variables
  * "remove"   = remove undefined variables
  * "comment"  = replace undefined variables with comments
  * "keep"     = keep undefined variables
  *
  * Note: "comment" can cause unexpected results when the variable tag is embedded
  * inside an HTML tag, for example a tag which is expected to be replaced with a URL.
  *
  * usage: set_unknowns(string $unknowns)
  *
  * @param     $unknowns         new value for unknowns
  * @see       unknowns
  * @access    public
  * @return    void
  */
  function set_unknowns($unknowns = 'remove') {
    if ($this->debug & 4) {
      echo "<p><strong>unknowns:</strong> unknowns = $unknowns</p>\n";
    }
    $this->unknowns = $unknowns;
  }


 /******************************************************************************
  * Defines a filename for the initial value of a variable.
  *
  * It may be passed either a varname and a file name as two strings or
  * a hash of strings with the key being the varname and the value
  * being the file name.
  *
  * The new mappings are stored in the array $this->file.
  * The files are not loaded yet, but only when needed.
  *
  * Returns TRUE on success, FALSE on error.
  *
  * usage: set_file(array $filelist = (string $varname => string $filename))
  * or
  * usage: set_file(string $varname, string $filename)
  *
  * @param     $varname      either a string containing a varname or a hash of varname/file name pairs.
  * @param     $filename     if varname is a string this is the filename otherwise filename is not required
  * @access    public
  * @return    boolean
  */
  function set_file($varname, $filename = '') {
    if (!is_array($varname)) {
      if ($this->debug & 4) {
        echo "<p><strong>set_file:</strong> (with scalar) varname = $varname, filename = $filename</p>\n";
      }
      if ($filename == '') {
        $this->halt("set_file: For varname $varname filename is empty.");
        return FALSE;
      }
      $this->file[$varname] = $this->filename($filename);
    } else {
      foreach ($varname as $v => $f) {
        if ($this->debug & 4) {
          echo "<p><strong>set_file:</strong> (with array) varname = $v, filename = $f</p>\n";
        }
        if ($f == '') {
          $this->halt("set_file: For varname $v filename is empty.");
          return FALSE;
        }
        $this->file[$v] = $this->filename($f);
      }
    }
    return TRUE;
  }


 /******************************************************************************
  * A variable $parent may contain a variable block defined by:
  * &lt;!-- BEGIN $varname --&gt; content &lt;!-- END $varname --&gt;. This function removes
  * that block from $parent and replaces it with a variable reference named $name.
  * The block is inserted into the varkeys and varvals hashes. If $name is
  * omitted, it is assumed to be the same as $varname.
  *
  * Blocks may be nested but care must be taken to extract the blocks in order
  * from the innermost block to the outermost block.
  *
  * Returns TRUE on success, FALSE on error.
  *
  * usage: set_block(string $parent, string $varname, [string $name = ""])
  *
  * @param     $parent       a string containing the name of the parent variable
  * @param     $varname      a string containing the name of the block to be extracted
  * @param     $name         the name of the variable in which to store the block
  * @access    public
  * @return    boolean
  */
  function set_block($parent, $varname, $name = '') {
    if ($this->debug & 4) {
      echo "<p><strong>set_block:</strong> parent = $parent, varname = $varname, name = $name</p>\n";
    }
    if (!$this->loadfile($parent)) {
      $this->halt("set_block: unable to load $parent.");
      return FALSE;
    }
    if ($name == '') {
      $name = $varname;
    }

    $str = $this->get_var($parent);
    $reg = "/[ \t]*<!--\s+BEGIN $varname\s+-->\s*?\n?(\s*.*?\n?)\s*<!--\s+END $varname\s+-->\s*?\n?/sm";
    preg_match_all($reg, $str, $m);
    $str = preg_replace($reg, '{' . $name .'}', $str);
    $this->set_var($varname, $m[1][0]);
    $this->set_var($parent, $str);
    return TRUE;
  }


 /******************************************************************************
  * This functions sets the value of a variable.
  *
  * It may be called with either a varname and a value as two strings or an
  * an associative array with the key being the varname and the value being
  * the new variable value.
  *
  * The function inserts the new value of the variable into the $varkeys and
  * $varvals hashes. It is not necessary for a variable to exist in these hashes
  * before calling this function.
  *
  * An optional third parameter allows the value for each varname to be appended
  * to the existing variable instead of replacing it. The default is to replace.
  * This feature was introduced after the 7.2d release.
  *
  *
  * usage: set_var(string $varname, [string $value = ""], [boolean $append = FALSE])
  * or
  * usage: set_var(array $varname = (string $varname => string $value), [mixed $dummy_var], [boolean $append = FALSE])
  *
  * @param     $varname      either a string containing a varname or a hash of varname/value pairs.
  * @param     $value        if $varname is a string this contains the new value for the variable otherwise this parameter is ignored
  * @param     $append       if TRUE, the value is appended to the variable's existing value
  * @access    public
  * @return    void
  */
  function set_var($varname, $value = '', $append = FALSE) {
    if (!is_array($varname)) {
      if (!empty($varname)) {
        if ($this->debug & 1) {
          printf("<strong>set_var:</strong> (with scalar) <strong>%s</strong> = '%s'<br" . XHTML . ">\n", $varname, htmlspecialchars($value, ENT_QUOTES));
        }
        $this->varkeys[$varname] = '{' . $varname . '}';
        if ($append && isset($this->varvals[$varname])) {
          $this->varvals[$varname] .= $value;
        } else {
          $this->varvals[$varname] = $value;
        }
      }
    } else {
      foreach ($varname as $k => $v) {
        if (!empty($k)) {
          if ($this->debug & 1) {
            printf("<strong>set_var:</strong> (with array) <strong>%s</strong> = '%s'<br" . XHTML . ">\n", $k, htmlspecialchars($v, ENT_QUOTES));
          }
          $this->varkeys[$k] = '{' . $k . '}';
          if ($append && isset($this->varvals[$k])) {
            $this->varvals[$k] .= $v;
          } else {
            $this->varvals[$k] = $v;
          }
        }
      }
    }
  }


 /******************************************************************************
  * This functions clears the value of a variable.
  *
  * It may be called with either a varname as a string or an array with the 
  * values being the varnames to be cleared.
  *
  * The function sets the value of the variable in the $varkeys and $varvals 
  * hashes to "". It is not necessary for a variable to exist in these hashes
  * before calling this function.
  *
  *
  * usage: clear_var(string $varname)
  * or
  * usage: clear_var(array $varname = (string $varname))
  *
  * @param     $varname      either a string containing a varname or an array of varnames.
  * @access    public
  * @return    void
  */
  function clear_var($varname) {
    if (!is_array($varname)) {
      if (!empty($varname)) {
        if ($this->debug & 1) {
          printf("<strong>clear_var:</strong> (with scalar) <strong>%s</strong><br" . XHTML . ">\n", $varname);
        }
        $this->set_var($varname, '');
      }
    } else {
      foreach ($varname as $v) {
        if (!empty($v)) {
          if ($this->debug & 1) {
            printf("<strong>clear_var:</strong> (with array) <strong>%s</strong><br" . XHTML . ">\n", $v);
          }
          $this->set_var($v, '');
        }
      }
    }
  }


 /******************************************************************************
  * This functions unsets a variable completely.
  *
  * It may be called with either a varname as a string or an array with the 
  * values being the varnames to be cleared.
  *
  * The function removes the variable from the $varkeys and $varvals hashes.
  * It is not necessary for a variable to exist in these hashes before calling
  * this function.
  *
  *
  * usage: unset_var(string $varname)
  * or
  * usage: unset_var(array $varname = (string $varname))
  *
  * @param     $varname      either a string containing a varname or an array of varnames.
  * @access    public
  * @return    void
  */
  function unset_var($varname) {
    if (!is_array($varname)) {
      if (!empty($varname)) {
        if ($this->debug & 1) {
          printf("<strong>unset_var:</strong> (with scalar) <strong>%s</strong><br" . XHTML . ">\n", $varname);
        }
        unset($this->varvals[$varname]);
      }
    } else {
      foreach ($varname as $v) {
        if (!empty($v)) {
          if ($this->debug & 1) {
            printf("<strong>unset_var:</strong> (with array) <strong>%s</strong><br" . XHTML . ">\n", $v);
          }
          unset($this->varvals[$v]);
        }
      }
    }
  }


 /******************************************************************************
  * This function fills in all the variables contained within the variable named
  * $varname. The resulting value is returned as the function result and the
  * original value of the variable varname is not changed. The resulting string
  * is not "finished", that is, the unresolved variable name policy has not been
  * applied yet.
  *
  * Returns: the value of the variable $varname with all variables substituted.
  *
  * usage: subst(string $varname)
  *
  * @param     $varname      the name of the variable within which variables are to be substituted
  * @access    public
  * @return    string
  */
  function subst($varname) {
    if ($this->debug & 4) {
      echo "<p><strong>subst:</strong> varname = $varname</p>\n";
    }
    if (!$this->loadfile($varname)) {
      $this->halt("subst: unable to load $varname.");
      return FALSE;
    }
    
    $str = $this->get_var($varname);
    if (preg_match("/\{[!#]/", $str)) {	//	Contains CTL expression
      $str = $this->_compile($str);
    }
    $str  = str_replace(array_values($this->varkeys), array_values($this->varvals), $str);
    
    return $str;
  }


 /******************************************************************************
  * This is shorthand for print $this->subst($varname). See subst for further
  * details.
  *
  * Returns: always returns FALSE.
  *
  * usage: psubst(string $varname)
  *
  * @param     $varname      the name of the variable within which variables are to be substituted
  * @access    public
  * @return    FALSE
  * @see       subst
  */
  function psubst($varname) {
    if ($this->debug & 4) {
      echo "<p><strong>psubst:</strong> varname = $varname</p>\n";
    }
    print $this->subst($varname);

    return FALSE;
  }


 /******************************************************************************
  * The function substitutes the values of all defined variables in the variable
  * named $varname and stores or appends the result in the variable named $target.
  *
  * It may be called with either a target and a varname as two strings or a
  * target as a string and an array of variable names in varname.
  *
  * The function inserts the new value of the variable into the $varkeys and
  * $varvals hashes. It is not necessary for a variable to exist in these hashes
  * before calling this function.
  *
  * An optional third parameter allows the value for each varname to be appended
  * to the existing target variable instead of replacing it. The default is to
  * replace.
  *
  * If $target and $varname are both strings, the substituted value of the
  * variable $varname is inserted into or appended to $target.
  *
  * If $handle is an array of variable names the variables named by $handle are
  * sequentially substituted and the result of each substitution step is
  * inserted into or appended to in $target. The resulting substitution is
  * available in the variable named by $target, as is each intermediate step
  * for the next $varname in sequence. Note that while it is possible, it
  * is only rarely desirable to call this function with an array of varnames
  * and with $append = TRUE. This append feature was introduced after the 7.2d
  * release.
  *
  * Returns: the last value assigned to $target.
  *
  * usage: parse(string $target, string $varname, [boolean $append])
  * or
  * usage: parse(string $target, array $varname = (string $varname), [boolean $append])
  *
  * @param     $target      a string containing the name of the variable into which substituted $varnames are to be stored
  * @param     $varname     if a string, the name the name of the variable to substitute or if an array a list of variables to be substituted
  * @param     $append      if TRUE, the substituted variables are appended to $target otherwise the existing value of $target is replaced
  * @access    public
  * @return    string
  * @see       subst
  */
  function parse($target, $varname, $append = FALSE) {
    if (!is_array($varname)) {
      if ($this->debug & 4) {
        echo "<p><strong>parse:</strong> (with scalar) target = $target, varname = $varname, append = $append</p>\n";
      }
      $str = $this->subst($varname);
      if ($append) {
        $this->set_var($target, $this->get_var($target) . $str);
      } else {
        $this->set_var($target, $str);
      }
    } else {
      foreach ($varname as $i => $v) {
        if ($this->debug & 4) {
          echo "<p><strong>parse:</strong> (with array) target = $target, i = $i, varname = $v, append = $append</p>\n";
        }
        $str = $this->subst($v);
        if ($append) {
          $this->set_var($target, $this->get_var($target) . $str);
        } else {
          $this->set_var($target, $str);
        }
      }
    }
    
    if ($this->debug & 4) {
      echo "<p><strong>parse:</strong> completed</p>\n";
    }
    
    return $str;
  }


 /******************************************************************************
  * This is shorthand for print $this->parse(...) and is functionally identical.
  * See parse for further details.
  *
  * Returns: always returns FALSE.
  *
  * usage: pparse(string $target, string $varname, [boolean $append])
  * or
  * usage: pparse(string $target, array $varname = (string $varname), [boolean $append])
  *
  * @param     $target      a string containing the name of the variable into which substituted $varnames are to be stored
  * @param     $varname     if a string, the name the name of the variable to substitute or if an array a list of variables to be substituted
  * @param     $append      if TRUE, the substituted variables are appended to $target otherwise the existing value of $target is replaced
  * @access    public
  * @return    FALSE
  * @see       parse
  */
  function pparse($target, $varname, $append = FALSE) {
    if ($this->debug & 4) {
      echo "<p><strong>pparse:</strong> passing parameters to parse...</p>\n";
    }
    print $this->finish($this->parse($target, $varname, $append));
    return FALSE;
  }


 /******************************************************************************
  * This function returns an associative array of all defined variables with the
  * name as the key and the value of the variable as the value.
  *
  * This is mostly useful for debugging. Also note that $this->debug can be used
  * to echo all variable assignments as they occur and to trace execution.
  *
  * Returns: a hash of all defined variable values keyed by their names.
  *
  * usage: get_vars()
  *
  * @access    public
  * @return    array
  * @see       $debug
  */
  function get_vars() {
    if ($this->debug & 4) {
      echo "<p><strong>get_vars:</strong> constructing array of vars...</p>\n";
    }
    return $this->varvals;
  }


 /******************************************************************************
  * This function returns the value of the variable named by $varname.
  * If $varname references a file and that file has not been loaded yet, the
  * variable will be reported as empty.
  *
  * When called with an array of variable names this function will return a a
  * hash of variable values keyed by their names.
  *
  * Returns: a string or an array containing the value of $varname.
  *
  * usage: get_var(string $varname)
  * or
  * usage: get_var(array $varname)
  *
  * @param     $varname     if a string, the name the name of the variable to get the value of, or if an array a list of variables to return the value of
  * @access    public
  * @return    string or array
  */
  function get_var($varname) {
    if (!is_array($varname)) {
      if (isset($this->varvals[$varname])) {
        $str = $this->varvals[$varname];
      } else {
        $str = '';
      }
      if ($this->debug & 2) {
        printf ("<strong>get_var</strong> (with scalar) <strong>%s</strong> = '%s'<br" . XHTML . ">\n", $varname, htmlentities($str));
      }
      return $str;
    } else {
      foreach ($varname as $v) {
        if (isset($this->varvals[$v])) {
          $str = $this->varvals[$v];
        } else {
          $str = '';
        }
        if ($this->debug & 2) {
          printf ("<strong>get_var:</strong> (with array) <strong>%s</strong> = '%s'<br" . XHTML . ">\n", $v, htmlentities($str));
        }
        $result[$v] = $str;
      }
      return $result;
    }
  }


 /******************************************************************************
  * This function returns a hash of unresolved variable names in $varname, keyed
  * by their names (that is, the hash has the form $a[$name] = $name).
  *
  * Returns: a hash of varname/varname pairs or FALSE on error.
  *
  * usage: get_undefined(string $varname)
  *
  * @param     $varname     a string containing the name the name of the variable to scan for unresolved variables
  * @access    public
  * @return    array
  */
  function get_undefined($varname) {
    if ($this->debug & 4) {
      echo "<p><strong>get_undefined:</strong> varname = $varname</p>\n";
    }
    if (!$this->loadfile($varname)) {
      $this->halt("get_undefined: unable to load $varname.");
      return FALSE;
    }

    preg_match_all("/{([^ \t\r\n}]+)}/", $this->get_var($varname), $m);
    $m = $m[1];
    if (!is_array($m)) {
      return FALSE;
    }

    foreach ($m as $v) {
      if (!isset($this->varvals[$v])) {
        if ($this->debug & 4) {
         echo "<p><strong>get_undefined:</strong> undefined: $v</p>\n";
        }
        $result[$v] = $v;
      }
    }

    if (count($result)) {
      return $result;
    } else {
      return FALSE;
    }
  }


 /******************************************************************************
  * This function returns the finished version of $str. That is, the policy
  * regarding unresolved variable names will be applied to $str.
  *
  * Returns: a finished string derived from $str and $this->unknowns.
  *
  * usage: finish(string $str)
  *
  * @param     $str         a string to which to apply the unresolved variable policy
  * @access    public
  * @return    string
  * @see       set_unknowns
  */
  function finish($str) {
    global $TEMPLATE_OPTIONS;
    
    switch ($this->unknowns) {
      case 'keep':
      break;

      case 'remove':
        $str = preg_replace('/{[^ \t\r\n}]+}/', '', $str);
      break;

      case 'comment':
        $str = preg_replace('/{([^ \t\r\n}]+)}/', "<!-- Template variable \\1 undefined -->", $str);
      break;
    }
    
    return $str;
  }


 /******************************************************************************
  * This function prints the finished version of the value of the variable named
  * by $varname. That is, the policy regarding unresolved variable names will be
  * applied to the variable $varname then it will be printed.
  *
  * usage: p(string $varname)
  *
  * @param     $varname     a string containing the name of the variable to finish and print
  * @access    public
  * @return    void
  * @see       set_unknowns
  * @see       finish
  */
  function p($varname) {
    print $this->finish($this->get_var($varname));
  }


 /******************************************************************************
  * This function returns the finished version of the value of the variable named
  * by $varname. That is, the policy regarding unresolved variable names will be
  * applied to the variable $varname and the result returned.
  *
  * Returns: a finished string derived from the variable $varname.
  *
  * usage: get(string $varname)
  *
  * @param     $varname     a string containing the name of the variable to finish
  * @access    public
  * @return    void
  * @see       set_unknowns
  * @see       finish
  */
  function get($varname) {
    return $this->finish($this->get_var($varname));
  }


 /******************************************************************************
  * This function will return the pathname with $this->root prepended.
  * When the pasth is missing and "override" is enabled, then the path will be
  * translated into an "overriden" one.
  *
  * Returns: a string containing an absolute pathname.
  *
  * usage: filename(string $filename)
  *
  * @param     $filename    a string containing a filename
  * @access    private
  * @return    string
  * @see       set_root
  */
  function filename($filename) {
    global $_CONF, $_USER, $TEMPLATE_OPTIONS;
    
    if ($this->debug & 4) {
      echo "<p><strong>filename:</strong> filename = $filename</p>\n";
    }
    clearstatcache();
    foreach ($this->root as $r) {
      $f = $r . '/' . $filename;
      if (file_exists($f)) {
        return $f;
      }
    }

    if (isset($TEMPLATE_OPTIONS['override'])
     AND ($TEMPLATE_OPTIONS['override'] != '')) {

      // "override" feature is enabled, so let's replace the missing file
      // with the correspoing one of the default theme
      $current_theme = isset($_USER['theme']) ? $_USER['theme'] : $_CONF['theme'];
      $current_base  = $_CONF['path_themes'] . $current_theme;
      $current_base  = str_replace("\\", '/', $current_base);

      $replace_theme = $TEMPLATE_OPTIONS['override'];
      $replace_base  = $_CONF['path_themes'] . $replace_theme;
      $replace_base  = str_replace("\\", '/', $replace_base);

      foreach ($this->root as $r) {
        $f = $r . '/' . $filename;
        $f = str_replace($current_base, $replace_base, $f);
        if (file_exists($f)) {
          return $f;
        }
      }
      $this->halt("filename: file $filename does not exist.  Cannot override.");
    } else {
      $this->halt("filename: file $filename does not exist.");
    }
  }


 /******************************************************************************
  * This function will construct an expression for a given variable name.
  *
  * Returns: a string containing an expressionf for the variable name.
  *
  * usage: varname(string $varname)
  *
  * @param     $varname    a string containing a variable name
  * @access    private
  * @return    string
  */
  function varname($varname) {
    return '{' . $varname . '}';
  }


 /******************************************************************************
  * If a variable's value is undefined and the variable has a filename stored in
  * $this->file[$varname] then the backing file will be loaded and the file's
  * contents will be assigned as the variable's value.
  *
  * Note that the behaviour of this function changed slightly after the 7.2d
  * release. Where previously a variable was reloaded from file if the value
  * was empty, now this is not done. This allows a variable to be loaded then
  * set to "", and also prevents attempts to load empty variables. Files are
  * now only loaded if $this->varvals[$varname] is unset.
  *
  * NOTE: When the variable's value contains CTL expression like '{!' or '{#',
  *       then the value will be translated and evaluated as PHP expression.
  *
  * Returns: TRUE on success, FALSE on error.
  *
  * usage: loadfile(string $varname)
  *
  * @param     $varname    a string containing the name of a variable to load
  * @access    private
  * @return    boolean
  * @see       set_file
  */
  function loadfile($varname) {
    if ($this->debug & 4) {
      echo "<p><strong>loadfile:</strong> varname = $varname</p>\n";
    }

    if (!isset($this->file[$varname])) {
      // $varname does not reference a file so return
      if ($this->debug & 4) {
        echo "<p><strong>loadfile:</strong> varname $varname does not reference a file</p>\n";
      }
      return TRUE;
    }

    if (isset($this->varvals[$varname])) {
      // will only be unset if varname was created with set_file and has never been loaded
      // $varname has already been loaded so return
      if ($this->debug & 4) {
        echo "<p><strong>loadfile:</strong> varname $varname is already loaded</p>\n";
      }
      return TRUE;
    }
    
    $file_path = $this->file[$varname];
    $str = @file_get_contents($file_path);
    if ($str === FALSE) {
      $this->halt("loadfile: While loading $varname, $filename does not exist or is empty.");
      return FALSE;
    }
    
    $this->set_var($varname, $str);
    
    return TRUE;
  }


 /******************************************************************************
  * This function is called whenever an error occurs and will handle the error
  * according to the policy defined in $this->halt_on_error. Additionally the
  * error message will be saved in $this->last_error.
  *
  * Returns: always returns FALSE.
  *
  * usage: halt(string $msg)
  *
  * @param     $msg         a string containing an error message
  * @access    private
  * @return    void
  * @see       $halt_on_error
  */
  function halt($msg) {
    $this->last_error = $msg;

    if ($this->halt_on_error != 'no') {
      $this->haltmsg($msg);
    }

    if ($this->halt_on_error == 'yes') {
      die('<strong>Halted.</strong>');
    }

    return FALSE;
  }


 /******************************************************************************
  * This function prints an error message.
  * It can be overridden by your subclass of Template. It will be called with an
  * error message to display.
  *
  * usage: haltmsg(string $msg)
  *
  * @param     $msg         a string containing the error message to display
  * @access    public
  * @return    void
  * @see       halt
  */
  function haltmsg($msg) {
    printf('<strong>Template Error:</strong> %s<br' . XHTML . ">\n", $msg);
  }

///////////////////////////////////////////////////////////////////////////////
// THE LINES BELOW WERE ADDED BY mystral-kk <geeklog AT mystral-kk DOT net>
///////////////////////////////////////////////////////////////////////////////
  
  /**
  * Compile the loop syntax extended in CTL into equivalent PHP syntax
  *
  * @access  private
  * @param   str  string  string containing expressions like "{!...}"
  * @return       string  string translated into PHP equivalent
  */
  function _compile_loop($str)
  {
    // {!loop variable} ... {!endloop}
    $pattern = '/\{!loop\s+(' . $this->var_name_pattern . ')\}(.*?)\{!endloop\}/im';
    $num     = preg_match_all($pattern, $str, $M);
    
    for ($i = 0; $i < $num; $i ++) {
      $var_name = $M[$i][1];
      $tmp_name = $var_name . '__loopvar';
      $block    = $M[$i][2];
      $value    = intval($this->get_var($var_name));
      $replace  = '<?php $this->set_var(\'' . $var_name . '\', '
                . (string) $value . ');';
      if ($value > 0) {
        $replace .= 'for ($' . $tmp_name . ' = 1; $' . $tmp_name .  ' <= '
                 .  (string) $value . '; $' . $tmp_name . ' ++): ?>'
                 .  '<?php $this->set_var(\'' . $tmp_name . '\', $tmp_name); ?>'
                 .  $block;
      } else if ($value < 0) {
        $replace .= 'for ($' . $tmp_name . ' = -1; $' . $tmp_name .  ' >= '
                 .  (string) $value . '; $' . $tmp_name . ' --): ?>'
                 .  '<?php $this->set_var(\'' . $tmp_name . '\', $tmp_name); ?>'
                 .  $block;
      }
      
      $replace .= '<?php endfor; $this->unset_var(\'' . $tmp_name . '\'); ?>';
      $str = str_replace($M[$i][0], $replace, $str);
    }
    
    return $str;
  }
  
  /**
  * Compile the set syntax extended in CTL into equivalent PHP syntax
  *
  * @access  private
  * @param   str  string  string containing expressions like "{!...}"
  * @return       string  string translated into PHP equivalent
  */
  function _compile_set($str)
  {
    // {!set variable value}
    $pattern = '/\{!set\s+(' . $this->var_name_pattern . ')\s+/i';
    
    while (preg_match($pattern, $str, $M)) {
      $var_name = $M[1];
      $value    = '';
      $pos      = strpos($str, $M[0]) + strlen($M[0]);
      $level    = 1;
      
      while (TRUE) {
        $ch = $str[$pos ++];
        $value .= $ch;
        if ($ch == '}') {	// This won't be good for multibyte string ...
          $level --;
          if ($level == 0) {
            break;
          }
        } else if ($ch == '{') {
          $level ++;
        }
      }
      
      $replace = '<?php $this->set_var(\'' . $var_name . '\', \'' . $value
               . '\'); ?>';
      $str = str_replace($M[0] . $value, $replace, $str);
    }
    
    return $str;
  }
  
  /**
  * Compile the syntax extended in CTL into equivalent PHP syntax
  *
  * @access  private
  * @param   str  string  string containing expressions like "{!...}"
  * @return       string  string translated into PHP equivalent
  */
  function _compile($str)
  {
    $actions = array(
      // {!if var}
      '/\{!if\s+(' . $this->var_name_pattern . ')\}/i'
        =>	'<?php if ($this->get_var(\'$1\')): ?>',
      // {!endif}
      '/\{!endif\}/i'
        =>	'<?php endif; ?>',
      // {!else}
      '/\{!else\}/i'
        =>	'<?php else: ?>',
      // {!elseif var}
      '/\{!elseif\s+(' . $this->var_name_pattern . ')\}/i'
        =>	'<?php elseif ($this->get_var(\'$1\')): ?>',
      // {!while var}
      '/\{!while\s+(' . $this->var_name_pattern . ')\}/i'
        =>	'<?php while ($this->get_var(\'$1\')): ?>',
      // {!endwhile}
      '/\{!endwhile\}/i'
        =>	'<?php endwhile; ?>',
      // {!break}
      '/\{!break\}/i'
        =>	'<?php break; ?>',
      // {!continue}
      '/\{!continue\}/i'
        =>	'<?php continue; ?>',
      // {!inc variable}
      '/\{!inc\s+(' . $this->var_name_pattern . ')\}/i'
        =>	'<?php $this->set_var(\'$1\', intval($this->get_var(\'$1\')) + 1); ?>',
      // {!dec variable}
      '/\{!dec\s+(' . $this->var_name_pattern . ')\}/i'
        =>	'<?php $this->set_var(\'$1\', intval($this->get_var(\'$1\')) - 1); ?>',
      // {!inc+echo variable}
      '/\{!inc\+echo\s+(' . $this->var_name_pattern . ')\}/i'
        =>	'<?php $this->set_var(\'$1\', intval($this->get_var(\'$1\')) + 1); echo $this->get_var(\'$1\'); ?>',
      // {!dec+echo variable}
      '/\{!dec\+echo\s+(' . $this->var_name_pattern . ')\}/i'
        =>	'<?php $this->set_var(\'$1\', intval($this->get_var(\'$1\')) - 1); echo $this->get_var(\'$1\'); ?>',
      // {!unset variable}
      '/\{!unset\s+(' . $this->var_name_pattern . ')\}/i'
        =>	'<?php $this->unset_var(\'$1\'); ?>',
    );
    
    $str = preg_replace(array_keys($actions), array_values($actions), $str);
    $str = $this->_compile_loop($str);
    $str = $this->_compile_set($str);
    
    // Remove CTL comments
    $str = preg_replace('/\{#[^#]+#\}/', '', $str);
    
    ob_start();
    eval('?>' . $str);
    $str = ob_get_clean();
    return $str;
  }
  
  /**
  * Set "override" value
  *
  * @access  public
  * @param   val     mixed   When val is a string, it is the default theme name
  *                          to which template path will fall back when the
  *                          target template file is missing.  If $val is an
  *                          empty string or FALSE, then "override" feature
  *                          will be disabled.
  * @return          (void)
  */
  function setOverride($val = 'professional')
  {
    global $TEMPLATE_OPTIONS;
    
    if ($val === FALSE) {
      $val = '';
    }
    
    $TEMPLATE_OPTIONS['override'] = $val;
  }
  
  /**
  * Return the current "override" value
  *
  * @access  public
  * @return          string  the current "override" value or an empty string if
  *                  "override" is disabled.
  */
  function getOverride()
  {
    global $TEMPLATE_OPTIONS;
    
    return $TEMPLATE_OPTIONS['override'];
  }
}
