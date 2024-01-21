<?php
    //Defino widget de IronSky
    class IronSky_Widget extends WP_Widget {
        

        public function __construct() {
            parent::__construct(
                'ironsky_widget', 
                'IronSky: Realtime flights 3D Map', 
                array('description' => 'This plugin shows a 3d mapbox map and loads realtime flights as map features.')
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
            $map_width = getValueFromWpAdminSettings('map_width',300);
            $map_height = getValueFromWpAdminSettings('map_height',300);
            $map_zoom = getValueFromWpAdminSettings('map_zoom',1);
            ?>
	    <!-- Contenedor del mapa -->
            <div id='map' style='width: <?php echo $map_width; ?>px; height: <?php echo $map_height; ?>px;'></div>
	    <!-- css con mini ajustes -->
            <style>
                .mapboxgl-popup, .mapboxgl-popup h5, .mapboxgl-popup p{
                color: #000000 !important;
                }
                .mapboxgl-popup h5{
                font-size: 10pt;
                }
            </style>
	    <!-- cargo mapa y vuelos -->
            <script>
		//Cargo mapbox apikey desde la conf de admin menu
                mapboxgl.accessToken = '<?php echo $mapbox_api_key; ?>';
		//Creo mapa
                const map = new mapboxgl.Map({
                    container: 'map', // container ID
                    style: 'mapbox://styles/litoxperaloca/clpeazft8006q01pg8fghbgdl', // style URL
                    center: [-56.183321,  -34.911186], // starting position [lng, lat]
                    zoom: <?php echo $map_zoom; ?>, // starting zoom
                    projection: 'globe',
                });
                map.on('load', async () => {
                      map.loadImage(
                	'https://ironplatform.com.uy/wp-content/uploads/2024/01/plane.png',
	                (error, image) => {
	                	if (error) throw error;
	               		map.addImage('custom-marker', image);
				
				//Llamada a la api de vuelos
	                        fetch('https://airlabs.co/api/v9/flights?_fields=hex,flag,lat,lng,dir,alt&zoom=8&api_key=<?php echo $airlabs_api_key; ?>')
	                        .then( response => response.json())
	                        .then(data =>{
	                            //console.log(data);
					
	                            //FeatureCollection con la respuesta
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
					
				    //Creo map source   	
	                            map.addSource('liveflights', {
	                            'type': 'geojson',
	                            'data': {
	                                    type: 'FeatureCollection',
	                                    features: flights
	                                }
	                            });
					
	                            //Añado layer con el icono a la source creada
	                            map.addLayer({
		                        'id': 'flights',
		                        'type': 'symbol',
		                        'source': 'liveflights',
		                        'layout': {
			                        'icon-image': 'custom-marker',
			                        'icon-size': 0.40,
			                        'icon-rotate': ['get', 'dir']
		                
	                            	}
	                            });

				    //Agrego popup al clickear avion
	                            map.on('click', 'flights', function(e) {
	                                var vuelo = e.features[0];
	                                var coordenadas = vuelo.geometry.coordinates.slice();
	                                var descripcion = '<br/><br/><br/><h5>Flight ID: ' + vuelo.properties.hex + '</h5><p>Flag: ' + vuelo.properties.flag + '</p><p>Altitude: ' + vuelo.properties.alt + ' meters</p>';
	                
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
	
	 //registro mi widget
    add_action('widgets_init', function() {
        register_widget('IronSky_Widget');
    });
?>
