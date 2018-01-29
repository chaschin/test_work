<?php

/**
 * @author Alexey Chaschin
 */
class plFunctions
{
    public static function prepare4serialize($a) {
        if (is_array($a)) {
            foreach ($a as &$e) {
                if (is_array($e)) {
                    $e = $this->prepare4serialize($e);
                } else {
                    $e = $this->escapeQuery($e);
                }
            }
        } else {
            $a = $this->escapeQuery($a);
        }
        return $a;
    }

    public static function escapeQuery($str) {
        $str = stripslashes($str);
        return strtr($str, array(
            "\0" => "",
            "'"  => "&#39;",
            "\"" => "&#34;",
            "\\" => "&#92;",
            "<"  => "&lt;",
            ">"  => "&gt;",
        ));
    }

}

?>
