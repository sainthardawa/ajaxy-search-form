<?php
/**
 * List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 * @access private
 */
 
class WP_SF_List_Table extends WP_List_Table {

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
		$actions['hide'] = __( 'Hide from results' );
		$actions['show'] = __( 'Show in results' );

		return $actions;
	}

	function current_action() {
		if ( isset( $_REQUEST['action'] ) && ( 'hide' == $_REQUEST['action'] || 'hide' == $_REQUEST['action2'] ) )
			return 'bulk-hide';

		return parent::current_action();
	}

	function get_columns() {
		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'post_type'    => __( 'Template post type' ),
			'title'        => __( 'Title' ),
			'search_setting'=> __( 'Search setting' ),
			'show_on_search' => __( 'Search' ),
			'limit_results' => __( 'Limit' ),	
			'order'        	=> __( 'Order' )
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
		$orderby = 'order';
		$order = (in_array($_REQUEST['order'], array('asc','desc')) ? $_REQUEST['order']: 'asc');
		switch($_REQUEST['orderby']){
			case 'n':
				$orderby = 'name';
				break;
			case 'o':
				$orderby = 'order';
				break;
			case 'p':
				$orderby = 'post_type';
				break;
			default:
				$orderby = 'order';
				break;
		}

		if(!empty($fields)){
			foreach($fields as $post_type){
				$this->single_row($post_type);
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
		$setting = (array)$AjaxyLiveSearch->get_setting($field->name);
		$add_class = ($setting['show'] == 1 ? 'row-yes':'row-no');
		$row_class = ( $row_class == '' ? ' class="alternate '.$add_class.'"' : ' class="'.$add_class.'"' );
		
		echo '<tr id="type-' . $field->name . '"' . $row_class . '>';
		echo $this->single_row_columns( $field );
		echo '</tr>';
	}

	function column_cb( $field ) {
		return '<input type="checkbox" name="template_id[]" value="'.$field->name.'" />';
	}
	function column_title( $field ) {
		global $AjaxyLiveSearch;
		$setting = (array)$AjaxyLiveSearch->get_setting($field->name);
		return $setting['title'];
	}
	function column_show_on_search( $field ) {
		global $AjaxyLiveSearch;
		$setting = (array)$AjaxyLiveSearch->get_setting($field->name);
		return ($setting['show'] == 1 ? 'Yes':'No');
	}
	function column_search_setting( $field ) {
		global $AjaxyLiveSearch;
		$setting = (array)$AjaxyLiveSearch->get_setting($field->name);
		return ($setting['search_content'] == 0 ? 'Only title':'both title and content');
	}
	function column_limit_results( $field ) {
		global $AjaxyLiveSearch;
		$setting = (array)$AjaxyLiveSearch->get_setting($field->name);
		return $setting['limit'];
	}
	function column_order( $field ) {
		global $AjaxyLiveSearch;
		$setting = (array)$AjaxyLiveSearch->get_setting($field->name);
		return $setting['order'];
	}
	function column_post_type( $field ) {
		global $AjaxyLiveSearch;
		$pad = str_repeat( '&#8212; ', max( 0, $this->level ) );
		$name = $pad . ' ' . $field->labels->name;;

		$edit_link = menu_page_url('ajaxy_sf_admin', false).'&type='.$field->name.'&edit=1';

		$out = '<strong><a class="row-title" href="' . $edit_link . '" title="' . esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $name ) ) . '">' . $name . '</a></strong><br />';

		$actions = array();

		$actions['edit'] = '<a href="' . $edit_link . '">' . __( 'Edit template & Settings' ) . '</a>';
		//$actions['inline hide-if-no-js'] = '<a href="#" class="editinline">' . __( 'Quick&nbsp;Edit' ) . '</a>';
		$setting = (array)$AjaxyLiveSearch->get_setting($field->name);
		if ($setting['show'] == 1 ):
			$actions['hide'] = "<a class='hide-field' href='" . wp_nonce_url( menu_page_url('ajaxy_sf_admin', false).'&amp;type='.$field->name.'&amp;show=0&amp;tab=templates', 'hide-post_type_' .$field->name ) . "'>" . __( 'Hide from results' ) . "</a>";
		else:
			$actions['show'] = "<a class='show-field' href='" . wp_nonce_url( menu_page_url('ajaxy_sf_admin', false).'&amp;type='.$field->name.'&amp;show=1&amp;tab=templates', 'show-post_type_' .$field->name ) . "'>" . __( 'show in results' ) . "</a>";
		endif;
		$out .= $this->row_actions( $actions );
		$out .= '<div class="hidden" id="inline_' . $field->name . '">';
		$out .= '<div class="name">' . $field->labels->name. '</div>';

		return $out;
	}
	function column_default( $field, $column_name ) {
		return $field->{$column_name};
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
