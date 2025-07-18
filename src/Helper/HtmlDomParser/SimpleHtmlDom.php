<?php

namespace VnCoder\Helper\HtmlDomParser;

define('HDOM_TYPE_ELEMENT', 1);
define('HDOM_TYPE_COMMENT', 2);
define('HDOM_TYPE_TEXT', 3);
define('HDOM_TYPE_ENDTAG', 4);
define('HDOM_TYPE_ROOT', 5);
define('HDOM_TYPE_UNKNOWN', 6);
define('HDOM_QUOTE_DOUBLE', 0);
define('HDOM_QUOTE_SINGLE', 1);
define('HDOM_QUOTE_NO', 3);
define('HDOM_INFO_BEGIN', 0);
define('HDOM_INFO_END', 1);
define('HDOM_INFO_QUOTE', 2);
define('HDOM_INFO_SPACE', 3);
define('HDOM_INFO_TEXT', 4);
define('HDOM_INFO_INNER', 5);
define('HDOM_INFO_OUTER', 6);
define('HDOM_INFO_ENDSPACE', 7);

defined('DEFAULT_TARGET_CHARSET') || define('DEFAULT_TARGET_CHARSET', 'UTF-8');
defined('DEFAULT_BR_TEXT') || define('DEFAULT_BR_TEXT', "\r\n");
defined('DEFAULT_SPAN_TEXT') || define('DEFAULT_SPAN_TEXT', ' ');
defined('MAX_FILE_SIZE') || define('MAX_FILE_SIZE', 60000000);
define('HDOM_SMARTY_AS_TEXT', 1);

class SimpleHtmlDom
{
    public $root = null;
    public array $nodes = [];
    public $callback = null;
    public $lowercase = false;
    public $original_size;
    public $size;

    protected $pos;
    protected $doc;
    protected $char;

    protected $cursor;
    protected $parent;
    protected array $noise = [];
    protected string $token_blank = " \t\r\n";
    protected string $token_equal = ' =/>';
    protected string $token_slash = " />\r\n\t";
    protected string $token_attr = ' >';

    public string $_charset = '';
    public string $_target_charset = '';

    protected string $default_br_text = '';

    public string $default_span_text = '';

    protected array $self_closing_tags = array(
        'area' => 1,
        'base' => 1,
        'br' => 1,
        'col' => 1,
        'embed' => 1,
        'hr' => 1,
        'img' => 1,
        'input' => 1,
        'link' => 1,
        'meta' => 1,
        'param' => 1,
        'source' => 1,
        'track' => 1,
        'wbr' => 1
    );
    protected array $block_tags = array(
        'body' => 1,
        'div' => 1,
        'form' => 1,
        'root' => 1,
        'span' => 1,
        'table' => 1
    );
    protected array $optional_closing_tags = array(
        'b' => array('b' => 1),
        'dd' => array('dd' => 1, 'dt' => 1),
        'dl' => array('dd' => 1, 'dt' => 1),
        'dt' => array('dd' => 1, 'dt' => 1),
        'li' => array('li' => 1),
        'optgroup' => array('optgroup' => 1, 'option' => 1),
        'option' => array('optgroup' => 1, 'option' => 1),
        'p' => array('p' => 1),
        'rp' => array('rp' => 1, 'rt' => 1),
        'rt' => array('rp' => 1, 'rt' => 1),
        'td' => array('td' => 1, 'th' => 1),
        'th' => array('td' => 1, 'th' => 1),
        'tr' => array('td' => 1, 'th' => 1, 'tr' => 1),
    );

    function __construct($str = null, $lowercase = true, $forceTagsClosed = true, $target_charset = DEFAULT_TARGET_CHARSET, $stripRN = true, $defaultBRText = DEFAULT_BR_TEXT, $defaultSpanText = DEFAULT_SPAN_TEXT, $options = 0){
        if ($str) {
            if (preg_match('/^http:\/\//i', $str) || is_file($str)) {
                $this->load_file($str);
            } else {
                $this->load(
                    $str,
                    $lowercase,
                    $stripRN,
                    $defaultBRText,
                    $defaultSpanText,
                    $options
                );
            }
        }
        if (!$forceTagsClosed) {
            $this->optional_closing_array = array();
        }
        $this->_target_charset = $target_charset;
    }

