<?php
/**
 * Author:
 *    Vayn a.k.a. VT <vt@elnode.com>
 *    http://elnode.com
 *
 *    File:             vws_functions.php
 *    Create Date:      2010年 05月 13日 星期四 18:43:21 CST
 */
// 从 dict.cn 获得单词音标、解释、中英文例句
function dict_query($value_1) {
  $xml = simplexml_load_file('http://api.dict.cn/ws.php?utf8=true&q=' . $value_1);
  if (!($xml->def == 'Not Found')) {
    $arr['key'] = $xml->key;
    $arr['pron'] = $xml->pron;
    $arr['def'] = $xml->def;
    $arr['sent_o'] = $xml->sent->orig;
    $arr['sent_t'] = $xml->sent->trans;

    return $arr;
  }
  else {
    return FALSE;
  }
}

// 从 Google Dictinary 获得单词发音
function audio($value_2) {
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

function generate_content($page = 1) {
  global $vw_perpage;

  $xml = simplexml_load_file('vws_data.xml');
  $i = 0;
  $arr = array();
 
  // Generate Words table array
  foreach ($xml->word as $xml) {
    $id = $xml->attributes();
    $key = $xml->key;
    $pron = $xml->defs->pron;
    $def = $xml->defs->def;
    $sent_o = $xml->defs->sent->orig;
    $sent_t = $xml->defs->sent->trans;

    $arr[$i] = '<table class="word_fleet" cellspacing="2">';
    $arr[$i] .= '<tr>';
    $arr[$i] .= '<td class="word_box_s">';
    $arr[$i] .= $key . '<span id="' . $id . '"></span>';
    $arr[$i] .= '</td>';
    $arr[$i] .= '<td class="word_box_s">/' . $pron . '/ ' . audio($key) . '</td>';
    $arr[$i] .= '<td class="word_box_s">' . $def . '</td>';
    $arr[$i] .= '</tr>';

    if ($sent_o != '' || $sent_t != '') {
      $arr[$i] .= '<tr><td class="word_box_l" colspan=3>' . $sent_o . '</td></tr>';
      $arr[$i] .= '<tr><td class="word_box_l" colspan=3>' . $sent_t;
      if (($i%5 == 0) && ($i != 0)) {
        $arr[$i] .= '<a href="#top" title="Back to top"><div class="back">&uarr;<div></a>';
      }
      $arr[$i] .= '</td></tr>';
    }

    $arr[$i] .= '</table>';
    $i++;
  }

  // Pagination
  $count = count($arr);
  $pages = ceil($count / $vw_perpage);

  if (isset($_GET['page'])) {
    if (($_GET['page'] > 0) && ($_GET['page'] < $pages+1)) {
      $page = (int) $_GET['page'];
    }
    elseif ($_GET['page'] > $pages) {
      header('Location: ?page=' . $pages);
      exit;
    }
    else {
      header('Location: ?page=1');
      exit;
    }
  }

  $start = ceil(($page - 1) * $vw_perpage);

  $arr = array_slice($arr, $start, $vw_perpage);

  if ($pages > 1) {
    // Assign the previous page
    if ($page != 1) {
      $plink = '<a href="?page=' . ($page - 1) . '">&laquo; Prev</a>';

      if ($page != $pages) {
        $nlink = '<a href="?page=' . ($page + 1) . '">Next &raquo;</a>';
      }
    }
    else {
      $nlink = '<a href="?page=' . ($page + 1) . '">Next &raquo;</a>';
    }

    // Assign all the page numbers and links to the array
    for ($l = 1; $l < $pages+1; $l++) {
      if ($page == $l) {
        $link .= ' <span class="current">' . $l . '</span> '; // If we are on the current page
      }
      else {
        $link .= ' <a href="?page=' . $l . '" class="page">' . $l . '</a> ';
      }
    }
  }

  $arr[] = '<div id="pagination">' . $plink . $link . $nlink . '</div>';

  foreach ($arr as $key) {
    echo $key;
  }

}

?>

