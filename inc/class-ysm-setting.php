<?php
/**
 * Class Ysm_Setting
 * @author YummyWP
 */
class Ysm_Setting
{

	/**
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Ysm_Setting constructor.
	 */
	private function __construct(){}

	/**
	 * Cloning is not allowed
	 */
	private function __clone() {}

	/**
	 * Get instance
	 * @return null|Ysm_Setting
	 */
	public static function init()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/** Check args and retrieve setting html output
	 * @param $id
	 * @param $args
	 */
	public function get_setting_html($id, $args)
	{

		if ( isset( $args['choices'] ) ) {
			$args['choices'] = (array) $args['choices'];
		}

		$defaults = array(
			'type'              => 'text',
			'title'             => '',
			'description'       => '',
			'placeholder'       => '',
			'choices'           => array(),
			'value'             => '',
			'disabled'          => false,
			'class'             => '',
			'callback'          => null,
			'name'              => 'settings[' . $id . ']',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( method_exists( $this, 'get_'.$args['type'].'_html' ) ) {
			?>
			<tr valign="top">
				<th scope="row">
					<?php echo wp_kses_post( $args['title'] ); ?>
				</th>
				<td>
					<fieldset class="sm-setting <?php echo esc_attr( $args['class'] ); ?>">
						<legend class="screen-reader-text"><span><?php echo wp_kses_post( $args['title'] ); ?></span></legend>
						<?php echo $this->{'get_'.$args['type'].'_html'}($id, $args); ?>
					</fieldset>
				</td>
			</tr>
			<?php
		}

	}

	/**
	 * Retrieve text <input> html
	 * @param $id
	 * @param $args
	 * @return string
	 */
	public function get_text_html($id, $args)
	{

		ob_start();

		if ( is_array( $args['value'] ) ) {
			$args['value'] = implode( ',', $args['value'] );
		}
		?>
		<label for="<?php echo esc_attr( $id ); ?>">
			<input value="<?php echo esc_attr( $args['value'] ); ?>" placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>" <?php disabled($args['disabled'], true); ?>
			       type="<?php echo esc_attr( $args['type'] ); ?>" class="code" name="<?php echo esc_attr( $args['name'] ); ?>" id="<?php echo esc_attr( $id ); ?>" />
		</label>
		<p class="description">
			<?php echo wp_kses_post( $args['description'] ); ?>
		</p>
		<?php

		return ob_get_clean();
	}

	/**
	 * Retrieve <checkbox> html
	 * @param $id
	 * @param $args
	 * @return string
	 */
	public function get_checkbox_html($id, $args)
	{

		ob_start();

		?>
		<label for="<?php echo esc_attr( $id ); ?>">
			<input value="1" <?php checked( (int) $args['value'], 1 ); ?> <?php disabled($args['disabled'], true); ?>
			       type="<?php echo esc_attr( $args['type'] ); ?>" name="<?php echo esc_attr( $args['name'] ); ?>" id="<?php echo esc_attr( $id ); ?>" />
			<?php echo wp_kses_post( $args['description'] ); ?>
		</label>
		<?php

		return ob_get_clean();
	}

	/**
	 * Retrieve <select> html
	 * @param $id
	 * @param $args
	 * @return string
	 */
	public function get_select_html($id, $args)
	{

		ob_start();

		?>
		<select class="select" name="<?php echo esc_attr( $args['name'] ); ?>" id="<?php echo esc_attr( $id ); ?>" <?php disabled( $args['disabled'], true ); ?>>
			<?php foreach ( $args['choices'] as $key => $value ) { ?>
				<?php
				$args['value'] = (array) $args['value'];
				$args['value'] = array_map( 'esc_attr', $args['value'] );
				$selected = in_array( $key, $args['value'] ) ? 'selected' : '';
				?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $value ); ?></option>
			<?php } ?>
		</select>
		<?php

		return ob_get_clean();
	}

	/**
	 * Retrieve <textarea> html
	 * @param $id
	 * @param $args
	 * @return string
	 */
	public function get_textarea_html($id, $args)
	{

		ob_start();

		?>
		<textarea rows="3" cols="20" class="input-text wide-input"
		          type="<?php echo esc_attr( $args['type'] ); ?>" name="<?php echo esc_attr( $args['name'] ); ?>" id="<?php echo esc_attr( $id ); ?>"
		          placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>" <?php disabled( $args['disabled'], true ); ?>><?php echo esc_textarea( $args['value'] ); ?></textarea>
		<p class="description">
			<?php echo wp_kses_post( $args['description'] ); ?>
		</p>
		<?php

		return ob_get_clean();
	}

	/**
	 * Retrieve colorpicker html
	 * @param $id
	 * @param $args
	 * @return string
	 */
	public function get_color_html($id, $args)
	{

		ob_start();

		?>
		<input class="sm-color-picker" type="text" name="<?php echo esc_attr( $args['name'] ); ?>" id="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $args['value'] ); ?>" />
		<p class="description">
			<?php echo wp_kses_post( $args['description'] ); ?>
		</p>
		<?php

		return ob_get_clean();
	}

	/**
	 * @param $value
	 * @return string
	 */
	public function validate_text($value )
	{

		$value = trim( $value );
		$value = strip_tags( stripslashes( $value ) );

		return $value;
	}

	/**
	 * @param $value
	 * @return bool|string
	 */
	public function validate_color($value )
	{

		$value = trim( $value );
		$value = strip_tags( stripslashes( $value ) );

		if ( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) {
			return $value;
		}

		return false;
	}

}
