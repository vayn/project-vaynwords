<?php
/**
 * Author:
 *    Vayn a.k.a. VT <vt@elnode.com>
 *    http://elnode.com
 *
 *    File:             DataUpdate.php
 *    Create Date:      2010年05月08日 星期六 15时36分12秒
 */
  $xml = new DomDocument();
  $xml->preserveWhiteSpace = FALSE;
  $xml->load('vws_data.xml');
  $xpath = new DOMXPath($xml);

  $key = $xpath->query('/words/word/key');

  foreach ($key as $key) {
    $date = $xml->createElement('date');
    $key->parentNode->insertBefore($date, $key);
  }

  /*
  $date = $xml->getElementsByTagName('date');
  foreach ($date as $date) {
    $date->parentNode->removeChild($date);
  }
   */

  $id = $xml->getElementsByTagName('word');
  $i = 1;

  foreach ($id as $uid) {
    $uid = $uid->appendChild($xml->createAttribute('id'));
    $uid->appendChild($xml->createTextNode($i));
    $i++;
  }

  // Save new data into database
  $xml->save('vws_data.xml');
  echo "Update Successfully!";

?>

