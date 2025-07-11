<?php

namespace VnCoder\Helper\HtmlDomParser;

class SimpleHtmlDomNode
{
    public $nodetype = HDOM_TYPE_TEXT;
    public $tag = 'text';
    public $attr = array();
    public $children = array();
    public $nodes = array();
    public $parent = null;
    public $_ = array();
    public $tag_start = 0;
    private $dom = null;

    function __construct($dom)
    {
        $this->dom = $dom;
        $dom->nodes[] = $this;
    }

    function __destruct()
    {
        $this->clear();
    }

    function __toString()
    {
        return $this->outertext();
    }

    function clear()
    {
        $this->dom = null;
        $this->nodes = null;
        $this->parent = null;
        $this->children = null;
    }

    function dump($show_attr = true, $depth = 0)
    {
        echo str_repeat("\t", $depth) . $this->tag;
        if ($show_attr && count($this->attr) > 0) {
            echo '(';
            foreach ($this->attr as $k => $v) {
                echo "[$k]=>\"$v\", ";
            }
            echo ')';
        }
        echo "\n";
        if ($this->nodes) {
            foreach ($this->nodes as $node) {
                $node->dump($show_attr, $depth + 1);
            }
        }
    }

    function dump_node($echo = true)
    {
        $string = $this->tag;

        if (count($this->attr) > 0) {
            $string .= '(';
            foreach ($this->attr as $k => $v) {
                $string .= "[$k]=>\"$v\", ";
            }
            $string .= ')';
        }

        if (count($this->_) > 0) {
            $string .= ' $_ (';
            foreach ($this->_ as $k => $v) {
                if (is_array($v)) {
                    $string .= "[$k]=>(";
                    foreach ($v as $k2 => $v2) {
                        $string .= "[$k2]=>\"$v2\", ";
                    }
                    $string .= ')';
                } else {
                    $string .= "[$k]=>\"$v\", ";
                }
            }
            $string .= ')';
        }

        if (isset($this->text)) {
            $string .= " text: ({$this->text})";
        }

        $string .= ' HDOM_INNER_INFO: ';

        if (isset($node->_[HDOM_INFO_INNER])) {
            $string .= "'" . $node->_[HDOM_INFO_INNER] . "'";
        } else {
            $string .= ' NULL ';
        }

        $string .= ' children: ' . count($this->children);
        $string .= ' nodes: ' . count($this->nodes);
        $string .= ' tag_start: ' . $this->tag_start;
        $string .= "\n";

        if ($echo) {
            echo $string;
        } else {
            return $string;
        }
    }

    function parent($parent = null)
    {
        if ($parent !== null) {
            $this->parent = $parent;
            $this->parent->nodes[] = $this;
            $this->parent->children[] = $this;
        }

        return $this->parent;
    }

    function has_child()
    {
        return !empty($this->children);
    }

    function children($idx = -1)
    {
        if ($idx === -1) {
            return $this->children;
        }

        if (isset($this->children[$idx])) {
            return $this->children[$idx];
        }

        return null;
    }

    function first_child()
    {
        if (count($this->children) > 0) {
            return $this->children[0];
        }
        return null;
    }

    function last_child()
    {
        if (count($this->children) > 0) {
            return end($this->children);
        }
        return null;
    }

    function next_sibling()
    {
        if ($this->parent === null) {
            return null;
        }

        $idx = array_search($this, $this->parent->children, true);

        if ($idx !== false && isset($this->parent->children[$idx + 1])) {
            return $this->parent->children[$idx + 1];
        }

        return null;
    }

    function prev_sibling()
    {
        if ($this->parent === null) {
            return null;
        }

        $idx = array_search($this, $this->parent->children, true);

        if ($idx !== false && $idx > 0) {
            return $this->parent->children[$idx - 1];
        }

        return null;
    }

    function find_ancestor_tag($tag)
    {
        if ($this->parent === null) {
            return null;
        }

        $ancestor = $this->parent;

        while (!is_null($ancestor)) {
            if ($ancestor->tag === $tag) {
                break;
            }

            $ancestor = $ancestor->parent;
        }

        return $ancestor;
    }

