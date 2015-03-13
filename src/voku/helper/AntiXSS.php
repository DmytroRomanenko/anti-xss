<?php

namespace voku\helper;

/**
 * Anti XSS library
 *
 * ported from "CodeIgniter"
 *
 * @author      EllisLab Dev Team
 * @author      Lars Moelleken
 * @copyright   Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright   Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
class AntiXSS
{

  /**
   * XSS Hash
   *
   * Random Hash for protecting URLs.
   *
   * @var  string
   */
  protected $_xss_hash;

  /**
   * the replacement-string for not allowed strings
   *
   * @var string
   */
  protected $_replacement = '';

  /**
   * List of never allowed strings
   *
   * @var  array
   */
  protected $_never_allowed_str = array();

  protected $_never_allowed_str_afterwards = array();

  /**
   * List of never allowed regex replacements
   *
   * @var  array
   */
  protected $_never_allowed_regex = array(
      'javascript\s*:',
      '(document|(document\.)?window)\.(location|on\w*)',
      'expression\s*(\(|&\#40;)',
      // CSS and IE
      'vbscript\s*:',
      // IE, surprise!
      'wscript\s*:',
      // IE
      'jscript\s*:',
      // IE
      'vbs\s*:',
      // IE
      'Redirect\s+30\d',
      "([\"'])?data\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?"
  );

  public function __construct()
  {
    $this->_never_allowed_str = array(
        'document.cookie' => $this->_replacement,
        'document.write' => $this->_replacement,
        '.parentNode' => $this->_replacement,
        '.innerHTML' => $this->_replacement,
        '-moz-binding' => $this->_replacement,
        '<!--' => '&lt;!--',
        '-->' => '--&gt;',
        '<![CDATA[' => '&lt;![CDATA[',
        '<comment>' => '&lt;comment&gt;',
    );

    $this->_never_allowed_str_afterwards = array(
        'FSCommand=' => $this->_replacement,
        'onAbort=' => $this->_replacement,
        'onActivate=' => $this->_replacement,
        'onAttribute=' => $this->_replacement,
        'onAfterPrint=' => $this->_replacement,
        'onAfterUpdate=' => $this->_replacement,
        'onBeforeActivate=' => $this->_replacement,
        'onBeforeCopy=' => $this->_replacement,
        'onBeforeCut=' => $this->_replacement,
        'onBeforeDeactivate=' => $this->_replacement,
        'onBeforeEditFocus=' => $this->_replacement,
        'onBeforePaste=' => $this->_replacement,
        'onBeforePrint=' => $this->_replacement,
        'onBeforeUnload=' => $this->_replacement,
        'onBegin=' => $this->_replacement,
        'onBlur=' => $this->_replacement,
        'onBounce=' => $this->_replacement,
        'onCanPlay=' => $this->_replacement,
        'onCanPlayThrough=' => $this->_replacement,
        'onCueChange=' => $this->_replacement,
        'onCellChange=' => $this->_replacement,
        'onChange=' => $this->_replacement,
        'onClick=' => $this->_replacement,
        'onContextMenu=' => $this->_replacement,
        'onControlSelect=' => $this->_replacement,
        'onCopy=' => $this->_replacement,
        'onCut=' => $this->_replacement,
        'onDataAvailable=' => $this->_replacement,
        'onDataSetChanged=' => $this->_replacement,
        'onDataSetComplete=' => $this->_replacement,
        'onDblClick=' => $this->_replacement,
        'onDeactivate=' => $this->_replacement,
        'onDrag=' => $this->_replacement,
        'onDragEnd=' => $this->_replacement,
        'onDragLeave=' => $this->_replacement,
        'onDragEnter=' => $this->_replacement,
        'onDragOver=' => $this->_replacement,
        'onDragDrop=' => $this->_replacement,
        'onDrop=' => $this->_replacement,
        'onDurationChange=' => $this->_replacement,
        'onEmptied=' => $this->_replacement,
        'onEnd=' => $this->_replacement,
        'onEnded=' => $this->_replacement,
        'onError=' => $this->_replacement,
        'onErrorUpdate=' => $this->_replacement,
        'onFilterChange=' => $this->_replacement,
        'onFinish=' => $this->_replacement,
        'onFocus=' => $this->_replacement,
        'onFocusIn=' => $this->_replacement,
        'onFocusOut=' => $this->_replacement,
        'onHashChange=' => $this->_replacement,
        'onHelp=' => $this->_replacement,
        'onInput=' => $this->_replacement,
        'onInvalid=' => $this->_replacement,
        'onKeyDown=' => $this->_replacement,
        'onKeyPress=' => $this->_replacement,
        'onKeyUp=' => $this->_replacement,
        'onLayoutComplete=' => $this->_replacement,
        'onLoad=' => $this->_replacement,
        'onLoadStart=' => $this->_replacement,
        'onLoadedData=' => $this->_replacement,
        'onLoadedMetaData=' => $this->_replacement,
        'onLoseCapture=' => $this->_replacement,
        'onMediaComplete=' => $this->_replacement,
        'onMediaError=' => $this->_replacement,
        'onMouseDown=' => $this->_replacement,
        'onMouseEnter=' => $this->_replacement,
        'onMouseLeave=' => $this->_replacement,
        'onMouseMove=' => $this->_replacement,
        'onMouseOut=' => $this->_replacement,
        'onMouseOver=' => $this->_replacement,
        'onMouseUp=' => $this->_replacement,
        'onMouseWheel=' => $this->_replacement,
        'onMove=' => $this->_replacement,
        'onMoveEnd=' => $this->_replacement,
        'onMoveStart=' => $this->_replacement,
        'onOutOfSync=' => $this->_replacement,
        'onPageHide=' => $this->_replacement,
        'onPageShow=' => $this->_replacement,
        'onPaste=' => $this->_replacement,
        'onPause=' => $this->_replacement,
        'onPlay=' => $this->_replacement,
        'onPlaying=' => $this->_replacement,
        'onProgress=' => $this->_replacement,
        'onPropertyChange=' => $this->_replacement,
        'onPopState=' => $this->_replacement,
        'onRateChange=' => $this->_replacement,
        'onReadyStateChange=' => $this->_replacement,
        'onRepeat=' => $this->_replacement,
        'onReset=' => $this->_replacement,
        'onResize=' => $this->_replacement,
        'onResizeEnd=' => $this->_replacement,
        'onResizeStart=' => $this->_replacement,
        'onResume=' => $this->_replacement,
        'onReverse=' => $this->_replacement,
        'onRowsEnter=' => $this->_replacement,
        'onRowExit=' => $this->_replacement,
        'onRowDelete=' => $this->_replacement,
        'onRowInserted=' => $this->_replacement,
        'onScroll=' => $this->_replacement,
        'onSeek=' => $this->_replacement,
        'onSeeked=' => $this->_replacement,
        'onSeeking=' => $this->_replacement,
        'onSelect=' => $this->_replacement,
        'onSelectionChange=' => $this->_replacement,
        'onSelectStart=' => $this->_replacement,
        'onSearch=' => $this->_replacement,
        'onStalled=' => $this->_replacement,
        'onStorage=' => $this->_replacement,
        'onMessage=' => $this->_replacement,
        'onStart=' => $this->_replacement,
        'onStop=' => $this->_replacement,
        'onShow=' => $this->_replacement,
        'onSyncRestored=' => $this->_replacement,
        'onSubmit=' => $this->_replacement,
        'onSuspend=' => $this->_replacement,
        'onTimeError=' => $this->_replacement,
        'onTimeUpdate=' => $this->_replacement,
        'onTrackChange=' => $this->_replacement,
        'onToggle=' => $this->_replacement,
        'onUnload=' => $this->_replacement,
        'onURLFlip=' => $this->_replacement,
        'onVolumeChange=' => $this->_replacement,
        'onWaiting=' => $this->_replacement,
        'onWheel=' => $this->_replacement,
        'seekSegmentTime=' => $this->_replacement,
    );
  }

  /**
   * XSS Clean
   *
   * Sanitizes data so that Cross Site Scripting Hacks can be
   * prevented.  This method does a fair amount of work but
   * it is extremely thorough, designed to prevent even the
   * most obscure XSS attempts.  Nothing is ever 100% foolproof,
   * of course, but I haven't been able to get anything passed
   * the filter.
   *
   * Note: Should only be used to deal with data upon submission.
   *   It's not something that should be used for general
   *   runtime processing.
   *
   * @link  http://channel.bitflux.ch/wiki/XSS_Prevention
   *    Based in part on some code and ideas from Bitflux.
   *
   * @link  http://ha.ckers.org/xss.html
   *    To help develop this script I used this great list of
   *    vulnerabilities along with a few other hacks I've
   *    harvested from examining vulnerabilities in other programs.
   *
   * @param  string|string[] $str      Input data
   * @param  bool            $is_image Whether the input is an image
   *
   * @return  string
   */
  public function xss_clean($str, $is_image = false)
  {
    // Is the string an array?
    if (is_array($str)) {
      while (list($key) = each($str)) {
        $str[$key] = $this->xss_clean($str[$key]);
      }

      return $str;
    }

    // Remove Invisible Characters
    $str = UTF8::remove_invisible_characters($str);
    $str = UTF8::clean($str, true, true, false);

    /*
     * URL Decode
     *
     * Just in case stuff like this is submitted:
     *
     * <a href="http://%77%77%77%2E%67%6F%6F%67%6C%65%2E%63%6F%6D">Google</a>
     *
     * Note: Use rawurldecode() so it does not remove plus signs
     */
    do {
      $str = rawurldecode($str);
    }
    while (preg_match('/%[0-9a-f]{2,}/i', $str));

    /*
     * Convert character entities to ASCII
     *
     * This permits our tests below to work reliably.
     * We only convert entities that are within tags since
     * these are the ones that will pose security problems.
     */
    $str = preg_replace_callback(
        "/[^a-z0-9>]+[a-z0-9]+=([\'\"]).*?\\1/si", array(
            $this,
            '_convert_attribute'
        ), $str
    );
    $str = preg_replace_callback(
        '/<\w+.*/si', array(
            $this,
            '_decode_entity'
        ), $str
    );

    // Remove Invisible Characters Again!
    $str = UTF8::remove_invisible_characters($str);
    $str = UTF8::clean($str, true, true, false);

    /*
     * Convert all tabs to spaces
     *
     * This prevents strings like this: ja	vascript
     * NOTE: we deal with spaces between characters later.
     * NOTE: preg_replace was found to be amazingly slow here on
     * large blocks of data, so we use str_replace.
     */
    $str = str_replace("\t", ' ', $str);

    // Capture converted string for later comparison
    $converted_string = $str;

    // Remove Strings that are never allowed
    $str = $this->_do_never_allowed($str);

    /*
     * Makes PHP tags safe
     *
     * Note: XML tags are inadvertently replaced too:
     *
     * <?xml
     *
     * But it doesn't seem to pose a problem.
     */
    if ($is_image === true) {
      // Images have a tendency to have the PHP short opening and
      // closing tags every so often so we skip those and only
      // do the long opening tags.
      $str = preg_replace('/<\?(php)/i', '&lt;?\\1', $str);
    } else {
      $str = str_replace(
          array(
              '<?',
              '?' . '>'
          ), array(
              '&lt;?',
              '?&gt;'
          ), $str
      );
    }

    /*
     * Compact any exploded words
     *
     * This corrects words like:  j a v a s c r i p t
     * These words are compacted back to their correct state.
     */
    $words = array(
        'javascript',
        'expression',
        'vbscript',
        'jscript',
        'wscript',
        'vbs',
        'script',
        'base64',
        'applet',
        'alert',
        'document',
        'write',
        'cookie',
        'window',
        'confirm',
        'prompt'
    );

    foreach ($words as $word) {
      $word = implode('\s*', UTF8::str_split($word)) . '\s*';

      // We only want to do this when it is followed by a non-word character
      // That way valid stuff like "dealer to" does not become "dealerto"
      $str = preg_replace_callback(
          '#(' . UTF8::substr($word, 0, -3) . ')(\W)#is', array(
              $this,
              '_compact_exploded_words'
          ), $str
      );
    }

    /*
     * Remove disallowed Javascript in links or img tags
     * We used to do some version comparisons and use of stripos(),
     * but it is dog slow compared to these simplified non-capturing
     * preg_match(), especially if the pattern exists in the string
     *
     * Note: It was reported that not only space characters, but all in
     * the following pattern can be parsed as separators between a tag name
     * and its attributes: [\d\s"\'`;,\/\=\(\x00\x0B\x09\x0C]
     * ... however, remove_invisible_characters() above already strips the
     * hex-encoded ones, so we'll skip them below.
     */
    do {
      $original = $str;

      if (preg_match('/<a/i', $str)) {
        $str = preg_replace_callback(
            '#<a[^a-z0-9>]+([^>]*?)(?:>|$)#si', array(
                $this,
                '_js_link_removal'
            ), $str
        );
      }

      if (preg_match('/<img/i', $str)) {
        $str = preg_replace_callback(
            '#<img[^a-z0-9]+([^>]*?)(?:\s?/?>|$)#si', array(
                $this,
                '_js_img_removal'
            ), $str
        );
      }

      if (preg_match('/script|xss/i', $str)) {
        $str = preg_replace('#</*(?:script|xss).*?>#si', $this->_replacement, $str);
      }
    }
    while ($original !== $str);

    unset($original);

    // Remove evil attributes such as style, onclick and xmlns
    $str = $this->remove_evil_attributes($str, $is_image);

    /*
     * Sanitize naughty HTML elements
     *
     * If a tag containing any of the words in the list
     * below is found, the tag gets converted to entities.
     *
     * So this: <blink>
     * Becomes: &lt;blink&gt;
     */
    $naughty = 'alert|prompt|confirm|applet|audio|basefont|base|behavior|bgsound|blink|body|embed|expression|form|frameset|frame|head|html|ilayer|iframe|input|button|select|isindex|layer|link|meta|keygen|object|plaintext|style|script|textarea|title|math|video|svg|xml|xss';
    $str = preg_replace_callback(
        '#<(/*\s*)(' . $naughty . ')([^><]*)([><]*)#is', array(
            $this,
            '_sanitize_naughty_html'
        ), $str
    );

    /*
     * Sanitize naughty scripting elements
     *
     * Similar to above, only instead of looking for
     * tags it looks for PHP and JavaScript commands
     * that are disallowed. Rather than removing the
     * code, it simply converts the parenthesis to entities
     * rendering the code un-executable.
     *
     * For example:	eval('some code')
     * Becomes:	eval&#40;'some code'&#41;
     */
    $str = preg_replace(
        '#(alert|prompt|confirm|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si',
        '\\1\\2&#40;\\3&#41;',
        $str
    );

    // Final clean up

    // This adds a bit of extra precaution in case
    // something got through the above filters
    $str = $this->_do_never_allowed($str);

    // clean-up also some string, also if there is no html-tag
    $str = UTF8::str_ireplace(array_keys($this->_never_allowed_str_afterwards), $this->_never_allowed_str_afterwards, $str);

    /*
     * Images are Handled in a Special Way
     * - Essentially, we want to know that after all of the character
     * conversion is done whether any unwanted, likely XSS, code was found.
     * If not, we return TRUE, as the image is clean.
     * However, if the string post-conversion does not matched the
     * string post-removal of XSS, then it fails, as there was unwanted XSS
     * code found and removed/changed during processing.
     */
    if ($is_image === true) {
      return ($str === $converted_string);
    }

    return $str;
  }

  /**
   * Do Never Allowed
   *
   * @param  string
   *
   * @return  string
   */
  protected function _do_never_allowed($str)
  {
    $str = str_replace(array_keys($this->_never_allowed_str), $this->_never_allowed_str, $str);

    foreach ($this->_never_allowed_regex as $regex) {
      $str = preg_replace('#' . $regex . '#is', $this->_replacement, $str);
    }

    return $str;
  }

  /**
   * Remove Evil HTML Attributes (like event handlers and style)
   *
   * It removes the evil attribute and either:
   *
   *  - Everything up until a space. For example, everything between the pipes:
   *
   *  <code>
   *    <a |style=document.write('hello');alert('world');| class=link>
   *  </code>
   *
   *  - Everything inside the quotes. For example, everything between the pipes:
   *
   *  <code>
   *    <a |style="document.write('hello'); alert('world');"| class="link">
   *  </code>
   *
   * @param  string $str      The string to check
   * @param  bool   $is_image Whether the input is an image
   *
   * @return  string  The string with the evil attributes removed
   */
  public function remove_evil_attributes($str, $is_image)
  {
    // https://www.owasp.org/index.php/XSS_Filter_Evasion_Cheat_Sheet#Event_Handlers

    $evil_attributes = array(
        'on\w*',
        'style',
        'xmlns',
        'formaction',
        'form',
        'xlink:href',
        'seekSegmentTime',
        'FSCommand'
    );

    if ($is_image === true) {
      /*
       * Adobe Photoshop puts XML metadata into JFIF images,
       * including namespacing, so we have to allow this for images.
       */
      unset($evil_attributes[array_search('xmlns', $evil_attributes)]);
    }

    do {
      $count = $temp_count = 0;

      // replace occurrences of illegal attribute strings with quotes (042 and 047 are octal quotes)
      $str = preg_replace('/(<[^>]+)(?<!\w)(' . implode('|', $evil_attributes) . ')\s*=\s*(\042|\047)([^\\2]*?)(\\2)/is', '$1' . $this->_replacement, $str, -1, $temp_count);
      $count += $temp_count;

      // find occurrences of illegal attribute strings without quotes
      $str = preg_replace('/(<[^>]+)(?<!\w)(' . implode('|', $evil_attributes) . ')\s*=\s*([^\s>]*)/is', '$1' . $this->_replacement, $str, -1, $temp_count);
      $count += $temp_count;
    }
    while ($count);

    return $str;
  }

  /**
   * Compact Exploded Words
   *
   * Callback method for xss_clean() to remove whitespace from
   * things like 'j a v a s c r i p t'.
   *
   * @param  array $matches
   *
   * @return  string
   */
  protected function _compact_exploded_words($matches)
  {
    return preg_replace('/\s+/s', '', $matches[1]) . $matches[2];
  }

  /**
   * Sanitize Naughty HTML
   *
   * Callback method for xss_clean() to remove naughty HTML elements.
   *
   * @param  array $matches
   *
   * @return  string
   */
  protected function _sanitize_naughty_html($matches)
  {
    return '&lt;' . $matches[1] . $matches[2] . $matches[3] // encode opening brace
    // encode captured opening or closing brace to prevent recursive vectors:
    . str_replace(
        array(
            '>',
            '<'
        ), array(
            '&gt;',
            '&lt;'
        ), $matches[4]
    );
  }

  /**
   * JS Link Removal
   *
   * Callback method for xss_clean() to sanitize links.
   *
   * This limits the PCRE backtracks, making it more performance friendly
   * and prevents PREG_BACKTRACK_LIMIT_ERROR from being triggered in
   * PHP 5.2+ on link-heavy strings.
   *
   * @param  array $match
   *
   * @return  string
   */
  protected function _js_link_removal($match)
  {
    return str_replace(
        $match[1],
        preg_replace(
            '#href=.*?(?:(?:alert|prompt|confirm)(?:\(|&\#40;)|javascript:|livescript:|mocha:|charset=|window\.|document\.|\.cookie|<script|<xss|data\s*:)#si',
            '',
            $this->_filter_attributes(
                str_replace(
                    array(
                        '<',
                        '>'
                    ), '', $match[1]
                )
            )
        ),
        $match[0]
    );
  }

  /**
   * Filter Attributes
   *
   * Filters tag attributes for consistency and safety.
   *
   * @param  string $str
   *
   * @return  string
   */
  protected function _filter_attributes($str)
  {
    $out = '';
    if (preg_match_all('#\s*[a-z\-]+\s*=\s*(\042|\047)([^\\1]*?)\\1#is', $str, $matches)) {
      foreach ($matches[0] as $match) {
        $out .= preg_replace('#/\*.*?\*/#s', '', $match);
      }
    }

    return $out;
  }

  /**
   * JS Image Removal
   *
   * Callback method for xss_clean() to sanitize image tags.
   *
   * This limits the PCRE backtracks, making it more performance friendly
   * and prevents PREG_BACKTRACK_LIMIT_ERROR from being triggered in
   * PHP 5.2+ on image tag heavy strings.
   *
   * @param  array $match
   *
   * @return  string
   */
  protected function _js_img_removal($match)
  {
    return str_replace(
        $match[1],
        preg_replace(
            '#src=.*?(?:(?:alert|prompt|confirm)(?:\(|&\#40;)|javascript:|livescript:|mocha:|charset=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si',
            '',
            $this->_filter_attributes(
                str_replace(
                    array(
                        '<',
                        '>'
                    ), '', $match[1]
                )
            )
        ),
        $match[0]
    );
  }

  /**
   * Attribute Conversion
   *
   * @param  array $match
   *
   * @return  string
   */
  protected function _convert_attribute($match)
  {
    return str_replace(
        array(
            '>',
            '<',
            '\\'
        ), array(
            '&gt;',
            '&lt;',
            '\\\\'
        ), $match[0]
    );
  }

  /**
   * HTML Entity Decode Callback
   *
   * @param  array $match
   *
   * @return  string
   */
  protected function _decode_entity($match)
  {
    // Protect GET variables in URLs
    // 901119URL5918AMP18930PROTECT8198
    $match = preg_replace('|\&([a-z\_0-9\-]+)\=([a-z\_0-9\-/]+)|i', $this->xss_hash() . '\\1=\\2', $match[0]);

    // Decode, then un-protect URL GET vars
    return str_replace(
        $this->xss_hash(),
        '&',
        UTF8::html_entity_decode($match, null, 'UTF-8')
    );
  }

  /**
   * XSS Hash
   *
   * Generates the XSS hash if needed and returns it.
   *
   * @return  string  XSS hash
   */
  public function xss_hash()
  {
    if ($this->_xss_hash === null) {
      $rand = Bootup::get_random_bytes(16);
      $this->_xss_hash = ($rand === false) ? md5(uniqid(mt_rand(), true)) : bin2hex($rand);
    }

    return $this->_xss_hash;
  }

  /**
   * set the replacement-string for not allowed strings
   *
   * @param $string
   */
  public function setReplacement($string)
  {
    $this->_replacement = (string)$string;
  }

}
