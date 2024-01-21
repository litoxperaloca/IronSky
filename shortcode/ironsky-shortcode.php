<?php
	//Creo shortcode [ironsky]
    function ironsky_shortcode($atts) {
        // Guardo la salida del widget en un búfer y retorno
        ob_start();
        the_widget('IronSky_Widget'); 
        $widget_output = ob_get_clean();
        return $widget_output;
    }
    
    function ironsky_register_shortcodes() {
        add_shortcode('ironsky', 'ironsky_shortcode');
    }

    add_action('init', 'ironsky_register_shortcodes');
?>