    function innertext()
    {
        if (isset($this->_[HDOM_INFO_INNER])) {
            return $this->_[HDOM_INFO_INNER];
        }

        if (isset($this->_[HDOM_INFO_TEXT])) {
            return $this->dom->restore_noise($this->_[HDOM_INFO_TEXT]);
        }

        $ret = '';

        foreach ($this->nodes as $n) {
            $ret .= $n->outertext();
        }

        return trim($ret);
    }

    function outertext()
    {
        if ($this->tag === 'root') {
            return $this->innertext();
        }
        if ($this->dom && $this->dom->callback !== null) {
            call_user_func_array($this->dom->callback, array($this));
        }

        if (isset($this->_[HDOM_INFO_OUTER])) {
            return $this->_[HDOM_INFO_OUTER];
        }

        if (isset($this->_[HDOM_INFO_TEXT])) {
            return $this->dom->restore_noise($this->_[HDOM_INFO_TEXT]);
        }

        $ret = '';
        if ($this->dom && $this->dom->nodes[$this->_[HDOM_INFO_BEGIN]]) {
            $ret = $this->dom->nodes[$this->_[HDOM_INFO_BEGIN]]->makeup();
        }

        if (isset($this->_[HDOM_INFO_INNER])) {
            if ($this->tag !== 'br') {
                $ret .= $this->_[HDOM_INFO_INNER];
            }
        } elseif ($this->nodes) {
            foreach ($this->nodes as $n) {
                $ret .= $this->convert_text($n->outertext());
            }
        }

        if (isset($this->_[HDOM_INFO_END]) && $this->_[HDOM_INFO_END] != 0) {
            $ret .= '</' . $this->tag . '>';
        }

        return trim($ret);
    }

    function text()
    {
        if (isset($this->_[HDOM_INFO_INNER])) {
            return $this->_[HDOM_INFO_INNER];
        }

        switch ($this->nodetype) {
            case HDOM_TYPE_TEXT: return $this->dom->restore_noise($this->_[HDOM_INFO_TEXT]);
            case HDOM_TYPE_UNKNOWN:
            case HDOM_TYPE_COMMENT: return '';
        }

        if (strcasecmp($this->tag, 'script') === 0) { return ''; }
        if (strcasecmp($this->tag, 'style') === 0) { return ''; }

        $ret = '';
        if (!is_null($this->nodes)) {
            foreach ($this->nodes as $n) {
                if ($n->tag === 'p') {
                    $ret = trim($ret) . "\n\n";
                }
                $ret .= $this->convert_text($n->text());
                if ($n->tag === 'span') {
                    $ret .= $this->dom->default_span_text;
                }
            }
        }
        return trim($ret);
    }

    function xmltext()
    {
        $ret = $this->innertext();
        $ret = str_ireplace('<![CDATA[', '', $ret);
        $ret = str_replace(']]>', '', $ret);
        return trim($ret);
    }

    function makeup()
    {
        // text, comment, unknown
        if (isset($this->_[HDOM_INFO_TEXT])) {
            return $this->dom->restore_noise($this->_[HDOM_INFO_TEXT]);
        }

        $ret = '<' . $this->tag;
        $i = -1;

        foreach ($this->attr as $key => $val) {
            ++$i;
            if ($val === null || $val === false) { continue; }
            $ret .= $this->_[HDOM_INFO_SPACE][$i][0];
            if ($val === true) {
                $ret .= $key;
            } else {
                switch ($this->_[HDOM_INFO_QUOTE][$i])
                {
                    case HDOM_QUOTE_DOUBLE: $quote = '"'; break;
                    case HDOM_QUOTE_SINGLE: $quote = '\''; break;
                    default: $quote = '';
                }

                $ret .= $key
                    . $this->_[HDOM_INFO_SPACE][$i][1]
                    . '='
                    . $this->_[HDOM_INFO_SPACE][$i][2]
                    . $quote
                    . $val
                    . $quote;
            }
        }

        $ret = $this->dom->restore_noise($ret);
        return $ret . $this->_[HDOM_INFO_ENDSPACE] . '>';
    }

