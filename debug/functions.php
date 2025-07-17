<?php

/**
 * Custom debug functions.
 *
 * @package Image_Optmizer
 */

/**
 * Highlight Array
 *
 * @param array $debug_array
 * @return void
 */
function c2w_ha($debug_array): void
{
    ini_set('highlight.comment', '#008000');
    ini_set('highlight.default', '#ccc');
    ini_set('highlight.html', '#808080');
    ini_set('highlight.keyword', '#6868f9; font-weight: bold');
    ini_set('highlight.string', '#8cf580');

    echo "<pre class='c2w-pre'>";

    highlight_string("<?php\n" . var_export($debug_array, true) . ";\n?>");

    echo '</pre>';

    echo <<<STYLE
        <style>
            .c2w-pre {
                font-size: 0.9rem;
                font-weight: 400;
                background: #101010;
                padding: 0.4rem 1rem;
                line-height: 1.8rem;
                letter-spacing: 0.01rem;
            }
        </style>
    STYLE;
}
