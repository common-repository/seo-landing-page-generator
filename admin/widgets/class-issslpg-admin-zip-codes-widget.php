<?php
/**
 * Zip Codes Widget
 */



/**
 * Class ISSSLPG_Admin_Zip_Codes_Widget
 */
class ISSSLPG_Admin_Zip_Codes_Widget extends WP_Widget
{
	/** Basic Widget Settings */
	const WIDGET_NAME = "ISS: Zip Codes";
	const WIDGET_DESCRIPTION = "Displays the landing page's locations zip codes.";

	var $textdomain;
	var $fields;

	/**
	 * Construct the widget
	 */
	function __construct()
	{
		// We're going to use $this->textdomain as both the translation domain and the widget class name and ID
		$this->textdomain = strtolower( get_class( $this ) );

		// Add fields
		$this->add_field( 'title', 'Title', '', 'text' );

		// Translations
		load_plugin_textdomain( $this->textdomain, false, basename( dirname( __FILE__ ) ) . '/languages' );

		// Init the widget
		parent::__construct( $this->textdomain, __( self::WIDGET_NAME, $this->textdomain ), array(
			'description' => __( self::WIDGET_DESCRIPTION, $this->textdomain ),
			'classname'   => $this->textdomain
		) );
	}

	/**
	 * Widget frontend
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance )
	{
		// Only output widget in landing pages
		if ( ! ISSSLPG_Landing_Page::is_landing_page() ) {
			return;
		}

		extract( $instance );

		$title  = apply_filters( 'widget_title', $instance['title'] );
		if ( ! empty( $title ) ) {
			$title = $args['before_title'] . do_shortcode( $title ) . $args['after_title'];
		}
		?>

		<?php
		/**
		 * Widget frontend output
		 */
		?>

		<?php echo $args['before_widget']; ?>

		<?php echo $title; ?>

		<?php
		$location = new ISSSLPG_Public_Location();
		$zip_codes = $location->zip_codes;

		if ( ! $zip_codes ) {
			return;
		}

		shuffle( $zip_codes );
		$zip_codes = array_slice( $zip_codes, 0, 10 );
		$zip_codes = join( ', ', $zip_codes );
		echo $zip_codes;
		?>

		<?php echo $args['after_widget']; ?>

		<?php
	}

	/**
	 * Widget backend
	 *
	 * @param array $instance
	 *
	 * @return string|void
	 */
	public function form( $instance )
	{
		/* Generate admin for fields */
		foreach ( $this->fields as $field_name => $field_data ) {
			if ( $field_data['type'] === 'text' ):
				?>
				<p>
					<label for="<?php echo $this->get_field_id( $field_name ); ?>">
						<?php _e( $field_data['description'], $this->textdomain ); ?>
					</label>
					<input class="widefat"
					       id="<?php echo $this->get_field_id( $field_name ); ?>"
					       name="<?php echo $this->get_field_name( $field_name ); ?>"
					       type="text"
					       value="<?php echo esc_attr( isset( $instance[ $field_name ] ) ? $instance[ $field_name ] : $field_data['default_value'] ); ?>"
					/>
				</p>
				<?php
			elseif($field_data['type'] == 'textarea'):
				?>
				<p>
					<label for="<?php echo $this->get_field_id( $field_name ); ?>">
						<?php _e( $field_data['description'], $this->textdomain ); ?>
					</label>
                    <textarea class="widefat" rows="10" cols="20"
                              id="<?php echo $this->get_field_id( $field_name ); ?>"
                              name="<?php echo $this->get_field_name( $field_name ); ?>"><?php echo esc_attr( isset( $instance[ $field_name ] ) ? $instance[ $field_name ] : $field_data['default_value'] ); ?></textarea>
				</p>
				<?php
			else:
				echo __( 'Error - Field type not supported', $this->textdomain ) . ': ' . $field_data['type'];
			endif;
		}
	}

	/**
	 * Adds field to the widget
	 *
	 * @param $field_name
	 * @param string $field_description
	 * @param string $field_default_value
	 * @param string $field_type
	 */
	private function add_field( $field_name, $field_description = '', $field_default_value = '', $field_type = 'text' )
	{
		if ( ! is_array( $this->fields ) ) {
			$this->fields = array();
		}

		$this->fields[ $field_name ] = array(
			'name'          => $field_name,
			'description'   => $field_description,
			'default_value' => $field_default_value,
			'type'          => $field_type
		);
	}

	/**
	 * Updating widget by replacing the old instance with new
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance )
	{
		return $new_instance;
	}

}