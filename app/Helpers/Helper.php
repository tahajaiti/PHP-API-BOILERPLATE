<?php
namespace app\Helpers;

class Helper {
    /**
     * Dump and die.
     *
     * @param $var
     * @return void
     */
    public static function dd(...$var): void
    {
        foreach ($var as $elem) {
            echo '<pre class="codespan">';
            echo '<code>';
            !$elem || $elem == '' ? var_dump($elem) : print_r($elem);
            echo '</code>';
            echo '</pre>';
        }

        // die();
    }
}