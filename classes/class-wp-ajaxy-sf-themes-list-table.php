<?php
/**
 * List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 * @access private
 */
 
class WP_SF_THEMES_List_Table extends WP_List_Table {

	var $callback_args;

	function WP_SF_List_Table() {
		parent::__construct( array(
			'plural' => 'Settings',
			'singular' => 'Setting',
		) );
	}

	function ajax_user_can() {
		return true;
	}

	function prepare_items() {
		$ratingManager = new AjaxyUserRating();
		$fields = $ratingManager->get_all_fields();

		$search = !empty( $_REQUEST['s'] ) ? trim( stripslashes( $_REQUEST['s'] ) ) : '';

		$args = array(
			'search' => $search,
			'page' => $this->get_pagenum(),
			'number' => $tags_per_page,
		);

		if ( !empty( $_REQUEST['orderby'] ) )
			$args['orderby'] = trim( stripslashes( $_REQUEST['orderby'] ) );

		if ( !empty( $_REQUEST['order'] ) )
			$args['order'] = trim( stripslashes( $_REQUEST['order'] ) );

		$this->callback_args = $args;

		$this->set_pagination_args( array(
			'total_items' => sizeof($fields),
			'per_page' => 10,
		) );
	}

	function has_items() {
		// todo: populate $this->items in prepare_items()
		return true;
	}

	function get_bulk_actions() {
		$actions = array();
		//$actions['apply'] = __( 'Apply theme' );

		return $actions;
	}

	function current_action() {
		if ( isset( $_REQUEST['action'] ) && ( 'hide' == $_REQUEST['action'] || 'hide' == $_REQUEST['action2'] ) )
			return 'bulk-hide';

		return parent::current_action();
	}

	function get_columns() {
		$columns = array(
			'title'    => __( 'Theme' ),
			'directory'        => __( 'Directory' ),
			'stylesheet_url'        => __( 'Stylesheet URL' )
		);

		return $columns;
	}
	function get_column_info() {
		if ( isset( $this->_column_headers ) )
			return $this->_column_headers;

		$screen = get_current_screen();

		$columns = $this->get_columns();
		$hidden = array();

		$this->_column_headers = array( $columns, $hidden, $this->get_sortable_columns() );

		return $this->_column_headers;
	}

	function get_sortable_columns() {
		return array(
		);
	}

	function display_rows_or_placeholder() {
		global $AjaxyLiveSearch;
		$themes = $AjaxyLiveSearch->get_installed_themes(AJAXY_THEMES_DIR, 'themes'); 
		
		$fields = $AjaxyLiveSearch->get_post_types();
		$fields[] = (object)array('name' => 'category', 'labels'=> (object)array('name' => 'Categories'));
		$args = wp_parse_args( $this->callback_args, array(
			'page' => 1,
			'number' => 20,
			'search' => '',
			'hide_empty' => 0
		) );

		extract( $args, EXTR_SKIP );

		$args['offset'] = $offset = ( $page - 1 ) * $number;

		// convert it to table rows
		$out = '';
		$count = 0;


		if(sizeof($themes) > 0){
			foreach($themes as $theme){
				$this->single_row($theme);
			}
		}
		if ( empty( $fields ) ) {
			list( $columns, $hidden ) = $this->get_column_info();
			echo '<tr class="no-items"><td class="colspanchange" colspan="' . $this->get_column_count() . '">';
			$this->no_items();
			echo '</td></tr>';
		} else {
			echo $out;
		}
	}

	function single_row( $field, $level = 0 ) {
		static $row_class = '';
		global $AjaxyLiveSearch;
		$theme = $AjaxyLiveSearch->get_style_setting('theme', false);
		
		$add_class = ($field['title'] == $theme ? 'row-yes':'row-no');
		$row_class = ( $row_class == '' ? ' class="alternate '.$add_class.'"' : ' class="'.$add_class.'"' );
		
		echo '<tr id="type-' . $field['name'] . '"' . $row_class . '>';
		echo $this->single_row_columns( $field );
		echo '</tr>';
	}

	function column_cb( $field ) {
		return '<input type="checkbox" name="apply_theme[]" value="0" />';
	}
	function column_title( $field ) {
		global $AjaxyLiveSearch;
		$pad = str_repeat( '&#8212; ', max( 0, $this->level ) );
		$name = $pad . ' ' . $field['title'];

		$edit_link = menu_page_url('ajaxy_sf_admin', false).'&tab=themes&theme='.$field['title'].'&apply=1';
		$edit_link = wp_nonce_url( $edit_link, 'hide-post_type_' .$field['title'] ) ;
		$out = '<strong><a class="row-title" href="' . $edit_link . '" title="' . esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $name ) ) . '">' . $name . '</a></strong><br />';

		$actions = array();
		$theme = $AjaxyLiveSearch->get_style_setting('theme', false);
		if ($theme != $field['title'] ):
			$actions['apply'] = "<a class='hide-field' href='" . $edit_link . "'>" . __( 'Apply theme' ) . "</a>";	
		else :
			$actions['apply'] =  __( 'Current theme' );		
		endif;
		$out .= $this->row_actions( $actions );
		$out .= '<div class="hidden" id="inline_' . $field['title'] . '">';
		$out .= '<div class="name">' . $field['title'] . '</div>';

		return $out;
	}
	function column_theme_name( $field ) {
		return $field['name'];
	}
	function column_directory( $field ) {
		return $field['dir'];
	}
	function column_stylesheet_url( $field ) {
		return '<a target="_blank" href="'.$field['stylesheet_url'].'">'.$field['stylesheet_url'].'</a>';
	}
	function column_default( $field, $column_name ) {
		return $field[$column_name];
	}

	/**
	 * Outputs the hidden row displayed when inline editing
	 *
	 * @since 3.1.0
	 */
	function inline_edit() {
	}
}

?>
