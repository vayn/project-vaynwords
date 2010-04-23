<?php

function definition($value_1) { # 从 dict.cn 获得单词音标、解释、中英文例句
  $xml = simplexml_load_file('http://dict.cn/ws.php?utf8=true&q=' . $value_1);

  $arr['key'] = $xml->key;
  $arr['pron'] = $xml->pron;
  $arr['def'] = $xml->def;
  $arr['sent_o'] = $xml->sent->orig;
  $arr['sent_t'] = $xml->sent->trans;

  return $arr;
}

function audio($value_2) { # 从 Google Dictinary 获得单词发音
  $piece_1 = substr($value_2, 0, 1);
  $piece_2 = substr($value_2, 0, 2);
  $piece_3 = substr($value_2, 0, 3);
  $audio .= '<object data="http://www.google.com/dictionary/flash/SpeakerApp16.swf" type="application/x-shockwave-flash" id="pronunciation" height="16" width=" 16">';
  $audio .= '<param name="movie" value="http://www.google.com/dictionary/flash/SpeakerApp16.swf">';
  $audio .= '<param name="flashvars" value="sound_name=http%3A%2F%2Fwww.gstatic.com%2Fdictionary%2Fstatic%2Fsounds%2Flf%2F0%2F'
    . $piece_1 . '%2F' . $piece_2 .  '%2F' . $piece_3 . '%2F' . $value_2 . '%2523_gb_1.mp3">';
  $audio .= '<param name="wmode" value="transparent">';
  $audio .= '<a href="http://www.gstatic.com/dictionary/static/sounds/lf/0/'
    . $piece_1 . '/' . $piece_2 . '/' . $piece_3 .'/'
    . $value_2 . '%23_gb_1.mp3"><img src="http://www.google.com/dictionary/flash/SpeakerOffA16.png" alt="listen" height="16" width="16" border="0"></a>';
  $audio .= '</object>';

  return $audio;
}

?>
