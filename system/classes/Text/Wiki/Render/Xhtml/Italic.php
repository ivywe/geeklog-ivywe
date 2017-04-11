<?php

namespace Geeklog\Text\Wiki;

/**
 * Italic rule end renderer for Xhtml
 * PHP versions 4 and 5
 *
 * @category   Text
 * @package    Text_Wiki
 * @author     Paul M. Jones <pmjones@php.net>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    CVS: $Id: Italic.php 191862 2005-07-30 08:03:29Z toggg $
 * @link       http://pear.php.net/package/Text_Wiki
 */

/**
 * This class renders italic text in XHTML.
 *
 * @category   Text
 * @package    Text_Wiki
 * @author     Paul M. Jones <pmjones@php.net>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/Text_Wiki
 */
class Text_Wiki_Render_Xhtml_Italic extends Text_Wiki_Render
{
    public $conf = array(
        'css' => null,
    );

    /**
     * Renders a token into text matching the requested format.
     *
     * @param array $options The "options" portion of the token (second
     *                       element).
     * @return string The text rendered from the token options.
     */
    public function token($options)
    {
        if ($options['type'] === 'start') {
            $css = $this->formatConf(' class="%s"', 'css');

            return "<i$css>";
        }

        if ($options['type'] === 'end') {
            return '</i>';
        }

        return null;
    }
}