    function __destruct()
    {
        $this->clear();
    }

    function load($str, $lowercase = true, $stripRN = true, $defaultBRText = DEFAULT_BR_TEXT, $defaultSpanText = DEFAULT_SPAN_TEXT, $options = 0)
    {
        $this->prepare($str, $lowercase, $defaultBRText, $defaultSpanText);
        $this->remove_noise("'<\s*script[^>]*[^/]>(.*?)<\s*/\s*script\s*>'is");
        $this->remove_noise("'<\s*script\s*>(.*?)<\s*/\s*script\s*>'is");
        if ($stripRN) {
            $this->doc = str_replace("\r", ' ', $this->doc);
            $this->doc = str_replace("\n", ' ', $this->doc);
            $this->size = strlen($this->doc);
        }

        $this->remove_noise("'<!\[CDATA\[(.*?)]]>'is", true);
        // strip out comments
        $this->remove_noise("'<!--(.*?)-->'is");
        // strip out <style> tags
        $this->remove_noise("'<\s*style[^>]*[^/]>(.*?)<\s*/\s*style\s*>'is");
        $this->remove_noise("'<\s*style\s*>(.*?)<\s*/\s*style\s*>'is");
        // strip out preformatted tags
        $this->remove_noise("'<\s*code[^>]*>(.*?)<\s*/\s*code\s*>'is");
        // strip out server side scripts
        $this->remove_noise("'(<\?)(.*?)(\?>)'s", true);

        if($options & HDOM_SMARTY_AS_TEXT) { // Strip Smarty scripts
            $this->remove_noise("'(\{\w)(.*?)(})'s", true);
        }

        // parsing
        $this->parse();
        // end
        $this->root->_[HDOM_INFO_END] = $this->cursor;
        $this->parse_charset();

        // make load function chainable
        return $this;
    }

    function load_file()
    {
        $args = func_get_args();
        if(($doc = call_user_func_array('file_get_contents', $args)) !== false) {
            $this->load($doc);
        }
    }

    function set_callback($function_name)
    {
        $this->callback = $function_name;
    }

    function remove_callback()
    {
        $this->callback = null;
    }

    function save($filepath = '')
    {
        $ret = $this->root->innertext();
        if ($filepath !== '') { file_put_contents($filepath, $ret, LOCK_EX); }
        return $ret;
    }

    function find($selector, $idx = null, $lowercase = false)
    {
        return $this->root->find($selector, $idx, $lowercase);
    }

    function clear()
    {
        if (isset($this->nodes)) {
            foreach ($this->nodes as $n) {
                $n->clear();
                $n = null;
            }
        }

        if (isset($this->children)) {
            foreach ($this->children as $n) {
                $n->clear();
                $n = null;
            }
        }

        if (isset($this->parent)) {
            $this->parent->clear();
            unset($this->parent);
        }

        if (isset($this->root)) {
            $this->root->clear();
            unset($this->root);
        }

        unset($this->doc);
        unset($this->noise);
    }

    function dump($show_attr = true)
    {
        $this->root->dump($show_attr);
    }

    protected function prepare(
        $str, $lowercase = true,
        $defaultBRText = DEFAULT_BR_TEXT,
        $defaultSpanText = DEFAULT_SPAN_TEXT)
    {
        $this->clear();

        $this->doc = trim($str);
        $this->size = strlen($this->doc);
        $this->original_size = $this->size; // original size of the html
        $this->pos = 0;
        $this->cursor = 1;
        $this->noise = array();
        $this->nodes = array();
        $this->lowercase = $lowercase;
        $this->default_br_text = $defaultBRText;
        $this->default_span_text = $defaultSpanText;
        $this->root = new SimpleHtmlDomNode($this);
        $this->root->tag = 'root';
        $this->root->_[HDOM_INFO_BEGIN] = -1;
        $this->root->nodetype = HDOM_TYPE_ROOT;
        $this->parent = $this->root;
        if ($this->size > 0) { $this->char = $this->doc[0]; }
    }

