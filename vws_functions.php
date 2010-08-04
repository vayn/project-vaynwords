<?php
/**
 * Author:
 *    Vayn a.k.a. VT <vt@elnode.com>
 *    http://elnode.com
 *
 *    File:             vws_functions.php
 *    Create Date:      2010年 05月 13日 星期四 18:43:21 CST
 */

//
// 从 GDict 获得单词数据
//
function query_def($w) {
    $json = file_get_contents("http://www.google.com/dictionary/json?callback=dict_api.callbacks.id100&q={$w}&sl=en&tl=zh&restrict=pr%2Cde&client=te");
    $json = substr($json, strpos($json, "(")+1, -10);
    $json = str_replace("\\", "\\\\", $json);
    $decode = json_decode($json, true);

    $aDecode = $aPhonetic = $aMeaning = array();

    $aDecode = $decode['primaries'][0];

    $aPhonetic['label'] = $aDecode['terms'][1]['labels'][0]['text'];
    $aPhonetic['text'] = $aDecode['terms'][1]['text'];
    $aPhonetic['sound'] = $aDecode['terms'][2]['text'];

    $nPartOfSpeechCount = count($aDecode['entries']);

    for ($i = 0; $i < $nPartOfSpeechCount; $i++) {
        $aMeaning['pos'][$i]['type'] = $aDecode['entries'][$i]['labels'][0]['text'];
        if ($aMeaning['pos'][$i]['type'] == null ||
           $aMeaning['pos'][$i]['type'] == "Derivative:" ||
           $aMeaning['pos'][$i]['type'] == "Idiom:") {
           $aMeaning['pos'][$i]['type'] = $aDecode['entries'][$i]['terms'][1]['labels'][0]['text'];
            if ($aMeaning['pos'][$i]['type'] == 'DJ') {
                $aMeaning['pos'][$i]['type'] = $aDecode['entries'][$i]['terms'][0]['labels'][0]['text'];
            }
        }
        elseif ($aMeaning['pos'][$i]['type'] == 'Variant:') {
           $aMeaning['pos'][$i]['type'] = $aDecode['entries'][1]['labels'][0]['text'];
           if (count($aMeaning['pos'][$i]) == 1) {
               unset($aMeaning['pos'][$i]);
           }
        }
        elseif ($aMeaning['pos'][$i]['type'] == 'See also:') {
            $aMeaning['pos'][$i]['type'] .= ' '
                . $aDecode['entries'][$i]['terms'][0]['text'];
        }
        
        $nMeaningCount = count($aPMeaning = $aDecode['entries'][$i]['entries']);

        for ($j = 0; $j < $nMeaningCount; $j++) {
            $aMeaning['pos'][$i]['meaning'][$j]['def'][0] = $aPMeaning[$j]['terms'][0]['text'];
            $aMeaning['pos'][$i]['meaning'][$j]['def'][1] = $aPMeaning[$j]['terms'][1]['text'];
            if ($aMeaning['pos'][$i]['meaning'][$j]['def'][1] == null) {
               $aMeaning['pos'][$i]['meaning'][$j]['def'][0] = $aDecode['entries'][$i]['terms'][0]['text'];
               $aMeaning['pos'][$i]['meaning'][$j]['def'][1] = $aDecode['entries'][$i]['terms'][1]['text'];
            }

            $nPMExampleCount = count($aPMExample = $aPMeaning[$j]['entries'][0]['terms']);
            if ($nPMExampleCount > 0) {
                $aMeaning['pos'][$i]['meaning'][$j]['example'][0] = $aPMExample[0]['text'];
                $aMeaning['pos'][$i]['meaning'][$j]['example'][1] = $aPMExample[1]['text'];
            }

            if ($aMeaning['pos'][$i]['meaning'][$j]['def'][1] == '') {
                unset($aMeaning['pos'][$i]['meaning'][$j]);
            }
        }
    }

    $aMeaning = $aPhonetic + $aMeaning;
    return $aMeaning;
}


//
// 从 Google Dictinary 获得单词发音
//
function gsound($soundUrl) {
    $ueUrl = urlencode($soundUrl);

    $playcode =<<<EOF
<object data="/dictionary/flash/SpeakerApp16.swf" type="application/x-shockwave-flash" id="pronunciation" height="16" width=" 16">
<param name="movie" value="http://www.google.com/dictionary/flash/SpeakerApp16.swf">
<param name="flashvars" value="sound_name={$ueUrl}">
<param name="wmode" value="transparent">
<a href="{$soundUrl}"><img src="http://www.google.com/dictionary/flash/SpeakerOffA16.png" alt="发音" height="16" width="16" border="0"></a>
</object>
EOF;
    return $playcode;
}

function generate_content($page = 1) {
  global $vw_perpage, $dbhost, $dbuser, $dbpassword, $dbdatabase;

  $db = mysql_connect($dbhost, $dbuser, $dbpassword);
  mysql_select_db($dbdatabase, $db);
  mysql_query("set names 'utf8';");

  $sql = "SELECT * FROM vws_wordlist, vws_pos, vws_def WHERE vws_def.pid=vws_pos.id AND vws_pos.wid = vws_wordlist.id ORDER BY wl_date DESC;";
  $result = mysql_query($sql);
  $numrows = mysql_num_rows($result);
  $i = 0;
  $arr = array();
 
  // Generate Words table array
  if ($numrows > 0) {
      while ($row = mysql_fetch_assoc($result)) {
        $id = $row['wl_id'];
        $key = $row['wl_key'];
        $pron = $row['wl_pron'];
        $def = $row['wl_def'];
        $sent_o = $row['wl_orig'];
        $sent_t = $row['wl_trans'];

        $arr[$i] = '<table class="word_fleet" cellspacing="2">';
        $arr[$i] .= '<tr>';
        $arr[$i] .= '<td class="word_box_s">';
        $arr[$i] .= $key . '<span id="' . $id . '"></span>';
        $arr[$i] .= '</td>';
        if ($pron == '') {
            $arr[$i] .='<td class="word_box_s">' . audio($key) . '</td>';
        }
        else {
            $arr[$i] .= '<td class="word_box_s">/' . $pron . '/ ' . audio($key) . '</td>';
        }
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

    // Assign all the page numbers and links to the string
    for ($l = 1; $l < $pages+1; $l++) {
      if ($page == $l) {
        $link .= ' <span class="current">' . $l . '</span> '; // If we are on the current page
      }
      else {
        $link .= ' <a href="?page=' . $l . '" class="page">' . $l . '</a> ';
      }
    }

    $arr[] = '<div id="pagination">' . $plink . $link . $nlink . '</div>';
  }

  foreach ($arr as $key) {
    echo $key;
  }

}

?>