    function find($selector, $idx = null, $lowercase = false)
    {
        $selectors = $this->parse_selector($selector);
        if (($count = count($selectors)) === 0) { return array(); }
        $found_keys = array();
        for ($c = 0; $c < $count; ++$c) {
            // used to be: if (($levle=count($selectors[0]))===0) return array();
            if (($levle = count($selectors[$c])) === 0) { return array(); }
            if (!isset($this->_[HDOM_INFO_BEGIN])) { return array(); }
            $head = array($this->_[HDOM_INFO_BEGIN] => 1);
            $cmd = ' '; // Combinator
            for ($l = 0; $l < $levle; ++$l) {
                $ret = array();
                foreach ($head as $k => $v) {
                    $n = ($k === -1) ? $this->dom->root : $this->dom->nodes[$k];
                    $n->seek($selectors[$c][$l], $ret, $cmd, $lowercase);
                }
                $head = $ret;
                $cmd = $selectors[$c][$l][4]; // Next Combinator
            }

            foreach ($head as $k => $v) {
                if (!isset($found_keys[$k])) {
                    $found_keys[$k] = 1;
                }
            }
        }
        ksort($found_keys);
        $found = array();
        foreach ($found_keys as $k => $v) {
            $found[] = $this->dom->nodes[$k];
        }
        if (is_null($idx)) { return $found; }
        elseif ($idx < 0) { $idx = count($found) + $idx; }
        return (isset($found[$idx])) ? $found[$idx] : null;
    }

    protected function seek($selector, &$ret, $parent_cmd, $lowercase = false)
    {
        list($tag, $id, $class, $attributes, $cmb) = $selector;
        $nodes = array();

        if ($parent_cmd === ' ') {
            $end = (!empty($this->_[HDOM_INFO_END])) ? $this->_[HDOM_INFO_END] : 0;
            if ($end == 0) {
                $parent = $this->parent;
                while (!isset($parent->_[HDOM_INFO_END]) && $parent !== null) {
                    $end -= 1;
                    $parent = $parent->parent;
                }
                $end += $parent->_[HDOM_INFO_END];
            }
            $nodes_start = $this->_[HDOM_INFO_BEGIN] + 1;
            $nodes_count = $end - $nodes_start;
            $nodes = array_slice($this->dom->nodes, $nodes_start, $nodes_count, true);
        } elseif ($parent_cmd === '>') {
            $nodes = $this->children;
        } elseif ($parent_cmd === '+' && $this->parent && in_array($this, $this->parent->children)) {
            $index = array_search($this, $this->parent->children, true) + 1;
            if ($index < count($this->parent->children))
                $nodes[] = $this->parent->children[$index];
        } elseif ($parent_cmd === '~' && $this->parent && in_array($this, $this->parent->children)) { // Subsequent Sibling Combinator
            $index = array_search($this, $this->parent->children, true);
            $nodes = array_slice($this->parent->children, $index);
        }

        foreach($nodes as $node) {
            $pass = true;
            if(!$node->parent) {
                $pass = false;
            }
            if($pass && $tag === 'text' && $node->tag === 'text') {
                $ret[array_search($node, $this->dom->nodes, true)] = 1;
                unset($node);
                continue;
            }
            if($pass && !in_array($node, $node->parent->children, true)) {
                $pass = false;
            }
            if ($pass && $tag !== '' && $tag !== $node->tag && $tag !== '*') {
                $pass = false;
            }
            if ($pass && $id !== '' && !isset($node->attr['id'])) {
                $pass = false;
            }
            if ($pass && $id !== '' && isset($node->attr['id'])) {
                $node_id = explode(' ', trim($node->attr['id']))[0];
                if($id !== $node_id) { $pass = false; }
            }
            if ($pass && $class !== '' && is_array($class) && !empty($class)) {
                if (isset($node->attr['class'])) {
                    $node_classes = explode(' ', $node->attr['class']);
                    if ($lowercase) {
                        $node_classes = array_map('strtolower', $node_classes);
                    }
                    foreach($class as $c) {
                        if(!in_array($c, $node_classes)) {
                            $pass = false;
                            break;
                        }
                    }
                } else {
                    $pass = false;
                }
            }

            if ($pass && $attributes !== '' && is_array($attributes) && !empty($attributes)) {
                foreach($attributes as $a) {
                    list ($att_name, $att_expr, $att_val, $att_inv, $att_case_sensitivity) = $a;
                    if (is_numeric($att_name) && $att_expr === '' && $att_val === '') {
                        $count = 0;
                        foreach ($node->parent->children as $c) {
                            if ($c->tag === $node->tag) ++$count;
                            if ($c === $node) break;
                        }
                        if ($count === (int)$att_name) continue;
                    }
                    if ($att_inv) { // Attribute should NOT be set
                        if (isset($node->attr[$att_name])) {
                            $pass = false;
                            break;
                        }
                    } else {
                        if ($att_name !== 'plaintext' && !isset($node->attr[$att_name])) {
                            $pass = false;
                            break;
                        }
                    }
                    if ($att_expr === '') continue;
                    if ($att_name === 'plaintext') {
                        $nodeKeyValue = $node->text();
                    } else {
                        $nodeKeyValue = $node->attr[$att_name];
                    }

                    if ($lowercase) {
                        $check = $this->match(
                            $att_expr,
                            strtolower($att_val),
                            strtolower($nodeKeyValue),
                            $att_case_sensitivity
                        );
                    } else {
                        $check = $this->match(
                            $att_expr,
                            $att_val,
                            $nodeKeyValue,
                            $att_case_sensitivity
                        );
                    }

                    if (!$check) {
                        $pass = false;
                        break;
                    }
                }
            }

            if ($pass) $ret[$node->_[HDOM_INFO_BEGIN]] = 1;
            unset($node);
        }
    }

