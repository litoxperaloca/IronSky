<?php
/**
 * Plugin Name: IronSky
 * Description: Muestra vuelos actuales en un mapa.
 * Version: 1.0
 * Author: Pablo Pignolo, from IronPlatform 
 */

    //Defino widget de IronSky
    class IronSky_Widget extends WP_Widget {
        

        public function __construct() {
            parent::__construct(
                'ironsky_widget', 
                'IronSky: Vuelos en Tiempo Real', 
                array('description' => 'Muestra vuelos actuales en un mapa de Mapbox.')
            );
        }




        public function form($instance) {
            $airlabs_api_key = !empty($instance['airlabs_api_key']) ? $instance['airlabs_api_key'] : '';
            $mapbox_api_key = !empty($instance['mapbox_api_key']) ? $instance['mapbox_api_key'] : '';
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('airlabs_api_key')); ?>">Airlabs API Key:</label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('airlabs_api_key')); ?>" name="<?php echo esc_attr($this->get_field_name('airlabs_api_key')); ?>" type="text" value="<?php echo esc_attr($airlabs_api_key); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('mapbox_api_key')); ?>">Mapbox API Key:</label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('mapbox_api_key')); ?>" name="<?php echo esc_attr($this->get_field_name('mapbox_api_key')); ?>" type="text" value="<?php echo esc_attr($mapbox_api_key); ?>">
            </p>
        <?php
        }



        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['airlabs_api_key'] = (!empty($new_instance['airlabs_api_key'])) ? strip_tags($new_instance['airlabs_api_key']) : '';
            $instance['mapbox_api_key'] = (!empty($new_instance['mapbox_api_key'])) ? strip_tags($new_instance['mapbox_api_key']) : '';

            return $instance;
        }


        public function widget($args, $instance) {
           echo $args['before_widget'];
            if (!empty($instance['title'])) {
                echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
            }

            // Claves API obtenidas desde el formulario del widget
            //$airlabs_api_key = $instance['airlabs_api_key'];
            //$mapbox_api_key = $instance['mapbox_api_key'];
            $airlabs_api_key = get_option('airlabs_api_key');
            $mapbox_api_key = get_option('mapbox_api_key');
            ?>
            <div id='map' style='width: 400px; height: 300px;'></div>
            <style>
                .mapboxgl-popup, .mapboxgl-popup h5, .mapboxgl-popup p{
                color: #000000 !important;
                }
                .mapboxgl-popup h5{
                font-size: 10pt;
                }
            </style>
            <script>
                mapboxgl.accessToken = '<?php echo $mapbox_api_key; ?>';
                const map = new mapboxgl.Map({
                    container: 'map', // container ID
                    style: 'mapbox://styles/litoxperaloca/clpeazft8006q01pg8fghbgdl', // style URL
                    center: [-56.183321,  -34.911186], // starting position [lng, lat]
                    zoom: 9, // starting zoom
                    projection: 'globe',
                });
                map.on('load', async () => {
                      map.loadImage(
                'https://ironplatform.com.uy/wp-content/uploads/2024/01/plane.png',
                (error, image) => {
                if (error) throw error;
                map.addImage('custom-marker', image);
                        fetch('https://airlabs.co/api/v9/flights?_fields=hex,flag,lat,lng,dir,alt&zoom=8&api_key=<?php echo $airlabs_api_key; ?>')
                        .then( response => response.json())
                        .then(data =>{
                            //console.log(data);
                        
                          const flights = data.response.map(flight => ({
                            type: 'Feature',
                            properties: {
                                hex: flight.hex,
                                flag: flight.flag,
                                dir: flight.dir,
                                alt: flight.alt
                            },
                            geometry: {
                                type: 'Point',
                                coordinates: [flight.lng, flight.lat]
                            }
                            }));
                            map.addSource('liveflights', {
                            'type': 'geojson',
                            'data': {
                                    type: 'FeatureCollection',
                                    features: flights
                                }
                            });
                            // Add a symbol layer
                            map.addLayer({
                            'id': 'flights',
                            'type': 'symbol',
                            'source': 'liveflights',
                            'layout': {
                            'icon-image': 'custom-marker',
                            'icon-size': 0.25,
                                        'icon-rotate': ['get', 'dir']
                
                            }
                            });
                            map.on('click', 'flights', function(e) {
                                var vuelo = e.features[0];
                                var coordenadas = vuelo.geometry.coordinates.slice();
                                var descripcion = '<br/><br/><br/><h5>Vuelo: ' + vuelo.properties.hex + '</h5><p>Flag: ' + vuelo.properties.flag + '</p><p>Altitud: ' + vuelo.properties.alt + ' metros</p>';
                
                                while (Math.abs(e.lngLat.lng - coordenadas[0]) > 180) {
                                    coordenadas[0] += e.lngLat.lng > coordenadas[0] ? 360 : -360;
                                }
                
                                    // Mostrar un Popup en la ubicación del clic con la información del vuelo
                                    new mapboxgl.Popup()
                                        .setLngLat(coordenadas)
                                        .setHTML(descripcion)
                                        .addTo(map);
                                });
                     
                        })
                        .catch(error => console.log(error));
                    });
                });
            </script>

            <?php

            echo $args['after_widget'];
        }
    }
    //Fin clase widget
	



    //hooks de WP
    
    //Admin menu para settings
    function ironsky_add_admin_menu() {
        add_menu_page(
            'Configuración de IronSky',   // Título de la página
            'IronSky',                    // Título del menú
            'manage_options',             // Capacidad requerida para ver este menú
            'ironsky-settings',           // Slug del menú
            'ironsky_settings_page',      // Función que renderiza la página de opciones
            'dashicons-admin-site-alt3',  // Icono del menú
            6                             // Posición en el menú
        );
    }

    add_action('admin_menu', 'ironsky_add_admin_menu');
    
    function ironsky_settings_page() {
        // usuario tiene permisos 
        if (!current_user_can('manage_options')) {
            return;
        }

        ?>
        <div class="wrap">
            <h1>Configuración de IronSky</h1>
            <form action="options.php" method="post">
                <?php
                // Opciones de seguridad, campos, etc.
                settings_fields('ironsky-settings');
                do_settings_sections('ironsky-settings');
                submit_button('Guardar Cambios');
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

        // sección de configuración
        add_settings_section(
            'ironsky_settings_section',
            'Configuraciones de API',
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
    }

    add_action('admin_init', 'ironsky_register_settings');

    function ironsky_settings_section_callback() {
        echo '<p>Introduce las claves API para IronSky.</p>';
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
    
    //agrego dependencisscexternas (js y css)
	function ironsky_enqueue_scripts() {
        //js de mapbox
        wp_enqueue_script('mapbox-gl-js', 'https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.js', array(), null, false);
        //css mapbox
        wp_enqueue_style('mapbox-gl-css', 'https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.css', array(), null);
    }
    //js y css necesarios
    add_action('wp_enqueue_scripts', 'ironsky_enqueue_scripts');
    
    //registro mi widget
    add_action('widgets_init', function() {
        register_widget('IronSky_Widget');
    });
    //Termine de setear hooks de acciones
?>
