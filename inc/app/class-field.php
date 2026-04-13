<?php
namespace YummyWP\App;

/**
 * Field manager
 * @author YummyWP
 */
class Field {

	/**
	 * Constructor.
	 */
	private function __construct() {}

	/**
	 * Cloning is not allowed
	 */
	private function __clone() {}

	/**
	 * Check args and output field html
	 * @param $id
	 * @param $args
	 */
	public static function output( $id, $args ) {

		if ( isset( $args['choices'] ) ) {
			$args['choices'] = (array) $args['choices'];
		}

		$defaults = array(
			'type'              => 'text',
			'title'             => '',
			'description'       => '',
			'placeholder'       => '',
			'is_pro'            => false,
			'choices'           => array(),
			'fields'            => array(),
			'value'             => '',
			'disabled'          => false,
			'multiple'          => false,
			'class'             => '',
			'callback'          => null,
			'name'              => 'settings[' . $id . ']',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( $args['is_pro'] && ! sws_fs()->is_premium() ) {
			$args['type'] = 'pro';
		}

		if ( method_exists( __CLASS__, 'get_' . $args['type'] . '_html' ) ) {
			?>
			<tr valign="top">
				<th scope="row">
					<?php echo wp_kses_post( $args['title'] ); ?>
				</th>
				<td>
					<fieldset class="<?php echo esc_attr( $args['class'] ); ?>">
						<legend class="screen-reader-text"><span><?php echo wp_kses_post( $args['title'] ); ?></span></legend>
						<?php echo self::{'get_' . $args['type'] . '_html'}( $id, $args ); /* @codingStandardsIgnoreLine */ ?>
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
	public static function get_text_html( $id, $args ) {
		ob_start();
		?>
		<label for="<?php echo esc_attr( $id ); ?>">
			<input value="<?php echo esc_attr( $args['value'] ); ?>" placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>" <?php disabled( $args['disabled'], true ); ?>
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
	public static function get_checkbox_html($id, $args) {
		ob_start();
		?>
		<input type="hidden" name="<?php echo esc_attr( $args['name'] ); ?>" value="<?php echo (int) $args['value']; ?>">
		<input value="1" <?php checked( (int) $args['value'], 1 ); ?> <?php disabled( $args['disabled'], true ); ?>
			   type="<?php echo esc_attr( $args['type'] ); ?>"
			   id="<?php echo esc_attr( $id ); ?>" class="ymapp-switcher" />
		<label for="<?php echo esc_attr( $id ); ?>">
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
	public static function get_select_html( $id, $args ) {
		if ( ! is_array( $args['value'] ) ) {
			$args['value'] = explode( ',', $args['value'] );
			$args['value'] = array_map( 'trim', $args['value'] );
		}

		ob_start();
		?>
		<select class="select" name="<?php echo esc_attr( $args['name'] ); ?>[]" id="<?php echo esc_attr( $id ); ?>" <?php disabled( $args['disabled'], true ); ?> <?php echo $args['multiple'] ? 'multiple' : ''; ?>>
			<?php foreach ( $args['choices'] as $key => $value ) { ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php echo in_array( $key, $args['value'] ) ? 'selected' : ''; ?>><?php echo esc_html( $value ); ?></option>
			<?php } ?>
		</select>
		<p class="description">
			<?php
			echo wp_kses(
				$args['description'],
				[
					'img' => [ 'src' => 1, 'style' => 1, 'class' => 1 ],
					'br' => [],
				]
			);
			?>
		</p>
		<?php

		return ob_get_clean();
	}

	/**
	 * Retrieve <textarea> html
	 * @param $id
	 * @param $args
	 * @return string
	 */
	public static function get_textarea_html( $id, $args ) {
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
	 * Retrieve <textarea> html
	 * @param $id
	 * @param $args
	 * @return string
	 */
	public static function get_textarea_list_html( $id, $args ) {
		ob_start();
		$value = $args['value'];
		if ( false !== strpos( $value, ',' ) ) {
			$value = explode( ',', $value );
			$value = implode( "\n", $value );
		}
		?>
		<textarea rows="10" cols="40" class="input-text wide-input"
				  type="<?php echo esc_attr( $args['type'] ); ?>" name="<?php echo esc_attr( $args['name'] ); ?>" id="<?php echo esc_attr( $id ); ?>"
				  placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>" <?php disabled( $args['disabled'], true ); ?>><?php echo esc_textarea( $value ); ?></textarea>
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
	public static function get_color_html( $id, $args ) {
		ob_start();
		?>
		<input class="ymapp-color-picker" type="text" name="<?php echo esc_attr( $args['name'] ); ?>" id="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $args['value'] ); ?>" />
		<p class="description">
			<?php echo wp_kses_post( $args['description'] ); ?>
		</p>
		<?php

		return ob_get_clean();
	}

	/**
	 * Retrieve repeater html
	 * @param $id
	 * @param $args
	 * @return string
	 */
	public static function get_repeater_html( $id, $args ) {
		if ( empty( $args['fields'] ) || ! is_array( $args['fields'] ) ) {
			return '';
		}

		$arr = [];

		foreach ( $args['fields'] as $field_slug => $field ) {
			$arr[ $field_slug ] = [];
			if ( ! empty( $args['value'][ $field_slug ] ) ) {
				$arr[ $field_slug ] = $args['value'][ $field_slug ];
			} else {
				$arr[ $field_slug ] = [ '' ];
			}
		}

		ob_start();
		?>
		<p class="description">
			<?php echo wp_kses_post( $args['description'] ); ?>
		</p>
		<br><br>
		<ul class="repeater-holder" id="repeater-<?php echo esc_attr( $id ); ?>">
			<?php foreach ( current( $arr ) as $arr_key => $arr_val ) { ?>
				<li>
					<span class="repeater-move dashicons"></span>
					<?php foreach ( $args['fields'] as $f_slug => $f_name ) { ?>
						<label><?php echo esc_html( $f_name ); ?> <input name="<?php echo esc_attr( $args['name'] . '[' . $f_slug . '][]' ); ?>" value="<?php echo esc_attr( $arr[ $f_slug ][ $arr_key ] ); ?>" type="text" class="widefat" /></label>
					<?php } ?>
					<span class="repeater-delete dashicons"></span>
				</li>
			<?php } ?>
		</ul>
		<div class="clear"></div>
		<span class="repeater-add ymapp-button-small">Add</span>

		<template id="repeater-<?php echo esc_attr( $id ); ?>-tmpl">
			<li>
				<span class="repeater-move dashicons"></span>
				<?php foreach ( $args['fields'] as $f_slug => $f_name ) { ?>
					<label><?php echo esc_html( $f_name ); ?> <input name="<?php echo esc_attr( $args['name'] . '[' . $f_slug . '][]' ); ?>" value="" type="text" class="widefat" /></label>
				<?php } ?>
				<span class="repeater-delete dashicons"></span>
			</li>
		</template>
		<?php
		return ob_get_clean();
	}

	/**
	 * Retrieve image uploader html
	 * @param $id
	 * @param $args
	 * @return string
	 */
	public static function get_image_html( $id, $args ) {
		ob_start();
		$url = wp_get_attachment_image_src( $args['value'], 'full' );

		if ( $url && is_array( $url ) ) {
			$url = $url[0];
		} else {
			$url = '';
		}
		?>
		<div class="image-uploader<?php echo $url ? ' has-image' : ''; ?>">
			<input type="hidden" name="<?php echo esc_attr( $args['name'] ); ?>" value="<?php echo esc_attr( $args['value'] ); ?>">
			<div class="image-preview">
				<div class="hover">
					<span class="image-delete dashicons dashicons-no-alt"></span>
					<span class="image-edit dashicons dashicons-edit"></span>
				</div>
				<img class="image-preview-img" src="<?php echo esc_url( $url ); ?>" alt="" scale="0">
			</div>
			<div class="no-image">
				<?php esc_html_e( 'No image selected', 'smart-woocommerce-search' ); ?><input type="button" class="ymapp-button-small ymapp-button-grey image-add" value="<?php esc_html_e( 'Select', 'yummywp-app' ); ?>">
			</div>
		</div>
		<div class="clear"></div>
		<p class="description">
			<?php echo wp_kses_post( $args['description'] ); ?>
		</p>
		<?php

		return ob_get_clean();
	}

	/**
	 * Retrieve Pro notification html
	 * @param $id
	 * @param $args
	 * @return string
	 */
	public static function get_pro_html( $id, $args ) {
		ob_start();
		?>
		<p class="description description-pro">
			<?php echo wp_kses_post( $args['description'] ); ?>
			<a href="<?php echo esc_url( sws_fs()->get_upgrade_url() ); ?>" style="color: red;margin-left: 10px;">
				<?php esc_html_e( 'Upgrade to Pro', 'smart-woocommerce-search' ); ?>
			</a>
		</p>
		<?php

		return ob_get_clean();
	}

	/**
	 * Retrieve sortable fields (Relevance Configurator) html
	 * @param $id
	 * @param $args
	 * @return string
	 */
	public static function get_sortable_fields_html( $id, $args ) {
		$all_fields = [
			'title'    => __( 'Title', 'smart-woocommerce-search' ),
			'content'  => __( 'Content', 'smart-woocommerce-search' ),
			'excerpt'  => __( 'Product short description', 'smart-woocommerce-search' ),
			'sku'      => __( 'Product SKU', 'smart-woocommerce-search' ),
//			'category' => __( 'Product Category', 'smart-woocommerce-search' ),
//			'tag'      => __( 'Product Tag', 'smart-woocommerce-search' ),
			'onsale'   => __( 'On Sale Products', 'smart-woocommerce-search' ),
			'featured' => __( 'Featured Products', 'smart-woocommerce-search' ),
		];

		// Parse saved value — JSON-encoded ordered array of keys
		$saved = $args['value'];

		if ( is_string( $saved ) && ! empty( $saved ) ) {
			$decoded = json_decode( stripslashes($saved), true );
			$saved   = is_array( $decoded ) ? $decoded : array_keys( $all_fields );
		} elseif ( ! is_array( $saved ) || empty( $saved ) ) {
			$saved = array_keys( $all_fields );
		}

		// Append any fields not yet in the saved order
		foreach ( array_keys( $all_fields ) as $key ) {
			if ( ! in_array( $key, $saved, true ) ) {
				$saved[] = $key;
			}
		}

		$total = count( $saved );

		ob_start();
		?>
		<ul class="sws-sortable-fields" id="sws-sortable-<?php echo esc_attr( $id ); ?>">
			<?php foreach ( $saved as $index => $field_key ) :
				if ( ! isset( $all_fields[ $field_key ] ) ) continue;
				$weight = ( $total - $index ) * 10;
			?>
				<li class="sws-sortable-fields__item" data-key="<?php echo esc_attr( $field_key ); ?>">
					<span class="sws-sortable-fields__handle dashicons dashicons-move"></span>
					<span class="sws-sortable-fields__label"><?php echo esc_html( $all_fields[ $field_key ] ); ?></span>
					<span class="sws-sortable-fields__weight"><?php echo esc_html( $weight ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
		<input type="hidden"
			   name="<?php echo esc_attr( $args['name'] ); ?>"
			   id="<?php echo esc_attr( $id ); ?>"
			   value="<?php echo esc_attr( wp_json_encode( $saved ) ); ?>" />
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
	public static function validate_text( $value ) {
		$value = trim( $value );
		$value = strip_tags( stripslashes( $value ) );

		return $value;
	}

	/**
	 * @param $value
	 * @return bool|string
	 */
	public static function validate_color( $value ) {
		$value = trim( $value );
		$value = strip_tags( stripslashes( $value ) );

		if ( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) {
			return $value;
		}

		return false;
	}

}