    protected function match($exp, $pattern, $value, $case_sensitivity)
    {
        if ($case_sensitivity === 'i') {
            $pattern = strtolower($pattern);
            $value = strtolower($value);
        }

        switch ($exp) {
            case '=':
                return ($value === $pattern);
            case '!=':
                return ($value !== $pattern);
            case '^=':
                return preg_match('/^' . preg_quote($pattern, '/') . '/', $value);
            case '$=':
                return preg_match('/' . preg_quote($pattern, '/') . '$/', $value);
            case '*=':
                return preg_match('/' . preg_quote($pattern, '/') . '/', $value);
            case '|=':
                return strpos($value, $pattern) === 0;
            case '~=':
                return in_array($pattern, explode(' ', trim($value)), true);
        }
        return false;
    }

    protected function parse_selector($selector_string)
    {
        $pattern = "/([\w:*-]*)(?:#([\w-]+))?(?:|\.([\w.-]+))?((?:\[@?!?[\w:-]+(?:[!*^$|~]?=[\"']?.*?[\"']?)?(?:\s*?[iIsS]?)?])+)?([\/, >+~]+)/is";
        preg_match_all($pattern, trim($selector_string) . ' ', $matches, PREG_SET_ORDER);
        $selectors = array();
        $result = array();

        foreach ($matches as $m) {
            $m[0] = trim($m[0]);
            if ($m[0] === '' || $m[0] === '/' || $m[0] === '//') { continue; }
            if ($this->dom->lowercase) {
                $m[1] = strtolower($m[1]);
            }
            if ($m[3] !== '') { $m[3] = explode('.', $m[3]); }
            if($m[4] !== '') {
                preg_match_all("/\[@?(!?[\w:-]+)(?:([!*^$|~]?=)[\"']?(.*?)[\"']?)?(?:\s+?([iIsS])?)?]/is", trim($m[4]), $attributes, PREG_SET_ORDER);
                $m[4] = array();
                foreach($attributes as $att) {
                    if(trim($att[0]) === '') { continue; }
                    $inverted = (isset($att[1][0]) && $att[1][0] === '!');
                    $m[4][] = array(
                        $inverted ? substr($att[1], 1) : $att[1], // Name
                        (isset($att[2])) ? $att[2] : '', // Expression
                        (isset($att[3])) ? $att[3] : '', // Value
                        $inverted, // Inverted Flag
                        (isset($att[4])) ? strtolower($att[4]) : '', // Case-Sensitivity
                    );
                }
            }

            if ($m[5] !== '' && trim($m[5]) === '') { // Descendant Separator
                $m[5] = ' ';
            } else { // Other Separator
                $m[5] = trim($m[5]);
            }

            // Clear Separator if it's a Selector List
            if ($is_list = ($m[5] === ',')) { $m[5] = ''; }

            // Remove full match before adding to results
            array_shift($m);
            $result[] = $m;

            if ($is_list) { // Selector List
                $selectors[] = $result;
                $result = array();
            }
        }

        if (count($result) > 0) { $selectors[] = $result; }
        return $selectors;
    }

