<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class that outputs an HTML table.
 * 
 * Important note!
 * This class is marked as private, which means that we do NOT recommended using it.
 * This class is subject to change without warning in any future SliceWP release.
 * If you would still like to make use of the class, you should make a copy to use and distribute with your own project, or else use it at your own risk.
 * 
 * @access private
 * 
 */
class SliceWP_List_Table {

    /**
     * A string identifying the table.
     * 
     * @access protected
	 * @var    string
     * 
     */
    protected $id = '';

    /**
     * The base URL where the table is outputted.
     * 
     * @access protected
     * @var    string
     * 
     */
    protected $screen_base_url = '';

    /**
     * The items to be shown in the table.
     * 
     * @access protected
	 * @var    array
     * 
     */
    protected $items = array();

    /**
     * All items in the collection, not just items shown.
     * 
     * @access protected
	 * @var    int
     * 
     */
    protected $items_total = 0;

    /**
     * How many items to show per page.
     * 
     * @access protected
	 * @var    int
     * 
     */
    protected $items_per_page = 0;

    /**
     * The table's columns.
     * 
     * @access protected
	 * @var    array
     * 
     */
    protected $table_columns = array();

    /**
     * The current page being displayed.
     * 
     * @access protected
     * @var    int
     * 
     */
    protected $current_page = 0;

    /**
     * The message to show when no items are in the table.
     * 
     * @access protected
	 * @var    string
     * 
     */
    protected $no_items = '';

    /**
     * Array of filters that should be shown before the table.
     * 
     * @access protected
     * @var    array
     * 
     */
    protected $table_filters = array();

    /**
     * Array of extra arguments.
     * 
     * @access protected
	 * @var    array
     * 
     */
    protected $args = array();


    /**
     * Constructor.
     * 
     */
    public function __construct( $args = array() ) {

        $args = ( is_object( $args ) ? get_object_vars( $args ) : $args );

        foreach ( $args as $key => $value ) {

			if ( ! property_exists( $this, $key ) ) {
				continue;
			}

			$this->$key = $value;

		}

        // Set the screen URL.
        $this->screen_base_url = ( ! empty( $this->screen_base_url ) ? $this->screen_base_url : strtok( set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ), '?' ) );

        // Set items per page.
        $user_preferences = slicewp_get_user_preferences();

        $this->items_per_page = ( empty( $this->items_per_page ) ? ( ! empty( $user_preferences['list_table_items_per_page'] ) ? absint( $user_preferences['list_table_items_per_page'] ) : 10 ) : $this->items_per_page );

        // Set the current page number.
        $page_number_arg_slug = ( ! empty( $this->id ) ? 'page_number_' . $this->id : 'page_number' );

