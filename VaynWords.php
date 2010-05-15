<?php
/**
 * Author:
 *    Vayn a.k.a. VT <vt@elnode.com>
 *    http://elnode.com
 *
 *    File:             VaynWord.php
 *    Create Date:      2010年 05月 08日 星期六 04:41:35 CST
 */
  session_start();
  
  if ($_SESSION['SESS_QUERY']) {
    session_destroy();
    include_once('config.php');
    include_once('TwitterSearch.php');
    include_once('vws_functions.php');
    
    // Search from Twitter
    $search = new TwitterSearch();
    $search->user_agent = $vw_useragent;
    
    $results = $search->from($vw_username)->with($vw_hashtag)->results();

    if (!file_exists('vws_data.xml')) {

      $dom = new DomDocument('1.0', 'UTF-8');

      // add root - <words>
      $data_root = $dom->appendChild($dom->createElement('words'));
      $i=1;

      foreach ($results as $key) {
        // Get word from hashtag tweet
        $tweet = substr($key->text, 0, strrpos($key->text, '#'));
        $tweet = explode(',', $tweet);
        $tweet = array_map('trim', $tweet);

        foreach ($tweet as $word) {

          $word_def = dict_query($word);

          $date = $key->created_at;

          if ($word_def != FALSE) {
            // add child element of <words>
            $data_root_word = $data_root->appendChild($dom->createElement('word'));

            $word_attr = $data_root_word->appendChild($dom->createAttribute('id'));
            $word_attr->appendChild($dom->createTextNode($i));

            $data_root_word_date = $data_root_word->appendChild($dom->createElement('date'));
            $data_root_word_date->appendChild($dom->createTextNode($date));          

            $data_root_word_key = $data_root_word->appendChild($dom->createElement('key'));
            $data_root_word_key->appendChild($dom->createTextNode($word_def['key']));

            $data_root_word_defs = $data_root_word->appendChild($dom->createElement('defs'));

            $data_root_word_defs_pron = $data_root_word_defs->appendChild($dom->createElement('pron'));
            $data_root_word_defs_pron->appendChild($dom->createTextNode($word_def['pron']));

            $data_root_word_defs_def = $data_root_word_defs->appendChild($dom->createElement('def'));
            $data_root_word_defs_def->appendChild($dom->createTextNode($word_def['def']));

            $data_root_word_defs_sent = $data_root_word_defs->appendChild($dom->createElement('sent'));

            $data_root_word_defs_sent_orig = $data_root_word_defs_sent->appendChild($dom->createElement('orig'));
            $data_root_word_defs_sent_orig->appendChild($dom->createTextNode($word_def['sent_o']));

            $data_root_word_defs_sent_trans = $data_root_word_defs_sent->appendChild($dom->createElement('trans'));
            $data_root_word_defs_sent_trans->appendChild($dom->createTextNode($word_def['sent_t']));

            $i++;
          }
        }
      }

      $dom->preserveWhiteSpace = FALSE;
      $dom->formatOutput = TRUE; // Set the formatOutput attribute of DomDocument to true
      $dom->save('vws_data.xml');
    }
    else {
      $xml = new DomDocument();
      $xml->preserveWhiteSpace = FALSE;
      $xml->load('vws_data.xml');
      $xpath = new DomXpath($xml);

      $flag = $xpath->query('/words/word/key[1]')->item(0)->textContent;
      $flag = strtolower($flag);

      // Get biggest ID
      $id = $xml->getElementsByTagName('word');
      foreach ($id as $id) {
        $i = $id->getAttribute('id');
      }
      // Generate new biggest ID
      $i = $i+1;

      // Query first element with Xpath
      $top = $xpath->query('/words/word[1]')->item(0);

      foreach ($results as $key) {
        // Get word from hashtag tweet
        $tweet = substr($key->text, 0, strrpos($key->text, '#'));
        $tweet = explode(',', $tweet);
        $tweet = array_map('trim', $tweet);
        $date = $key->created_at;

        foreach ($tweet as $word) {
          // If the latest word equal to word from database, stop inserting data
          if ($flag == $word) {
            header('Location: ./');
            break;
          }
          else {
            // Get definitions from dict.cn
            $word_def = dict_query($word);

            if ($word_def != FALSE) {
              // Create new child element of <words>
              $data_root_word = $xml->createElement('word');

              $word_attr = $data_root_word->appendChild($xml->createAttribute('id'));
              $word_attr->appendChild($xml->createTextNode($i));

              $data_root_word->appendChild($xml->createElement('date', $date));
              
              $data_root_word->appendChild($xml->createElement('key', $word_def['key']));
              $data_root_word_defs = $data_root_word->appendChild($xml->createElement('defs'));

              $data_root_word_defs->appendChild($xml->createElement('pron', $word_def['pron']));
              $data_root_word_defs->appendChild($xml->createElement('def', $word_def['def']));
              $data_root_word_defs_sent = $data_root_word_defs->appendChild($xml->createElement('sent'));

              $data_root_word_defs_sent->appendChild($xml->createElement('orig', $word_def['sent_o']));
              $data_root_word_defs_sent->appendChild($xml->createElement('trans', $word_def['sent_t']));

              // Insert new element before the top of old elements
              $top->parentNode->insertBefore($data_root_word, $top);
              
              $i++;
            }
          }
        }
      }
      // Change ID of the last word to the biggest one
      $id = $id->appendChild($xml->createAttribute('id'));
      $id->appendChild($xml->createTextNode($i));

      // Save new data into database
      $xml->save('vws_data.xml');
    }
  }
  else {
    header('Location: ./');
    exit;
  }

?>