    protected function parse()
    {
        while (true) {
            if (($s = $this->copy_until_char('<')) === '') {
                if($this->read_tag()) {
                    continue;
                } else {
                    return true;
                }
            }

            $node = new SimpleHtmlDomNode($this);
            ++$this->cursor;
            $node->_[HDOM_INFO_TEXT] = $s;
            $this->link_nodes($node, false);
        }
    }

    protected function parse_charset()
    {
        $charset = null;
        if (empty($charset)) {
            $el = $this->root->find('meta[http-equiv=Content-Type]', 0, true);
            if (!empty($el)) {
                $fullvalue = $el->content;
                if (!empty($fullvalue)) {
                    $success = preg_match('/charset=(.+)/i', $fullvalue, $matches);
                    if ($success) {
                        $charset = $matches[1];
                    } else {
                        $charset = 'ISO-8859-1';
                    }
                }
            }
        }

        if (empty($charset)) {
            if ($meta = $this->root->find('meta[charset]', 0)) {
                $charset = $meta->charset;
            }
        }

        if (empty($charset)) {
            if (function_exists('mb_detect_encoding')) {
                $encoding = mb_detect_encoding($this->doc, array( 'UTF-8', 'CP1252', 'ISO-8859-1' ));

                if ($encoding === 'CP1252' || $encoding === 'ISO-8859-1') {
                    if (!@iconv('CP1252', 'UTF-8', $this->doc)) {
                        $encoding = 'CP1251';
                    }
                }
                if ($encoding !== false) {
                    $charset = $encoding;
                }
            }
        }

        if (empty($charset)) {
            $charset = 'UTF-8';
        }

        if ((strtolower($charset) == 'iso-8859-1') || (strtolower($charset) == 'latin1') || (strtolower($charset) == 'latin-1')) {
            $charset = 'CP1252';
        }
        return $this->_charset = $charset;
    }

