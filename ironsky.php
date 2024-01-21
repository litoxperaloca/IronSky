<?php
/**
 * Plugin Name: IronSky
 * Description: This plugin shows a 3d mapbox’s map and loads realtime flights as map’s features.
 * Version: 1.0
 * Author: litoxperaloca / Pablo Pignolo, from IronPlatform 
 */
 
    // Incluir archivos de administración
    require_once plugin_dir_path(__FILE__) . 'admin/admin-menu.php';
    require_once plugin_dir_path(__FILE__) . 'admin/settings-page.php';

    // Incluir scripts y estilos
    require_once plugin_dir_path(__FILE__) . 'includes/enqueue-scripts.php';

    // Incluir utilidades y funciones comunes
    require_once plugin_dir_path(__FILE__) . 'includes/utility-functions.php';

    // Incluir el widget
    require_once plugin_dir_path(__FILE__) . 'widget/ironsky-widget.php';

    // Incluir el shortcode
    require_once plugin_dir_path(__FILE__) . 'shortcode/ironsky-shortcode.php';
?>