        $this->current_page = ( empty( $this->current_page ) ? ( ! empty( $_GET[$page_number_arg_slug] ) ? absint( $_GET[$page_number_arg_slug] ) : 1 ) : $this->current_page );

    }


    /**
	 * Getter.
	 *
	 * @param string $property
	 *
	 */
	public function get( $property ) {

		if ( method_exists( $this, 'get_' . $property ) ) {

			return $this->{'get_' . $property}();

		} else {

			return ( property_exists( $this, $property ) ? $this->$property : null );

		}

	}


	/**
	 * Setter.
	 *
	 * @param string $property
	 * @param string $value
	 *
	 */
	public function set( $property, $value ) {

		if ( method_exists( $this, 'set_' . $property ) ) {

			$this->{'set_' . $property}( $value );

		} else {

			$this->$property = $value;

		}

	}


	/**
	 * Returns the object attributes and their values as an array.
	 *
	 */
	public function to_array() {

		return get_object_vars( $this );

	}


    /**
     * Outputs HTML table and all related elements.
     * 
     */
    public function output() {

        $this->output_table();

    }


    /**
     * Returns the table columns after passing the value through filters for extendability.
     * 
     * @param array
     * 
     */
    protected function get_filtered_table_columns() {

        $table_columns = $this->table_columns;

        /**
         * Filter the table columns generally.
         * 
         * @param array $table_columns
         * 
         */
        $table_columns = apply_filters( 'slicewp_list_table_columns', $table_columns );

        if ( ! empty( $this->id ) ) {

            /**
             * Filter the table columns targetted by table unique ID.
             * 
             * @param array $table_columns
             * 
             */
            $table_columns = apply_filters( 'slicewp_list_table_columns_' . $this->id, $table_columns );

        }

        return $table_columns;

    }


    /**
     * Outputs the HTML of the table.
     * 
     */
    public function output_table() {

        $this->output_table_before();

        echo '<table class="slicewp-list-table">';

            $this->output_table_header();
            $this->output_table_body();

        echo '</table>';

        $this->output_table_after();

    }

    
    /**
     * Outputs the table header.
     * 
     */
    public function output_table_header() {

        $table_columns = $this->get_filtered_table_columns();

        if ( empty( $table_columns ) ) {
            return;
        }

        echo '<thead>';
            echo '<tr>';

                foreach ( $table_columns as $column_slug => $column_name ) {

                    echo '<th class="slicewp-column-' . esc_attr( $column_slug ) . '">' . $column_name . '</th>';

                }

            echo '</tr>';
        echo '</thead>';

    }


    /**
     * Outputs the table body.
     * 
     */
    public function output_table_body() {

        echo '<tbody>';

            if ( ! empty( $this->items ) ) {

                $items = $this->prepare_items_for_output( $this->items );

                foreach ( $items as $key => $item ) {

                    /**
                     * Filters the $item array generally, right before outputting it.
                     * 
                     * @param array $item
                     * 
                     */
                    $item = apply_filters( 'slicewp_list_table_row_item', $item );

                    if ( ! empty( $this->id ) ) {

                        /**
                         * Filters the $item array targetted by table unique ID, right before outputting it.
                         * 
                         * @param array $item
                         * 
                         */
                        $item = apply_filters( 'slicewp_list_table_row_item_' . $this->id, $item );
                        
                    }

                    $this->output_table_row( $item );

                }

            } else {

                echo '<tr>';
                    echo '<td colspan="' . count( $this->get_filtered_table_columns() ) . '">';
                        $this->output_no_items();
                    echo '</td>';
                echo '</tr>';

            }

        echo '</tbody>';

    }


    /**
     * Outputs the table row for the given item data.
     * 
     * @param array $item
     * 
     */
    public function output_table_row( $item ) {

        echo '<tr>';

            foreach ( $this->get_filtered_table_columns() as $column_slug => $column_name ) {

                $this->output_table_row_column( $column_slug, $item );

            }

        echo '</tr>';

    }


    /**
     * Outputs the given row column.
     * 
     * @param string $column_slug
     * @param array  $item
     * 
     */
    public function output_table_row_column( $column_slug, $item ) {

        echo '<td class="slicewp-column-' . esc_attr( $column_slug ) . '">';
        
            if ( method_exists( $this, 'column_' . $column_slug ) ) {

                echo $this->{'column_' . $column_slug}( $item );

            } else {

                echo $this->column_default( $column_slug, $item );

            }

        echo '</td>';

    }


    /**
     * Returns the default output for the given row column.
     * 
     * @param string $column_slug
     * @param array  $item
     * 
     * @return string
     * 
     */
    public function column_default( $column_slug, $item ) {

        return ( ! empty( $item[$column_slug] ) ? $item[$column_slug] : '' );

    }


    /**
     * Outputs the message shown when no items exist.
     * 
     */
    public function output_no_items() {

        echo ( ! empty( $this->no_items ) ? $this->no_items : __( 'No results found.', 'slicewp' ) );

    }


    /**
     * Outputs elements before the table.
     * 
     */
    public function output_table_before() {

        echo '<div class="slicewp-list-table-before">';

            if ( ! empty( $this->table_filters ) ) {
                $this->output_table_filters();
            }

        echo '</div>';

    }


    /**
     * Outputs elements after the table.
     * 
     */
    public function output_table_after() {

        echo '<div class="slicewp-list-table-after">';

            if ( $this->items_total > 10 ) {
                $this->output_per_page_selector();
            }

            $this->output_table_pagination();

        echo '</div>';

    }


    /**
     * Outputs the table filters.
     * 
     */
    public function output_table_filters() {

        echo '<div class="slicewp-list-table-filters">';

            echo '<form method="GET">';

                $parsed_url = parse_url( $this->screen_base_url );
                parse_str( $parsed_url['query'], $query_vars );

                foreach ( $query_vars as $key => $value ) {
                    echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" />';
                }

                foreach ( $this->table_filters as $filter_slug ) {

                    if ( method_exists( $this, 'output_table_filter_' . $filter_slug ) ) {

                        $this->{'output_table_filter_' . $filter_slug}();
            
                    }

                }

                echo '<button class="slicewp-button-primary" type="submit">' . __( 'Apply', 'slicewp' ) . '</button>';

            echo '</form>';

        echo '</div>';

    }


    /**
     * Outputs the table date range picker.
     * 
     */
    public function output_table_filter_date_range_picker() {

        echo slicewp_element_date_range_picker();

    }


    /**
     * Outputs the per page selector.
     * 
     */
    public function output_per_page_selector() {

        $per_page_options = slicewp_get_list_table_items_per_page_options();

        echo '<div class="slicewp-list-table-per-page-selector">';

            echo '<form method="POST" action="">';
            
                echo '<select name="list_table_items_per_page">';

                    foreach ( $per_page_options as $option ) {
                        echo '<option value="' . absint( $option ) . '" ' . ( $this->items_per_page == $option ? 'selected="selected"' : '' ) . '>' . esc_html( $option ) . '</option>';
                    }

                echo '</select>';

                echo '<span>' . __( 'results per page', 'slicewp' ) . '</span>';

                echo '<input type="hidden" name="slicewp_action" value="update_user_preferences_list_table_items_per_page" />';
                wp_nonce_field( 'slicewp_update_user_preferences_list_table_items_per_page', 'slicewp_token', false );

            echo '</form>';

        echo '</div>';

    }


    /**
     * Outputs the pagination elements.
     * 
     */
    public function output_table_pagination() {

        if ( count( $this->items ) == 0 ) {
            return;
        }

        $total_pages = ceil( $this->items_total / $this->items_per_page );

        echo '<div class="slicewp-list-table-pagination">';

        if ( $this->items_total <= count( $this->items ) ) {

            if ( $this->items_total == 1 ) {
                echo '<span>' . __( '1 result', 'slicewp' ) . '</span>';
            } else {
                echo '<span>' . sprintf( __( '%d results', 'slicewp' ), $this->items_total ) . '</span>';
            }

        } else {

            $first_item_count = ( $this->current_page - 1 ) * $this->items_per_page + 1;
            $last_item_count  = ( $this->current_page * $this->items_per_page < $this->items_total ? $first_item_count + $this->items_per_page - 1 : $this->items_total );

            $parsed_url = parse_url( $this->screen_base_url );
            parse_str( $parsed_url['query'], $query_vars );

            $screen_url = add_query_arg( array_diff_key( $_GET, $query_vars ), $this->screen_base_url );

            echo '<span>' . sprintf( __( 'Showing %s-%s of %s results', 'slicewp' ), '<strong>' . $first_item_count . '</strong>', '<strong>' . $last_item_count . '</strong>', '<strong>' . $this->items_total . '</strong>' ) . '</span>';

            echo '<div class="slicewp-pagination-links">';

                $page_number_arg_slug = ( ! empty( $this->id ) ? 'page_number_' . $this->id : 'page_number' );

                if ( $this->current_page == 1 ) {
                    echo '<span class="slicewp-pagination-link">' . slicewp_get_svg( 'outline-chevron-double-left' ) . '</span>';
                } else {
                    echo '<a class="slicewp-pagination-link" href="' . esc_url( add_query_arg( array( $page_number_arg_slug => 1 ), $screen_url ) ) . '" title="' . __( 'First page', 'slicewp' ) . '">' . slicewp_get_svg( 'outline-chevron-double-left' ) . '</a>';
                }

                if ( $this->current_page == 1 ) {
                    echo '<span class="slicewp-pagination-link">' . slicewp_get_svg( 'outline-chevron-left' ) . '</span>';
                } else {
                    echo '<a class="slicewp-pagination-link" href="' . esc_url( add_query_arg( array( $page_number_arg_slug => $this->current_page - 1 ), $screen_url ) ) . '" title="' . __( 'Previous page', 'slicewp' ) . '">' . slicewp_get_svg( 'outline-chevron-left' ) . '</a>';
                }

                if ( $this->current_page == $total_pages ) {
                    echo '<span class="slicewp-pagination-link">' . slicewp_get_svg( 'outline-chevron-right' ) . '</span>';
                } else {
                    echo '<a class="slicewp-pagination-link" href="' . esc_url( add_query_arg( array( $page_number_arg_slug => $this->current_page + 1 ), $screen_url ) ) . '" title="' . __( 'Next page', 'slicewp' ) . '">' . slicewp_get_svg( 'outline-chevron-right' ) . '</a>';
                }

                if ( $this->current_page == $total_pages ) {
                    echo '<span class="slicewp-pagination-link">' . slicewp_get_svg( 'outline-chevron-double-right' ) . '</span>';
                } else {
                    echo '<a class="slicewp-pagination-link" href="' . esc_url( add_query_arg( array( $page_number_arg_slug => $total_pages ), $screen_url ) ) . '" title="' . __( 'Last page', 'slicewp' ) . '">' . slicewp_get_svg( 'outline-chevron-double-right' ) . '</a>';
                }

            echo '</div>';

        }

        echo '</div>';

    }


    /**
     * Prepares the items for output.
     * Needs to return an array of arrays. Will parse provided data to make sure the output is as required.
     * 
     * @param array $items
     * 
     * @return array
     * 
     */
    protected function prepare_items_for_output( $items ) {

        foreach ( $items as $key => $item ) {

            if ( is_array( $item ) ) {
                continue;
            }

            if ( is_object( $item ) ) {

                $items[$key] = slicewp_object_to_array( $item );

            }

        }

        return $items;

    }

}