    protected function read_tag()
    {
        if ($this->char !== '<') {
            $this->root->_[HDOM_INFO_END] = $this->cursor;
            return false;
        }

        $begin_tag_pos = $this->pos;
        $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next

        if ($this->char === '/') {
            $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
            $this->skip($this->token_blank);
            $tag = $this->copy_until_char('>');
            if (($pos = strpos($tag, ' ')) !== false) {
                $tag = substr($tag, 0, $pos);
            }

            $parent_lower = strtolower($this->parent->tag);
            $tag_lower = strtolower($tag);
            if ($parent_lower !== $tag_lower) {
                if (isset($this->optional_closing_tags[$parent_lower]) && isset($this->block_tags[$tag_lower])) {
                    $this->parent->_[HDOM_INFO_END] = 0;
                    $org_parent = $this->parent;
                    while (($this->parent->parent) && strtolower($this->parent->tag) !== $tag_lower){
                        $this->parent = $this->parent->parent;
                    }

                    if (strtolower($this->parent->tag) !== $tag_lower) {
                        $this->parent = $org_parent;
                        if ($this->parent->parent) {
                            $this->parent = $this->parent->parent;
                        }

                        $this->parent->_[HDOM_INFO_END] = $this->cursor;
                        return $this->as_text_node($tag);
                    }
                } elseif (($this->parent->parent) && isset($this->block_tags[$tag_lower])) {
                    $this->parent->_[HDOM_INFO_END] = 0; // No end tag
                    $org_parent = $this->parent;
                    while (($this->parent->parent) && strtolower($this->parent->tag) !== $tag_lower) {
                        $this->parent = $this->parent->parent;
                    }
                    if (strtolower($this->parent->tag) !== $tag_lower) {
                        $this->parent = $org_parent; // restore origonal parent
                        $this->parent->_[HDOM_INFO_END] = $this->cursor;
                        return $this->as_text_node($tag);
                    }
                } elseif (($this->parent->parent) && strtolower($this->parent->parent->tag) === $tag_lower) { // Grandparent exists and current tag closes it
                    $this->parent->_[HDOM_INFO_END] = 0;
                    $this->parent = $this->parent->parent;
                } else { // Random tag, add as text node
                    return $this->as_text_node($tag);
                }
            }

            // Set end position of parent tag to current cursor position
            $this->parent->_[HDOM_INFO_END] = $this->cursor;

            if ($this->parent->parent) {
                $this->parent = $this->parent->parent;
            }

            $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
            return true;
        }

        $node = new SimpleHtmlDomNode($this);
        $node->_[HDOM_INFO_BEGIN] = $this->cursor;
        ++$this->cursor;
        $tag = $this->copy_until($this->token_slash); // Get tag name
        $node->tag_start = $begin_tag_pos;
        if (isset($tag[0]) && $tag[0] === '!') {
            $node->_[HDOM_INFO_TEXT] = '<' . $tag . $this->copy_until_char('>');
            if (isset($tag[2]) && $tag[1] === '-' && $tag[2] === '-') { // Comment ("<!--")
                $node->nodetype = HDOM_TYPE_COMMENT;
                $node->tag = 'comment';
            } else { // Could be doctype or CDATA but we don't care
                $node->nodetype = HDOM_TYPE_UNKNOWN;
                $node->tag = 'unknown';
            }
            if ($this->char === '>') { $node->_[HDOM_INFO_TEXT] .= '>'; }
            $this->link_nodes($node, true);
            $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
            return true;
        }

        if ($pos = str_contains($tag, '<')) {
            $tag = '<' . substr($tag, 0, -1);
            $node->_[HDOM_INFO_TEXT] = $tag;
            $this->link_nodes($node, false);
            $this->char = $this->doc[--$this->pos]; // prev
            return true;
        }

        if (!preg_match('/^\w[\w:-]*$/', $tag)) {
            $node->_[HDOM_INFO_TEXT] = '<' . $tag . $this->copy_until('<>');
            if ($this->char === '<') {
                $this->link_nodes($node, false);
                return true;
            }
            if ($this->char === '>') { $node->_[HDOM_INFO_TEXT] .= '>'; }
            $this->link_nodes($node, false);
            $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
            return true;
        }

        // begin tag, add new node
        $node->nodetype = HDOM_TYPE_ELEMENT;
        $tag_lower = strtolower($tag);
        $node->tag = ($this->lowercase) ? $tag_lower : $tag;

        // handle optional closing tags
        if (isset($this->optional_closing_tags[$tag_lower])) {
            // Traverse ancestors to close all optional closing tags
            while (isset($this->optional_closing_tags[$tag_lower][strtolower($this->parent->tag)])) {
                $this->parent->_[HDOM_INFO_END] = 0;
                $this->parent = $this->parent->parent;
            }
            $node->parent = $this->parent;
        }

        $guard = 0; // prevent infinity loop
        $space = array($this->copy_skip($this->token_blank), '', '');
        do {
            $name = $this->copy_until($this->token_equal);
            if ($name === '' && $this->char !== null && $space[0] === '') {
                break;
            }
            if ($guard === $this->pos) { // Escape infinite loop
                $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
                continue;
            }
            $guard = $this->pos;
            if ($this->pos >= $this->size - 1 && $this->char !== '>') {
                $node->nodetype = HDOM_TYPE_TEXT;
                $node->_[HDOM_INFO_END] = 0;
                $node->_[HDOM_INFO_TEXT] = '<' . $tag . $space[0] . $name;
                $node->tag = 'text';
                $this->link_nodes($node, false);
                return true;
            }

            if ($this->doc[$this->pos - 1] == '<') {
                $node->nodetype = HDOM_TYPE_TEXT;
                $node->tag = 'text';
                $node->attr = array();
                $node->_[HDOM_INFO_END] = 0;
                $node->_[HDOM_INFO_TEXT] = substr($this->doc, $begin_tag_pos, $this->pos - $begin_tag_pos - 1);
                $this->pos -= 2;
                $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
                $this->link_nodes($node, false);
                return true;
            }

            if ($name !== '/' && $name !== '') { // this is a attribute name
                $space[1] = $this->copy_skip($this->token_blank);
                $name = $this->restore_noise($name); // might be a noisy name
                if ($this->lowercase) { $name = strtolower($name); }
                if ($this->char === '=') { // attribute with value
                    $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
                    $this->parse_attr($node, $name, $space); // get attribute value
                } else {
                    //no value attr: nowrap, checked selected...
                    $node->_[HDOM_INFO_QUOTE][] = HDOM_QUOTE_NO;
                    $node->attr[$name] = true;
                    if ($this->char != '>') { $this->char = $this->doc[--$this->pos]; } // prev
                }

                $node->_[HDOM_INFO_SPACE][] = $space;
                $space = array($this->copy_skip($this->token_blank), '', '');
            } else { // no more attributes
                break;
            }
        } while ($this->char !== '>' && $this->char !== '/'); // go until the tag ended

        $this->link_nodes($node, true);
        $node->_[HDOM_INFO_ENDSPACE] = $space[0];

        // handle empty tags (i.e. "<div/>")
        if ($this->copy_until_char('>') === '/') {
            $node->_[HDOM_INFO_ENDSPACE] .= '/';
            $node->_[HDOM_INFO_END] = 0;
        } else {
            if (!isset($this->self_closing_tags[strtolower($node->tag)])) {
                $this->parent = $node;
            }
        }

        $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
        if ($node->tag === 'br') {
            $node->_[HDOM_INFO_INNER] = $this->default_br_text;
        }

        return true;
    }

