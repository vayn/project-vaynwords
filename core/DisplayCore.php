<?php

if (!defined('IN_VWS')) {
    exit('Access Denied.');
}

class DisplayCore {

    public static function indexDisplay() {
        $words = VWSCore::pullwords();

        foreach ($words as $word) {
            $id = $word['id'];
            $key = $word['key'];
            $pho = $word['text'];
            $mp3 = $word['sound'];
            $pho = $word['pho'];

            $arr[] = '<div class="word"><span id="' . $id . '"></span>' . $key . ' ';
            if ($pho == '' && $mp3 != '') {
                $arr[] .= VWSCore::soundPlayer($mp3) . '<br />';
            }
            elseif ($pho != '') {
                if ($mp3 != '') {
                    $arr[] .= '/' . $pho . '/ ' . VWSCore::soundPlayer($mp3) . '<br />';
                }
                else {
                    $arr[] .= '/' . $pho . '/ ' . '<br />';
                }
            }

            $def = $word['def'];
            $def_pos = $word['dpos'];
            $arr[] .= $def_pos . ' ' . $def . '<br />';

            $senO = $word['ses'];
            $senT = $word['scs'];
            $sen_pos = $word['spos'];
            if ($senO != '' || $senT != '') {
                if ($sen_pos != '') $sen_pos = ' [' . $sen_pos . ']';
                $arr[] .= $senO . $sen_pos . '<br />';
                $arr[] .= $senT . '<br />';
            }

            $c = $word['morc'];
            $m = $word['morm'];
            if ($c != '' || $m != '') {
               $arr[] .= '[ ' . $c . ': ';
               $arr[] .= $m . ' ]';
            }

            $arr[] .= '</div>';
        }

        return $arr;
    }

    public static function mobiDisplay() {
        $words = VWSCore::pullwords();

        foreach ($words as $word) {
            $id = $word['id'];
            $key = $word['key'];
            $pho = $word['text'];
            $mp3 = $word['sound'];
            $pho = $word['pho'];

            $arr[] = '<li style="listy-style: none;"><span id="' . $id . '"></span><span class="author">' . $key . '</span> ';
            if ($pho != '') {
                $arr[] .= '/' . $pho . '/ ' . '<br />';
            }

            $def = $word['def'];
            $def_pos = $word['dpos'];
            $arr[] .= $def_pos . ' ' . $def . '<br />';

            $senO = $word['ses'];
            $senT = $word['scs'];
            $sen_pos = $word['spos'];
             if ($senO != '' || $senT != '') {
                 if ($sen_pos != '') $sen_pos = ' [' . $sen_pos . ']';
                $arr[] .= $senO . $sen_pos . '<br />';
                 $arr[] .= $senT . '<br />';
            }

            $c = $word['morc'];
            $m = $word['morm'];
            if ($c != '' || $m != '') {
                $arr[] .= '<p class="description">[ ' . $c . ': ';
                $arr[] .= $m . ' ]</p>';
            }

            $arr[] .= '</li>';
        }
        return $arr;
    }

}

?>
