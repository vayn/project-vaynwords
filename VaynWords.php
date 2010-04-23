<?php
  
session_start();
  
if ($_SESSION['SESS_QUERY']) {
  
  ob_start();
  
  include_once("./config.php");
  include_once("./TwitterSearch.php");
  include_once("./functions.php");
  
  # Search from Twitter
  $search = new TwitterSearch();
  $search->user_agent = $vw_useragent;
  
  $results = $search->from($vw_username)->with($vw_hashtag)->results();
  
  # Get flag 
  if (file_exists('content_flag.php')) {
    if (($fp = fopen('content_flag.php', 'r')) == FALSE) {
      die('Failed to open file for writing!');
    }
    else {
      $flag = fread($fp, filesize('content_flag.php'));
      fclose($fp);
    }
  }
  
  foreach ($results as $key) { 
    $word = substr($key->text, 0, strrpos($key->text, '#'));
  
    # Query definition from Dict.cn until $word == $flag 
    if ($word == $flag) {
      break;
    }
    else {
      echo '<table class="word_fleet" cellspacing="2">';
      echo '<tr>';
      echo '<td class="word_box_s">';
      echo $word;
      echo '</td>';
      $def = definition($word);
      echo '<td class="word_box_s">/' . $def['pron'] . '/ ' . audio($def['key']) . '</td>';
      echo '<td class="word_box_s">' . $def['def'] . '</td>';
      // echo '<td>' . substr($key->created_at, 0, strrpos($key->created_at, ' ')) . '</td>';
      // echo '<td>' . $key->source . '</td>';
      echo '</tr>';
  
      if ($def['sent_o'] != '' || $def['sent_t'] != '') {
        echo '<tr><td class="word_box_l" colspan=3>' . $def['sent_o'] . '</td></tr>';
        echo '<tr><td class="word_box_l" colspan=3>' . $def['sent_t'] . '</td></tr>';
      }
  
      echo '</table>';
    }
  }
  
  $new_content = ob_get_contents();
  
  $old_content = '';
  
  if (file_exists('content.php')) {
    if (($fp = fopen('content.php', 'r')) == FALSE) {
      die('Failed to open file for writing!');
    }
    else {
      $old_content = fread($fp, filesize('content.php'));
      fclose($fp);
    }
  }
  
  # Output content
  if (($fp = fopen('content.php', 'w')) == FALSE) {
    die('Failed to open file for writing!');
  }
  else {
    $content = $new_content . $old_content;
    fwrite($fp, $content);
    fclose($fp);
  }
  
  # Update flag
  $flag = substr($results[0]->text, 0, strrpos($results[0]->text, '#'));
  if (($fp = fopen('content_flag.php', 'w')) == FALSE) {
    die('Failed to open file for writing!');
  }
  else {
    fwrite($fp, $flag);
    fclose($fp);
  }

  session_destroy();
}
else {
  header('Location: ./');
  exit;
}

?>