    function __get($name)
    {
        if (isset($this->attr[$name])) {
            return $this->convert_text($this->attr[$name]);
        }
        switch ($name) {
            case 'outertext': return $this->outertext();
            case 'innertext': return $this->innertext();
            case 'plaintext': return $this->text();
            case 'xmltext': return $this->xmltext();
            default: return array_key_exists($name, $this->attr);
        }
    }

    function __set($name, $value)
    {
        switch ($name) {
            case 'outertext': return $this->_[HDOM_INFO_OUTER] = $value;
            case 'innertext':
                if (isset($this->_[HDOM_INFO_TEXT])) {
                    return $this->_[HDOM_INFO_TEXT] = $value;
                }
                return $this->_[HDOM_INFO_INNER] = $value;
        }

        if (!isset($this->attr[$name])) {
            $this->_[HDOM_INFO_SPACE][] = array(' ', '', '');
            $this->_[HDOM_INFO_QUOTE][] = HDOM_QUOTE_DOUBLE;
        }

        $this->attr[$name] = $value;
    }

    function __isset($name)
    {
        switch ($name) {
            case 'plaintext':
            case 'innertext':
            case 'outertext': return true;
        }
        //no value attr: nowrap, checked selected...
        return array_key_exists($name, $this->attr) || isset($this->attr[$name]);
    }

    function __unset($name)
    {
        if (isset($this->attr[$name])) { unset($this->attr[$name]); }
    }

    function convert_text($text)
    {
        $converted_text = $text;
        $sourceCharset = '';
        $targetCharset = '';

        if ($this->dom) {
            $sourceCharset = strtoupper($this->dom->_charset);
            $targetCharset = strtoupper($this->dom->_target_charset);
        }

        if (!empty($sourceCharset) && !empty($targetCharset) && (strcasecmp($sourceCharset, $targetCharset) != 0)) {
            if ( !$this->is_utf8($text) ||  strcasecmp($targetCharset, 'UTF-8') == 0) {
                $converted_text = iconv($sourceCharset, $targetCharset, $text);
            }
        }

        // Lets make sure that we don't have that silly BOM issue with any of the utf-8 text we output.
        if ($targetCharset === 'UTF-8') {
            if (substr($converted_text, 0, 3) === "\xef\xbb\xbf") {
                $converted_text = substr($converted_text, 3);
            }

            if (substr($converted_text, -3) === "\xef\xbb\xbf") {
                $converted_text = substr($converted_text, 0, -3);
            }
        }

        return $converted_text;
    }

    static function is_utf8($str)
    {
        $c = 0; $b = 0;
        $bits = 0;
        $len = strlen($str);
        for($i = 0; $i < $len; $i++) {
            $c = ord($str[$i]);
            if($c > 128) {
                if(($c >= 254)) { return false; }
                elseif($c >= 252) { $bits = 6; }
                elseif($c >= 248) { $bits = 5; }
                elseif($c >= 240) { $bits = 4; }
                elseif($c >= 224) { $bits = 3; }
                elseif($c >= 192) { $bits = 2; }
                else { return false; }
                if(($i + $bits) > $len) { return false; }
                while($bits > 1) {
                    $i++;
                    $b = ord($str[$i]);
                    if($b < 128 || $b > 191) { return false; }
                    $bits--;
                }
            }
        }
        return true;
    }

