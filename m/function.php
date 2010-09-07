<?php
function mobiContent() {
    $words = pullword();
    $tablecount = 0;

    foreach ($words as $word) {
        $id = $word['id'];
        $key = $word['key'];
        $pho = $word['text'];
        $mp3 = $word['sound'];
        $pho = $word['pho'];

        $arr[$tablecount] = '<li style="listy-style: none;"><span id="' . $id . '"></span><span class="author">' . $key . '</span> ';
        if ($pho == '' && $mp3 != '') {
            $arr[$tablecount] .= gsound($mp3) . '<br />';
        }
        elseif ($pho != '') {
            if ($mp3 != '') {
                $arr[$tablecount] .= '/' . $pho . '/ ' . gsound($mp3) . '<br />';
            }
            else {
                $arr[$tablecount] .= '/' . $pho . '/ ' . '<br />';
            }
        }

        $defCount = count($word['def']);
        for ($i = 0; $i < $defCount; ++$i) {
            $def = $word['def'][$i]['def'];
            $def_pos = $word['def'][$i]['pos'];
            $arr[$tablecount] .= $def_pos . ' ' . $def . '<br />';
        }

        $senCount = count($word['sen']);
        for ($i = 0; $i < $senCount; ++$i) {
            $senO = $word['sen'][$i]['sen_es'];
            $senT = $word['sen'][$i]['sen_cs'];
            $sen_pos = $word['sen'][$i]['pos'];
             if ($senO != '' || $senT != '') {
                 if ($sen_pos != '') $sen_pos = ' [' . $sen_pos . ']';
                $arr[$tablecount] .= $senO . $sen_pos . '<br />';
                 $arr[$tablecount] .= $senT . '<br />';
            }
        }

        $c = $word['mor'][0]['c'];
        $m = $word['mor'][0]['m'];
        if ($c != '' || $m != '') {
            $arr[$tablecount] .= '<p class="description">[ ' . $c . ': ';
            $arr[$tablecount] .= $m . ' ]</p>';
        }

        $arr[$tablecount] .= '</li>';
        $tablecount++;
    }

    foreach ($arr as $key) {
        echo $key;
    }
}
