<?php

namespace Awssat\Tailwindo;

class ShimGenerator extends Converter
{
    /**
     * Search the given content and replace.
     *
     * @param string $search
     * @param string $replace
     *
     * @return null
     */
    protected function searchAndReplace($search, $replace)
    {

        // need to handle regex_string
        //  - .table-responsive{-sm|-md|-lg|-xl}
        //  - m&p for spacing of top/bottom/left/etc (t/b/l/r/x/y)
        //  - .navbar-expand{-sm|-md|-lg|-xl}
        //
        // need to handle regex_number
        //  - ditto m&p for 0-5+auto

        // if regex_string && table-resp || navbar
        // $sizes = ['sm', 'md', 'lg', 'xl'];
        //
        // foreach $sizes as $size
        // str_replace('{regex_string}', $size, $bsClass);
        //
        // if regex_number && m || p
        // $regex_number = [1 .. 5];
        // if regex_string
        // $regex_string = ['t', 'b', 'l', 'r', 'x', 'y'];
        //
        // str_replace('{regex_string}', $size, $twClasses);

        $bsClass = $search;
        $twClasses = '';

        if (!empty($replace)) {
            $replace = preg_replace('/([:\/@])/', '\\\\$1', trim($replace));
            $twClasses = '@apply .' . str_replace(' ', ' .', $replace);
        }

        if (
            strpos($bsClass, '{regex_string}') !== false &&
            (
                strpos($bsClass, 'table-responsive') !== false ||
                strpos($bsClass, 'navbar-expand') !== false
            )
        ) {
            foreach (['sm', 'md', 'lg', 'xl'] as $size) {
                $c = str_replace('{regex_string}', $size, $bsClass);
                $this->givenContent .= <<<EOS
.{$c} { {$twClasses} }

EOS;
            }
        }
        elseif (strpos($bsClass, '{regex_number}') !== false) {

            $values = (strpos($bsClass, '{regex_string}') !== false)
                ? ['t', 'b', 'l', 'r', 'x', 'y']
                : [''];

            foreach ($values as $str) {
                foreach ([0, 1, 2, 3, 4, 5, 'auto'] as $num) {
                    // $c = $bsClass;
                    // $t = $twClasses;
                    $c = str_replace('{regex_number}', $num, str_replace('{regex_string}', $str, $bsClass));
                    $t = str_replace('{regex_number}', $num, str_replace('{regex_string}', $str, $twClasses));
                    $this->givenContent .= <<<EOS
.{$c} { {$t} }

EOS;
                }
            }
        }
        else {
            $this->givenContent .= <<<EOS
.${bsClass} { ${twClasses} }

EOS;
        }
    }
}
