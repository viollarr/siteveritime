<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('pr')) {

    /**
     * 
     * @param type $label Texto que deverá ser exibido antes do conteúdo da variável.
     * @param type $var Variável que se deseja ver o conteúdo.
     */
    function pr($label = "", $var = "") {
        ?>
        <div>
            <?php
            $label = utf8_encode($label);
            echo "{$label}";
            if (!empty($var)) {
                echo ": <pre>";
                print_r($var);
                echo "</pre><br/>";
            }//if
            ?>
        </div>
        <?php
    }

}

if (!function_exists('pexit')) {

    function pexit($label = '', $var, $local = '') {
        $label = utf8_decode($label);
        $local = utf8_decode($local);

        pr($label, $var);
        echo '<hr />';
        exit($local);
    }

}

/* End of file pr_helper.php */
/* Location: ./application/helpers/pr_helper.php */