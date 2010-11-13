<?php

if (!defined('IN_VWS')) {
    exit('Access Denied.');
}

class VWSCore {

    // Lookup google dictionary sound URL
    public static function gSound($word) {
        $json = file_get_contents("http://www.google.com/dictionary/json?callback=dict_api.callbacks.id100&q={$word}&sl=en&tl=zh&restrict=pr%2Cde&client=te");
        $soundUrl = null;

        if ($json != '') {
            $json = substr($json, strpos($json, "(")+1, -10);
            $json = str_replace("\\", "\\\\", $json);
            $decode = json_decode($json, true);

            $i = 0;

            while ($i < count($decode['primaries'][0]['terms'])) {
                $verify = strpos($decode['primaries'][0]['terms'][$i]['text'], "http");
                if ($verify === 0) {
                    $soundUrl = $decode['primaries'][0]['terms'][$i]['text'];
                    break;
                }
                $i++;
            }    
            return $soundUrl;
        }

        return $soundUrl;
    }

    // Make a sound player with Google dictionary sound
    public static function soundPlayer($soundUrl) {
        $encodedUrl = urlencode($soundUrl);

        $player =<<<EOF
    <object data="http://www.google.com/dictionary/flash/SpeakerApp16.swf" type="application/x-shockwave-flash" id="pronunciation" height="16" width=" 16">
    <param name="movie" value="http://www.google.com/dictionary/flash/SpeakerApp16.swf">
    <param name="flashvars" value="sound_name={$encodedUrl}">
    <param name="wmode" value="transparent">
    <a href="{$soundUrl}"><img src="http://www.google.com/dictionary/flash/SpeakerOffA16.png" alt="发音" height="16" width="16" border="0"></a>
    </object>
EOF;
        return $player;    
    }

    //
    // Get word data from database
    //
    public static function pullwords() {
        global $vw_perpage, $page, $db;

        $vw_perpage = $vw_perpage ? $vw_perpage : 25;
        $page = $page ? $page : 1;

        $start = ($page==1) ? 0 : (($page - 1) * $vw_perpage) + 1;

        $words = array();

        // Fetch word, date, phonogram and sound url from DB
        $sql = "SELECT vws_words.*, vws_des.pos AS dpos, vws_des.def AS def,
                vws_sen.pos AS spos, vws_sen.sen_es AS ses, vws_sen.sen_cs AS scs,
                vws_mor.c AS morc, vws_mor.m AS morm FROM vws_words LEFT JOIN vws_des ON
                vws_words.id = vws_des.wid LEFT JOIN vws_sen ON vws_words.id = vws_sen.wid
                LEFT JOIN vws_mor ON vws_words.id = vws_mor.wid GROUP BY vws_words.key
                ORDER BY count(vws_words.key) DESC, vws_words.date DESC LIMIT {$start}, {$vw_perpage};";
        $res = $db->query($sql);
        $num = !!($db->num_rows($res));
        $i = 0;

        if ($num) {
            while ($row = $db->fetch_array($res)) {
                $words[$i] = array('id'=>$row['id'],
                                    'date'=>$row['date'],
                                    'key'=>$row['key'],
                                    'pho'=>$row['pho'],
                                    'sound'=>$row['sound']
                                );

                // Fetch definition and part of speech from DB
                $words[$i]['dpos'] = $row['dpos'];
                $words[$i]['def'] = $row['def'];

                // Fetch example sentences (both English and Chinese)
                // and the part of speech of the word in example sentence
                // from DB
                $words[$i]['spos'] = $row['spos'];
                $words[$i]['ses'] = $row['ses'];
                $words[$i]['scs'] = $row['scs'];

                // Fetch morphology from DB
                $words[$i]['morc'] = $row['morc'];
                $words[$i]['morm'] = $row['morm'];

                $i++;
            }
        }
        return $words;
    }

    //
    // Pagination
    //
    public static function pagination() {
        global $page, $pages, $vw_perpage;

        if ($pages > 1) {
            // Assign the "previous" and "next" button
            if ($page > 1) {
                $plink = '<a href="?page=' . ($page - 1) . '">&laquo; Prev</a>';
            }
            if($page > 2) {
                $plink = '<a href="?page=1">&lt;</a> <a href="?page=' . ($page - 1) . '">&laquo; Prev</a>';
            }

            if ($page < ($pages-1)) {
                    $nlink = '<a href="?page=' . ($page + 1) . '">Next &raquo;</a> <a href="?page=' . $pages . '">&gt;</a>';
            }
            elseif ($page < $pages) {
                $nlink = '<a href="?page=' . ($page + 1) . '">Next &raquo;</a>';
            }
        }

        // Assign all the page numbers and links to the string
        if ($page < 11) {
            for ($l = 1; $l < 11; $l++) {
                if ($page == $l) {
                    $link .= ' <span class="current">' . $l . '</span> '; // If we are on the current page
                }
                elseif ($l < ($pages+1)) {
                    $link .= ' <a href="?page=' . $l . '" class="page">' . $l . '</a> ';
                }
            }
        }
        else {
            for ($l = $page-4; $l < $page+5; $l++) {
                if ($page == $l) {
                    $link .= ' <span class="current">' . $l . '</span> ';
                }
                elseif ($l < ($pages+1)) {
                    $link .= ' <a href="?page=' . $l . '" class="page">' . $l . '</a> ';
                }
                else break;
            }
        }

        $pagination = '<div id="pagination">' . $plink . $link . $nlink . '</div>';
        return $pagination;
    }

    public static function UserAgent() {
        $user_agent = ( ! isset($_SERVER['HTTP_USER_AGENT'])) ? FALSE : $_SERVER['HTTP_USER_AGENT'];

        return $user_agent;
    }

}

?>
