<?php
	//Admin menu para settings
    function ironsky_add_admin_menu() {
        add_menu_page(
            'IronSky Config',   // Título de la página
            'IronSky',                    // Título del menú
            'manage_options',             // Capacidad requerida para ver este menú
            'ironsky-settings',           // Slug del menú
            'ironsky_settings_page',      // Función que renderiza la página de opciones
            'dashicons-admin-site-alt3',  // Icono del menú
            6                             // Posición en el menú
        );
    }

    add_action('admin_menu', 'ironsky_add_admin_menu');
?>
