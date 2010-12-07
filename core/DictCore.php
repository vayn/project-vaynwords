<?php

if (!defined('IN_VWS')) {
    exit('Access Denied.');
}

class query_Word {
    public $word;

    public function __construct($word) {
        $this->word = $word;
    }

    public function query($dict) {
        return $dict->query($this->word);
    }
}

interface query_Dict {
    public function query($word);
}

class query_qqDict implements query_Dict {
    public function query($word) {
        $word = urlencode($word);
        $json = @file_get_contents("http://dict.qq.com/dict?q={$word}");
        $decode = json_decode($json, true);
        return $decode;
    }
}

?>
