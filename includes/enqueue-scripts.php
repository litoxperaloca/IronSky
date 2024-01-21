<?php
    //agrego dependencias cexternas (js y css)
    function ironsky_enqueue_scripts() {
        //js de mapbox
        wp_enqueue_script('mapbox-gl-js', 'https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.js', array(), null, false);
        //css mapbox
        wp_enqueue_style('mapbox-gl-css', 'https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.css', array(), null);
    }
    //js y css necesarios
    add_action('wp_enqueue_scripts', 'ironsky_enqueue_scripts');
?>