    protected function parse_attr($node, $name, &$space)
    {
        $is_duplicate = isset($node->attr[$name]);
        if (!$is_duplicate) // Copy whitespace between "=" and value
            $space[2] = $this->copy_skip($this->token_blank);

        switch ($this->char) {
            case '"':
                $quote_type = HDOM_QUOTE_DOUBLE;
                $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
                $value = $this->copy_until_char('"');
                $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
                break;
            case '\'':
                $quote_type = HDOM_QUOTE_SINGLE;
                $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
                $value = $this->copy_until_char('\'');
                $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
                break;
            default:
                $quote_type = HDOM_QUOTE_NO;
                $value = $this->copy_until($this->token_attr);
        }

        $value = $this->restore_noise($value);
        $value = str_replace("\r", '', $value);
        $value = str_replace("\n", '', $value);

        if ($name === 'class') {
            $value = trim($value);
        }

        if (!$is_duplicate) {
            $node->_[HDOM_INFO_QUOTE][] = $quote_type;
            $node->attr[$name] = $value;
        }
    }

    protected function link_nodes(&$node, $is_child)
    {
        $node->parent = $this->parent;
        $this->parent->nodes[] = $node;
        if ($is_child) {
            $this->parent->children[] = $node;
        }
    }

    protected function as_text_node($tag)
    {
        $node = new SimpleHtmlDomNode($this);
        ++$this->cursor;
        $node->_[HDOM_INFO_TEXT] = '</' . $tag . '>';
        $this->link_nodes($node, false);
        $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
        return true;
    }