    function get_display_size()
    {
        $width = -1;
        $height = -1;

        if ($this->tag !== 'img') {
            return false;
        }
        if (isset($this->attr['width'])) {
            $width = $this->attr['width'];
        }

        if (isset($this->attr['height'])) {
            $height = $this->attr['height'];
        }

        // Now look for an inline style.
        if (isset($this->attr['style'])) {
            // Thanks to user gnarf from stackoverflow for this regular expression.
            $attributes = array();

            preg_match_all('/([\w-]+)\s*:\s*([^;]+)\s*;?/', $this->attr['style'], $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $attributes[$match[1]] = $match[2];
            }

            if (isset($attributes['width']) && $width == -1) {
                if (strtolower(substr($attributes['width'], -2)) === 'px') {
                    $proposed_width = substr($attributes['width'], 0, -2);
                    if (filter_var($proposed_width, FILTER_VALIDATE_INT)) {
                        $width = $proposed_width;
                    }
                }
            }

            if (isset($attributes['height']) && $height == -1) {
                if (strtolower(substr($attributes['height'], -2)) == 'px') {
                    $proposed_height = substr($attributes['height'], 0, -2);
                    if (filter_var($proposed_height, FILTER_VALIDATE_INT)) {
                        $height = $proposed_height;
                    }
                }
            }

        }

        return array(
            'height' => $height,
            'width' => $width
        );
    }

    function save($filepath = '')
    {
        $ret = $this->outertext();

        if ($filepath !== '') {
            file_put_contents($filepath, $ret, LOCK_EX);
        }

        return $ret;
    }

    function addClass($class)
    {
        if (is_string($class)) {
            $class = explode(' ', $class);
        }

        if (is_array($class)) {
            foreach($class as $c) {
                if (isset($this->class)) {
                    if ($this->hasClass($c)) {
                        continue;
                    } else {
                        $this->class .= ' ' . $c;
                    }
                } else {
                    $this->class = $c;
                }
            }
        }
    }

    function hasClass($class)
    {
        if (is_string($class)) {
            if (isset($this->class)) {
                return in_array($class, explode(' ', $this->class), true);
            }
        }
        return false;
    }

    function removeClass($class = null)
    {
        if (!isset($this->class)) {
            return;
        }

        if (is_null($class)) {
            $this->removeAttribute('class');
            return;
        }

        if (is_string($class)) {
            $class = explode(' ', $class);
        }

        if (is_array($class)) {
            $class = array_diff(explode(' ', $this->class), $class);
            if (empty($class)) {
                $this->removeAttribute('class');
            } else {
                $this->class = implode(' ', $class);
            }
        }
    }

    function getAllAttributes()
    {
        return $this->attr;
    }

    function getAttribute($name)
    {
        return $this->__get($name);
    }

    function setAttribute($name, $value)
    {
        $this->__set($name, $value);
    }

    function hasAttribute($name)
    {
        return $this->__isset($name);
    }

    function removeAttribute($name)
    {
        $this->__set($name, null);
    }

    function remove()
    {
        if ($this->parent) {
            $this->parent->removeChild($this);
        }
    }

    function removeChild($node)
    {
        $nidx = array_search($node, $this->nodes, true);
        $cidx = array_search($node, $this->children, true);
        $didx = array_search($node, $this->dom->nodes, true);

        if ($nidx !== false && $cidx !== false && $didx !== false) {

            foreach($node->children as $child) {
                $node->removeChild($child);
            }

            foreach($node->nodes as $entity) {
                $enidx = array_search($entity, $node->nodes, true);
                $edidx = array_search($entity, $node->dom->nodes, true);

                if ($enidx !== false && $edidx !== false) {
                    unset($node->nodes[$enidx]);
                    unset($node->dom->nodes[$edidx]);
                }
            }

            unset($this->nodes[$nidx]);
            unset($this->children[$cidx]);
            unset($this->dom->nodes[$didx]);

            $node->clear();

        }
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

    function getElementsByTagName($name, $idx = null)
    {
        return $this->find($name, $idx);
    }

    function parentNode()
    {
        return $this->parent();
    }

    function childNodes($idx = -1)
    {
        return $this->children($idx);
    }

    function firstChild()
    {
        return $this->first_child();
    }

    function lastChild()
    {
        return $this->last_child();
    }

    function nextSibling()
    {
        return $this->next_sibling();
    }

    function previousSibling()
    {
        return $this->prev_sibling();
    }

    function hasChildNodes()
    {
        return $this->has_child();
    }

    function nodeName()
    {
        return $this->tag;
    }

    function appendChild($node)
    {
        $node->parent($this);
        return $node;
    }

}
