<?php
	function ironsky_settings_page() {
        // usuario tiene permisos 
        if (!current_user_can('manage_options')) {
            return;
        }
		$imgSrc= '/wp-content/plugins/ironsky/assets/ironsky-logo.png';
        ?>
        <div class="wrap">
            <h1>IronSky Config</h1>
			<img src="<?php echo $imgSrc;?>"/>
            <form action="options.php" method="post">
                <?php
                // Opciones de seguridad, campos, etc.
                settings_fields('ironsky-settings');
                do_settings_sections('ironsky-settings');
                submit_button('Save');
                ?>
            </form>
        </div>
        <?php
    }
    
    function ironsky_register_settings() {
        // Registro de la configuración para airlabs API Key
        register_setting('ironsky-settings', 'airlabs_api_key');
        // Registro de la configuración para Mapbox API Key
        register_setting('ironsky-settings', 'mapbox_api_key');
        // Registro de la configuración para ancho
        register_setting('ironsky-settings', 'map_width');
        // Registro de la configuración para alto
        register_setting('ironsky-settings', 'map_height');
        // Registro de la configuración para zoom
        register_setting('ironsky-settings', 'map_zoom');
        /*
        // Registro de la configuración para pitch
        register_setting('ironsky-settings', 'map_pitch');
        // Registro de la configuración para lat
        register_setting('ironsky-settings', 'map_center_lat');
        // Registro de la configuración para lon
        register_setting('ironsky-settings', 'map_center_lon');
        */
        // sección de configuración
        add_settings_section(
            'ironsky_settings_section',
            'API keys & map settings',
            'ironsky_settings_section_callback',
            'ironsky-settings'
        );

        // airlabs API Key
        add_settings_field(
            'airlabs_api_key_field',
            'Airlabs API Key',
            'airlabs_api_key_field_callback',
            'ironsky-settings',
            'ironsky_settings_section'
        );

        // Mapbox API Key
        add_settings_field(
            'mapbox_api_key_field',
            'Mapbox API Key',
            'mapbox_api_key_field_callback',
            'ironsky-settings',
            'ironsky_settings_section'
        );
        // Map width
        add_settings_field(
            'map_width_field',
            'Map Width (px), default (leave empty) is 300',
            'map_width_field_callback',
            'ironsky-settings',
            'ironsky_settings_section'
        );
        // Map height
        add_settings_field(
            'map_height_field',
            'Map Height (px), default (leave empty) is 300',
            'map_height_field_callback',
            'ironsky-settings',
            'ironsky_settings_section'
        );
        // Map zoom
        add_settings_field(
            'map_zoom_field',
            'Map Zoom (1-16), default (leave empty) is 1',
            'map_zoom_field_callback',
            'ironsky-settings',
            'ironsky_settings_section'
        );
    }

    add_action('admin_init', 'ironsky_register_settings');

    function ironsky_settings_section_callback() {
        echo '<p>Edit IronSky settings.</p>
			  <p>Get free apikey from mapbox: <a href="https://account.mapbox.com/">https://account.mapbox.com/</a> <br/>
				Get free apikey from airlabs.co <a href="https://airlabs.co/signup">https://airlabs.co/signup</a></p>';
    }

    function airlabs_api_key_field_callback() {
        // Obtener el valor de airlabs API Key
        $airlabs_api_key = get_option('airlabs_api_key');
        echo '<input type="text" name="airlabs_api_key" value="' . esc_attr($airlabs_api_key) . '">';

    }
    
    function mapbox_api_key_field_callback() {
 
        // Obtener el valor de Mapbox API Key
        $mapbox_api_key = get_option('mapbox_api_key');
        echo '<input type="text" name="mapbox_api_key" value="' . esc_attr($mapbox_api_key) . '">';
    }
    
    function map_width_field_callback() {
 
        // Obtener el valor de map_width_field
        $map_width = get_option('map_width');
        echo '<input type="text" name="map_width" value="' . esc_attr($map_width) . '">';
    }
    
    function map_height_field_callback() {
 
        // Obtener el valor de map_height_field
        $map_height = get_option('map_height');
        echo '<input type="text" name="map_height" value="' . esc_attr($map_height) . '">';
    }
    
    function map_zoom_field_callback() {
 
        // Obtener el valor de map_height_field
        $map_zoom = get_option('map_zoom');
        echo '<input type="text" name="map_zoom" value="' . esc_attr($map_zoom) . '">';
    }
?>