    protected function skip($chars)
    {
        $this->pos += strspn($this->doc, $chars, $this->pos);
        $this->char = ($this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
    }

    protected function copy_skip($chars)
    {
        $pos = $this->pos;
        $len = strspn($this->doc, $chars, $pos);
        $this->pos += $len;
        $this->char = ($this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
        if ($len === 0) { return ''; }
        return substr($this->doc, $pos, $len);
    }

    protected function copy_until($chars)
    {
        $pos = $this->pos;
        $len = strcspn($this->doc, $chars, $pos);
        $this->pos += $len;
        $this->char = ($this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
        return substr($this->doc, $pos, $len);
    }

    protected function copy_until_char($char)
    {
        if ($this->char === null) { return ''; }

        if (($pos = strpos($this->doc, $char, $this->pos)) === false) {
            $ret = substr($this->doc, $this->pos, $this->size - $this->pos);
            $this->char = null;
            $this->pos = $this->size;
            return $ret;
        }

        if ($pos === $this->pos) { return ''; }

        $pos_old = $this->pos;
        $this->char = $this->doc[$pos];
        $this->pos = $pos;
        return substr($this->doc, $pos_old, $pos - $pos_old);
    }

    protected function remove_noise($pattern, $remove_tag = false)
    {
        $count = preg_match_all($pattern, $this->doc, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        for ($i = $count - 1; $i > -1; --$i) {
            $key = '___noise___' . sprintf('% 5d', count($this->noise) + 1000);
            $idx = ($remove_tag) ? 0 : 1; // 0 = entire match, 1 = submatch
            $this->noise[$key] = $matches[$i][$idx][0];
            $this->doc = substr_replace($this->doc, $key, $matches[$i][$idx][1], strlen($matches[$i][$idx][0]));
        }
        $this->size = strlen($this->doc);

        if ($this->size > 0) {
            $this->char = $this->doc[0];
        }
    }

    function restore_noise($text)
    {
        while (($pos = strpos($text, '___noise___')) !== false) {

            if (strlen($text) > $pos + 15) {
                $key = '___noise___' . $text[$pos + 11] . $text[$pos + 12] . $text[$pos + 13] . $text[$pos + 14] . $text[$pos + 15];
                if (isset($this->noise[$key])) {
                    $text = substr($text, 0, $pos) . $this->noise[$key] . substr($text, $pos + 16);
                } else {
                    $text = substr($text, 0, $pos) . 'UNDEFINED NOISE FOR KEY: ' . $key . substr($text, $pos + 16);
                }
            } else {
                $text = substr($text, 0, $pos) . 'NO NUMERIC NOISE KEY' . substr($text, $pos + 11);
            }
        }
        return $text;
    }

    function search_noise($text)
    {
        foreach($this->noise as $noiseElement) {
            if (str_contains($noiseElement, $text)) {
                return $noiseElement;
            }
        }
        return null;
    }

    function __toString()
    {
        return $this->root->innertext();
    }

    function __get($name)
    {
        switch ($name) {
            case 'innertext':
            case 'outertext':
                return $this->root->innertext();
            case 'plaintext':
                return $this->root->text();
            case 'charset':
                return $this->_charset;
            case 'target_charset':
                return $this->_target_charset;
        }
        return null;
    }

    function childNodes($idx = -1)
    {
        return $this->root->childNodes($idx);
    }

    function firstChild()
    {
        return $this->root->first_child();
    }

    function lastChild()
    {
        return $this->root->last_child();
    }

    function createElement($name, $value = null)
    {
        return $this->str_get_html("<$name>$value</$name>")->firstChild();
    }

    function createTextNode($value)
    {
        return @end($this->str_get_html($value)->nodes);
    }

    function getElementById($id)
    {
        return $this->find("#$id", 0);
    }

    function getElementsById($id, $idx = null)
    {
        return $this->find("#$id", $idx);
    }

    function getElementByTagName($name)
    {
        return $this->find($name, 0);
    }

    function getElementsByTagName($name, $idx = -1)
    {
        return $this->find($name, $idx);
    }

    function loadFile()
    {
        $args = func_get_args();
        $this->load_file($args);
    }

    function str_get_html($str, $lowercase = true, $forceTagsClosed = true, $target_charset = DEFAULT_TARGET_CHARSET, $stripRN = true, $defaultBRText = DEFAULT_BR_TEXT, $defaultSpanText = DEFAULT_SPAN_TEXT){
        $dom = new SimpleHtmlDom(null, $lowercase, $forceTagsClosed, $target_charset, $stripRN, $defaultBRText, $defaultSpanText);

        if (empty($str) || strlen($str) > MAX_FILE_SIZE) {
            $dom->clear();
            return false;
        }

        return $dom->load($str, $lowercase, $stripRN);
    }
}
