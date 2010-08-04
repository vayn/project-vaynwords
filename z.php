<?php

$w = 'indices';
$json = file_get_contents("http://www.google.com/dictionary/json?callback=dict_api.callbacks.id100&q={$w}&sl=en&tl=zh&restrict=pr%2Cde&client=te");
$json = substr($json, strpos($json, "(")+1, -10);
$json = str_replace("\\", "\\\\", $json);
$decode = json_decode($json, true);

print_r($decode);

?>
