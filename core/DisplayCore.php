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

            $defCount = count($word['def']);
            for ($i = 0; $i < $defCount; ++$i) {
                $def = $word['def'][$i]['def'];
                $def_pos = $word['def'][$i]['pos'];
                $arr[] .= $def_pos . ' ' . $def . '<br />';
            }

            $senCount = count($word['sen']);
            for ($i = 0; $i < $senCount; ++$i) {
                $senO = $word['sen'][$i]['sen_es'];
                $senT = $word['sen'][$i]['sen_cs'];
                $sen_pos = $word['sen'][$i]['pos'];
                 if ($senO != '' || $senT != '') {
                     if ($sen_pos != '') $sen_pos = ' [' . $sen_pos . ']';
                    $arr[] .= $senO . $sen_pos . '<br />';
                     $arr[] .= $senT . '<br />';
                }
            }

            $c = $word['mor'][0]['c'];
            $m = $word['mor'][0]['m'];
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

            $defCount = count($word['def']);
            for ($i = 0; $i < $defCount; ++$i) {
                $def = $word['def'][$i]['def'];
                $def_pos = $word['def'][$i]['pos'];
                $arr[] .= $def_pos . ' ' . $def . '<br />';
            }

            $senCount = count($word['sen']);
            for ($i = 0; $i < $senCount; ++$i) {
                $senO = $word['sen'][$i]['sen_es'];
                $senT = $word['sen'][$i]['sen_cs'];
                $sen_pos = $word['sen'][$i]['pos'];
                 if ($senO != '' || $senT != '') {
                     if ($sen_pos != '') $sen_pos = ' [' . $sen_pos . ']';
                    $arr[] .= $senO . $sen_pos . '<br />';
                     $arr[] .= $senT . '<br />';
                }
            }

            $c = $word['mor'][0]['c'];
            $m = $word['mor'][0]['m'];
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
