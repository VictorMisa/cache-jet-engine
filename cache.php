<?php
/*
Plugin Name: JetEngine Query Cache
Description: Cachea las consultas de JetEngine, incluyendo post types, taxonomías y listings, para mejorar el rendimiento. Proporciona una interfaz en el backend para gestionar la caché y visualizar estadísticas, incluyendo consultas no cacheadas.
Version: 1.8
Author: Victor Misa
Author URI:  https://victormisa.com
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Evita el acceso directo.
}

class JetEngine_Query_Cache {

    private $cache_expiration = 12 * HOUR_IN_SECONDS; // Duración de la caché (12 horas)
    private $max_uncached_queries = 50; // Máximo de consultas no cacheadas a almacenar

    public function __construct() {

        add_filter( 'posts_results', array( $this, 'cache_jetengine_queries' ), 10, 2 );
        add_filter( 'the_posts', array( $this, 'cache_jetengine_queries_alt' ), 10, 2 );

        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_post_clear_jetengine_cache', array( $this, 'handle_clear_cache' ) );

        add_action( 'admin_init', array( $this, 'register_settings' ) );

        add_action( 'pre_get_posts', array( $this, 'maybe_cache_jetengine_queries' ) );
    }


    public function cache_jetengine_queries( $posts, $query ) {
        if ( is_admin() ) {
            return $posts;
        }

        // Obtiene los post types, taxonomías y listings seleccionados para cachear
        $selected_post_types = get_option( 'jetengine_cache_selected_post_types', array() );
        $selected_taxonomies = get_option( 'jetengine_cache_selected_taxonomies', array() );
        $selected_listings = get_option( 'jetengine_cache_selected_listings', array() );

        if ( empty( $selected_post_types ) && empty( $selected_taxonomies ) && empty( $selected_listings ) ) {
            return $posts;
        }

        $should_cache = false;

        $current_post_type = $query->get( 'post_type' );
        $current_tax_query = $query->get( 'tax_query' );
        $current_listing = $query->get( 'jet_engine_listing' ); // Asumiendo que 'jet_engine_listing' es el parámetro para listings

        // Verificar post types
        if ( is_array( $current_post_type ) ) {
            $intersect = array_intersect( $current_post_type, $selected_post_types );
            if ( ! empty( $intersect ) ) {
                $should_cache = true;
            }
        } else {
            if ( 'any' === $current_post_type ) {
                if ( ! empty( $selected_post_types ) ) {
                    $should_cache = true;
                }
            } elseif ( in_array( $current_post_type, $selected_post_types ) ) {
                $should_cache = true;
            }
        }

        // Verificar taxonomías
        if ( ! $should_cache && ! empty( $current_tax_query ) && is_array( $current_tax_query ) ) {
            foreach ( $current_tax_query as $tax ) {
                if ( isset( $tax['taxonomy'] ) && in_array( $tax['taxonomy'], $selected_taxonomies ) ) {
                    $should_cache = true;
                    break;
                }
            }
        }

        // Verificar listings
        if ( ! $should_cache && ! empty( $current_listing ) && in_array( $current_listing, $selected_listings ) ) {
            $should_cache = true;
        }

        // Incrementa el contador total de consultas
        $this->increment_total_queries();

        if ( ! $should_cache ) {
            return $posts;
        }

        // Genera una clave única para la caché basada en los parámetros de la consulta
        $cache_key = $this->generate_cache_key( $query );

        // Intenta obtener los resultados de la caché
        $cached_results = get_transient( $cache_key );

        if ( false !== $cached_results ) {
            // Incrementa el contador de hits de caché
            $this->increment_cache_hits();
            return $cached_results;
        }

        // Si no hay caché, guarda los resultados actuales en la caché
        set_transient( $cache_key, $posts, $this->cache_expiration );

        return $posts;
    }


    public function cache_jetengine_queries_alt( $posts, $query ) {
        if ( is_admin() ) {
            return $posts;
        }

        // Obtiene los post types, taxonomías y listings seleccionados para cachear
        $selected_post_types = get_option( 'jetengine_cache_selected_post_types', array() );
        $selected_taxonomies = get_option( 'jetengine_cache_selected_taxonomies', array() );
        $selected_listings = get_option( 'jetengine_cache_selected_listings', array() );

        if ( empty( $selected_post_types ) && empty( $selected_taxonomies ) && empty( $selected_listings ) ) {
            return $posts;
        }

        $should_cache = false;

        $current_post_type = $query->get( 'post_type' );
        $current_tax_query = $query->get( 'tax_query' );
        $current_listing = $query->get( 'jet_engine_listing' ); // Asumiendo que 'jet_engine_listing' es el parámetro para listings

        // Verificar post types
        if ( is_array( $current_post_type ) ) {
            $intersect = array_intersect( $current_post_type, $selected_post_types );
            if ( ! empty( $intersect ) ) {
                $should_cache = true;
            }
        } else {
            if ( 'any' === $current_post_type ) {
                if ( ! empty( $selected_post_types ) ) {
                    $should_cache = true;
                }
            } elseif ( in_array( $current_post_type, $selected_post_types ) ) {
                $should_cache = true;
            }
        }

        // Verificar taxonomías
        if ( ! $should_cache && ! empty( $current_tax_query ) && is_array( $current_tax_query ) ) {
            foreach ( $current_tax_query as $tax ) {
                if ( isset( $tax['taxonomy'] ) && in_array( $tax['taxonomy'], $selected_taxonomies ) ) {
                    $should_cache = true;
                    break;
                }
            }
        }

        // Verificar listings
        if ( ! $should_cache && ! empty( $current_listing ) && in_array( $current_listing, $selected_listings ) ) {
            $should_cache = true;
        }

        if ( ! $should_cache ) {
            return $posts;
        }

        // Genera una clave única para la caché basada en los parámetros de la consulta
        $cache_key = $this->generate_cache_key( $query );

        // Intenta obtener los resultados de la caché
        $cached_results = get_transient( $cache_key );

        if ( false !== $cached_results ) {
            // Incrementa el contador de hits de caché
            $this->increment_cache_hits();
            return $cached_results;
        }

        // Si no hay caché, guarda los resultados actuales en la caché
        set_transient( $cache_key, $posts, $this->cache_expiration );

        return $posts;
    }

    /**
     * Hook adicional para interceptar consultas antes de que se ejecuten.
     *
     * @param WP_Query $query La instancia de WP_Query.
     */
    public function maybe_cache_jetengine_queries( $query ) {
        if ( is_admin() ) {
            return;
        }  

        // Obtiene los post types, taxonomías y listings seleccionados para cachear
        $selected_post_types = get_option( 'jetengine_cache_selected_post_types', array() );
        $selected_taxonomies = get_option( 'jetengine_cache_selected_taxonomies', array() );
        $selected_listings = get_option( 'jetengine_cache_selected_listings', array() );

        if ( empty( $selected_post_types ) && empty( $selected_taxonomies ) && empty( $selected_listings ) ) {
            return;
        }

        $should_cache = false;

        $current_post_type = $query->get( 'post_type' );
        $current_tax_query = $query->get( 'tax_query' );
        $current_listing = $query->get( 'jet_engine_listing' ); // Asumiendo que 'jet_engine_listing' es el parámetro para listings

        // Verificar post types
        if ( is_array( $current_post_type ) ) {
            $intersect = array_intersect( $current_post_type, $selected_post_types );
            if ( ! empty( $intersect ) ) {
                $should_cache = true;
            }
        } else {
            if ( 'any' === $current_post_type ) {
                if ( ! empty( $selected_post_types ) ) {
                    $should_cache = true;
                }
            } elseif ( in_array( $current_post_type, $selected_post_types ) ) {
                $should_cache = true;
            }
        }

        // Verificar taxonomías
        if ( ! $should_cache && ! empty( $current_tax_query ) && is_array( $current_tax_query ) ) {
            foreach ( $current_tax_query as $tax ) {
                if ( isset( $tax['taxonomy'] ) && in_array( $tax['taxonomy'], $selected_taxonomies ) ) {
                    $should_cache = true;
                    break;
                }
            }
        }

        // Verificar listings
        if ( ! $should_cache && ! empty( $current_listing ) && in_array( $current_listing, $selected_listings ) ) {
            $should_cache = true;
        }

        if ( ! $should_cache ) {
            return;
        }

        // Genera una clave única para la caché basada en los parámetros de la consulta
        $cache_key = $this->generate_cache_key( $query );

        // Intenta obtener los resultados de la caché
        $cached_results = get_transient( $cache_key );

        if ( false !== $cached_results ) {
            // Incrementa el contador de hits de caché
            $this->increment_cache_hits();
            // Reemplazar los resultados de la consulta
            $query->posts = $cached_results;
            $query->found_posts = count( $cached_results );
            $query->post_count = count( $cached_results );
        } else {
            // Agregar acción para guardar en caché después de la consulta
            add_action( 'the_posts', array( $this, 'save_cache_and_log_uncached_query' ), 10, 2 );
        }
    }


    public function save_cache_and_log_uncached_query( $posts, $query ) {
        if ( $query->is_main_query() && ! is_admin() ) {
            // Genera la clave de caché
            $cache_key = $this->generate_cache_key( $query );

            set_transient( $cache_key, $posts, $this->cache_expiration );
            // Incrementa el contador total de consultas
            $this->increment_total_queries();
            // Registrar la consulta no cacheada
            $this->log_uncached_query( $query );
        }
        return $posts;
    }


    private function generate_cache_key( $query ) {
        $args = $query->query_vars;
        ksort( $args ); // Ordena los argumentos para consistencia
        return 'jetengine_query_' . md5( serialize( $args ) );
    }

    /**
     * Incrementa el contador total de consultas.
     */
    private function increment_total_queries() {
        $total = get_option( 'jetengine_cache_total_queries', 0 );
        update_option( 'jetengine_cache_total_queries', $total + 1 );
    }

    /**
     * Incrementa el contador de hits de caché.
     */
    private function increment_cache_hits() {
        $hits = get_option( 'jetengine_cache_cache_hits', 0 );
        update_option( 'jetengine_cache_cache_hits', $hits + 1 );
    }

    /**
     * Registra una consulta no cacheada.
     *
     * @param WP_Query $query La instancia de WP_Query.
     */
    private function log_uncached_query( $query ) {
        $uncached_queries = get_option( 'jetengine_cache_uncached_queries', array() );

        // Limitar el número de consultas almacenadas
        if ( count( $uncached_queries ) >= $this->max_uncached_queries ) {
            array_shift( $uncached_queries );
        }

        $uncached_queries[] = array(
            'time' => current_time( 'mysql' ),
            'query' => $query->request,
            'vars'  => $query->query_vars,
        );

        update_option( 'jetengine_cache_uncached_queries', $uncached_queries );
    }


    public function add_admin_menu() {
        add_submenu_page(
            'tools.php',                      // Parent slug (menu principal: Herramientas)
            'JetEngine Query Cache',          // Page title
            'JetEngine Cache',                // Menu title
            'manage_options',                 // Capability
            'jetengine-query-cache',          // Menu slug
            array( $this, 'create_admin_page' ) // Callback function
        );
    }

    /**
     * Registra las configuraciones del plugin.
     */
    public function register_settings() {
        // Configuración para post types
        register_setting(
            'jetengine_cache_settings', // Grupo de opciones
            'jetengine_cache_selected_post_types', // Nombre de la opción
            array(
                'type' => 'array',
                'sanitize_callback' => array( $this, 'sanitize_post_types' ),
                'default' => array(),
            )
        );

        // Configuración para taxonomías
        register_setting(
            'jetengine_cache_settings', // Grupo de opciones
            'jetengine_cache_selected_taxonomies', // Nombre de la opción
            array(
                'type' => 'array',
                'sanitize_callback' => array( $this, 'sanitize_taxonomies' ),
                'default' => array(),
            )
        );

        // Configuración para listings
        register_setting(
            'jetengine_cache_settings', // Grupo de opciones
            'jetengine_cache_selected_listings', // Nombre de la opción
            array(
                'type' => 'array',
                'sanitize_callback' => array( $this, 'sanitize_listings' ),
                'default' => array(),
            )
        );
    }


    public function sanitize_post_types( $input ) {
        $valid_post_types = $this->get_all_custom_post_types();
        $sanitized = array();

        if ( is_array( $input ) ) {
            foreach ( $input as $post_type ) {
                if ( in_array( $post_type, $valid_post_types ) ) {
                    $sanitized[] = $post_type;
                }
            }
        }

        return $sanitized;
    }


    public function sanitize_taxonomies( $input ) {
        $valid_taxonomies = $this->get_all_custom_taxonomies();
        $sanitized = array();

        if ( is_array( $input ) ) {
            foreach ( $input as $taxonomy ) {
                if ( in_array( $taxonomy, $valid_taxonomies ) ) {
                    $sanitized[] = $taxonomy;
                }
            }
        }

        return $sanitized;
    }


    public function sanitize_listings( $input ) {
        $valid_listings = $this->get_all_custom_listings();
        $sanitized = array();

        if ( is_array( $input ) ) {
            foreach ( $input as $listing ) {
                if ( in_array( $listing, $valid_listings ) ) {
                    $sanitized[] = $listing;
                }
            }
        }

        return $sanitized;
    }


    private function get_all_custom_post_types() {
        $all_post_types = get_post_types( array( 'public' => true, '_builtin' => false ), 'names' );
        return $all_post_types;
    }


    private function get_all_custom_taxonomies() {
        $all_taxonomies = get_taxonomies( array( 'public' => true, '_builtin' => false ), 'names' );
        return $all_taxonomies;
    }


    private function get_all_custom_listings() {
        // Reemplaza 'jet-engine-listing' con el nombre correcto si es diferente
        $all_listings = get_posts( array(
            'post_type'      => 'jet-engine-listing', // Asegúrate de que este es el post type correcto para listings
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'fields'         => 'all', // Cambiar a 'all' para obtener los objetos completos
        ) );

        $listing_names = array();

        if ( ! empty( $all_listings ) ) {
            foreach ( $all_listings as $listing ) {
                $listing_names[] = $listing->post_name;
            }
        }

        return $listing_names;
    }


    public function create_admin_page() {
        // Obtiene los post types, taxonomías y listings personalizados
        $custom_post_types = $this->get_all_custom_post_types();
        $custom_taxonomies = $this->get_all_custom_taxonomies();
        $custom_listings = $this->get_all_custom_listings();

        // Obtiene los post types, taxonomías y listings seleccionados para cachear
        $selected_post_types = get_option( 'jetengine_cache_selected_post_types', array() );
        $selected_taxonomies = get_option( 'jetengine_cache_selected_taxonomies', array() );
        $selected_listings = get_option( 'jetengine_cache_selected_listings', array() );

        // Obtiene estadísticas de la caché
        $cache_count = $this->get_cached_queries_count();
        $last_cleared = $this->get_last_cache_cleared();
        $total_queries = get_option( 'jetengine_cache_total_queries', 0 );
        $cache_hits = get_option( 'jetengine_cache_cache_hits', 0 );
        $cache_percentage = $total_queries > 0 ? ( ( $cache_hits / $total_queries ) * 100 ) : 0;
        $uncached_queries = get_option( 'jetengine_cache_uncached_queries', array() );
        ?>
        <div class="wrap">
            <h1>JetEngine Query Cache</h1>
            <p>Este plugin cachea las consultas realizadas por JetEngine, incluyendo post types, taxonomías y listings, para mejorar el rendimiento.</p>

            <h2>Configuración de Cache</h2>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'jetengine_cache_settings' );
                do_settings_sections( 'jetengine-query-cache' );
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Selecciona los Post Types a Cachear</th>
                        <td>
                            <?php if ( ! empty( $custom_post_types ) ) : ?>
                                <?php foreach ( $custom_post_types as $post_type ) : ?>
                                    <label>
                                        <input type="checkbox" name="jetengine_cache_selected_post_types[]" value="<?php echo esc_attr( $post_type ); ?>" <?php checked( in_array( $post_type, $selected_post_types ) ); ?> />
                                        <?php echo esc_html( $post_type ); ?>
                                    </label><br>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <p>No se encontraron post types personalizados.</p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Selecciona las Taxonomías a Cachear</th>
                        <td>
                            <?php if ( ! empty( $custom_taxonomies ) ) : ?>
                                <?php foreach ( $custom_taxonomies as $taxonomy ) : ?>
                                    <label>
                                        <input type="checkbox" name="jetengine_cache_selected_taxonomies[]" value="<?php echo esc_attr( $taxonomy ); ?>" <?php checked( in_array( $taxonomy, $selected_taxonomies ) ); ?> />
                                        <?php echo esc_html( $taxonomy ); ?>
                                    </label><br>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <p>No se encontraron taxonomías personalizadas.</p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Selecciona los Listings a Cachear</th>
                        <td>
                            <?php if ( ! empty( $custom_listings ) ) : ?>
                                <?php foreach ( $custom_listings as $listing ) : ?>
                                    <label>
                                        <input type="checkbox" name="jetengine_cache_selected_listings[]" value="<?php echo esc_attr( $listing ); ?>" <?php checked( in_array( $listing, $selected_listings ) ); ?> />
                                        <?php echo esc_html( $listing ); ?>
                                    </label><br>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <p>No se encontraron listings personalizados.</p>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>

            <h2>Estadísticas de la Caché</h2>
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total de Consultas</td>
                        <td>Número total de consultas interceptadas</td>
                        <td><?php echo esc_html( $total_queries ); ?></td>
                    </tr>
                    <tr>
                        <td>Hits de Caché</td>
                        <td>Número de consultas servidas desde la caché</td>
                        <td><?php echo esc_html( $cache_hits ); ?></td>
                    </tr>
                    <tr>
                        <td>Misses de Caché</td>
                        <td>Número de consultas no servidas desde la caché</td>
                        <td><?php echo esc_html( $total_queries - $cache_hits ); ?></td>
                    </tr>
                    <tr>
                        <td>Porcentaje de Consultas Cacheadas</td>
                        <td>Proporción de consultas servidas desde la caché</td>
                        <td><?php echo esc_html( number_format( $cache_percentage, 2 ) ); ?>%</td>
                    </tr>
                    <tr>
                        <td>Última Vez que se Vació la Caché</td>
                        <td>Fecha y hora de la última limpieza de la caché</td>
                        <td><?php echo esc_html( $last_cleared ); ?></td>
                    </tr>
                </tbody>
            </table>

            <h2>Uso de la Caché</h2>
            <canvas id="cacheUsageChart" width="400" height="200"></canvas>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                var ctx = document.getElementById('cacheUsageChart').getContext('2d');
                var cacheUsageChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Consultas Cacheadas', 'Consultas No Cacheadas'],
                        datasets: [{
                            data: [<?php echo esc_js( $cache_hits ); ?>, <?php echo esc_js( $total_queries - $cache_hits ); ?>],
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.7)',
                                'rgba(255, 99, 132, 0.7)'
                            ],
                            borderColor: [
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 99, 132, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            title: {
                                display: true,
                                text: 'Porcentaje de Consultas Cacheadas'
                            }
                        }
                    },
                });
            </script>

            <h2>Consultas No Cacheadas</h2>
            <?php if ( ! empty( $uncached_queries ) ) : ?>
                <table class="widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Consulta SQL</th>
                            <th>Variables de Consulta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( array_reverse( $uncached_queries ) as $query ) : ?>
                            <tr>
                                <td><?php echo esc_html( $query['time'] ); ?></td>
                                <td><pre><?php echo esc_html( $query['query'] ); ?></pre></td>
                                <td><pre><?php print_r( $query['vars'] ); ?></pre></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No hay consultas no cacheadas registradas.</p>
            <?php endif; ?>

            <h2>Acciones</h2>
            <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
                <?php
                wp_nonce_field( 'clear_jetengine_cache', 'jetengine_cache_nonce' );
                ?>
                <input type="hidden" name="action" value="clear_jetengine_cache">
                <?php submit_button( 'Vaciar Caché', 'secondary', 'submit', false ); ?>
            </form>

            <?php if ( isset( $_GET['cache_cleared'] ) && $_GET['cache_cleared'] == '1' ) : ?>
                <div class="notice notice-success is-dismissible">
                    <p>Caché de JetEngine vaciada exitosamente.</p>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Obtiene el número de consultas cacheadas.
     *
     * @return int Número de consultas cacheadas.
     */
    private function get_cached_queries_count() {
        global $wpdb;
        $selected_post_types = get_option( 'jetengine_cache_selected_post_types', array() );
        $selected_taxonomies = get_option( 'jetengine_cache_selected_taxonomies', array() );
        $selected_listings = get_option( 'jetengine_cache_selected_listings', array() );

        if ( empty( $selected_post_types ) && empty( $selected_taxonomies ) && empty( $selected_listings ) ) {
            return 0;
        }

        $like_clauses = array();

        // Agregar post types a las cláusulas LIKE
        foreach ( $selected_post_types as $post_type ) {
            $cache_key_prefix = 'jetengine_query_' . md5( serialize( array( 'post_type' => $post_type ) ) );
            $like_clauses[] = $wpdb->prepare( 'option_name LIKE %s', $wpdb->esc_like( $cache_key_prefix ) . '%' );
        }

        // Agregar taxonomías a las cláusulas LIKE
        foreach ( $selected_taxonomies as $taxonomy ) {
            $cache_key_prefix = 'jetengine_query_' . md5( serialize( array( 'taxonomy' => $taxonomy ) ) );
            $like_clauses[] = $wpdb->prepare( 'option_name LIKE %s', $wpdb->esc_like( $cache_key_prefix ) . '%' );
        }

        // Agregar listings a las cláusulas LIKE
        foreach ( $selected_listings as $listing ) {
            $cache_key_prefix = 'jetengine_query_' . md5( serialize( array( 'jet_engine_listing' => $listing ) ) );
            $like_clauses[] = $wpdb->prepare( 'option_name LIKE %s', $wpdb->esc_like( $cache_key_prefix ) . '%' );
        }

        if ( empty( $like_clauses ) ) {
            return 0;
        }

        $where = implode( ' OR ', $like_clauses );

        // Consulta la base de datos
        $count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->options} WHERE {$where}" );

        return intval( $count );
    }


    private function get_last_cache_cleared() {
        $timestamp = get_option( 'jetengine_cache_last_cleared', false );
        if ( $timestamp ) {
            return date( 'Y-m-d H:i:s', $timestamp );
        }
        return 'Nunca';
    }

    /**
     * Maneja la solicitud de vaciar la caché.
     */
    public function handle_clear_cache() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'No tienes permisos para acceder a esta página.' );
        }

        if ( ! isset( $_POST['jetengine_cache_nonce'] ) || ! wp_verify_nonce( $_POST['jetengine_cache_nonce'], 'clear_jetengine_cache' ) ) {
            wp_die( 'Nonce inválido.' );
        }

        // Elimina todas las transients relacionadas con JetEngine Query Cache
        global $wpdb;
        $selected_post_types = get_option( 'jetengine_cache_selected_post_types', array() );
        $selected_taxonomies = get_option( 'jetengine_cache_selected_taxonomies', array() );
        $selected_listings = get_option( 'jetengine_cache_selected_listings', array() );

        if ( ! empty( $selected_post_types ) ) {
            foreach ( $selected_post_types as $post_type ) {
                $cache_key_prefix = 'jetengine_query_' . md5( serialize( array( 'post_type' => $post_type ) ) );

                // Eliminar transients
                $wpdb->query(
                    $wpdb->prepare(
                        "
                        DELETE FROM {$wpdb->options}
                        WHERE option_name LIKE %s
                        ",
                        $wpdb->esc_like( $cache_key_prefix ) . '%'
                    )
                );
            }
        }

        if ( ! empty( $selected_taxonomies ) ) {
            foreach ( $selected_taxonomies as $taxonomy ) {
                $cache_key_prefix = 'jetengine_query_' . md5( serialize( array( 'taxonomy' => $taxonomy ) ) );

                // Eliminar transients
                $wpdb->query(
                    $wpdb->prepare(
                        "
                        DELETE FROM {$wpdb->options}
                        WHERE option_name LIKE %s
                        ",
                        $wpdb->esc_like( $cache_key_prefix ) . '%'
                    )
                );
            }
        }

        if ( ! empty( $selected_listings ) ) {
            foreach ( $selected_listings as $listing ) {
                $cache_key_prefix = 'jetengine_query_' . md5( serialize( array( 'jet_engine_listing' => $listing ) ) );

                // Eliminar transients
                $wpdb->query(
                    $wpdb->prepare(
                        "
                        DELETE FROM {$wpdb->options}
                        WHERE option_name LIKE %s
                        ",
                        $wpdb->esc_like( $cache_key_prefix ) . '%'
                    )
                );
            }
        }

        // Actualiza la última vez que se vació la caché
        update_option( 'jetengine_cache_last_cleared', time() );

        // Reinicia los contadores
        update_option( 'jetengine_cache_total_queries', 0 );
        update_option( 'jetengine_cache_cache_hits', 0 );

        // Vaciar las consultas no cacheadas
        update_option( 'jetengine_cache_uncached_queries', array() );

        // Redirecciona de vuelta a la página de configuración con un mensaje de éxito
        wp_redirect( add_query_arg( 'cache_cleared', '1', admin_url( 'tools.php?page=jetengine-query-cache' ) ) );
        exit;
    }

}

if ( class_exists( 'JetEngine_Query_Cache' ) ) {
    new JetEngine_Query_Cache();
}
