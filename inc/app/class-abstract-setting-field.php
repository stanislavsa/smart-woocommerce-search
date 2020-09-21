<?php

namespace YMAPP;

if ( ! class_exists( 'YMAPP\Abstract_Setting_Field' ) ) :
	/**
	 * Class Abstract_Setting_Field
	 * @author YummyWP
	 */
	class Abstract_Setting_Field {

		/**
		 * @var null
		 */
		private static $_instance = null;

		/**
		 * Constructor.
		 */
		private function __construct(){}

		/**
		 * Cloning is not allowed
		 */
		private function __clone() {}

		/**
		 * Get instance
		 * @return null|Abstract_Setting
		 */
		public static function init() {
			if ( null === self::$_instance ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/** Check args and retrieve setting html output
		 * @param $id
		 * @param $args
		 */
		public function get_setting_html( $id, $args ) {

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

			if ( method_exists( $this, 'get_' . $args['type'] . '_html' ) ) {
				?>
				<tr valign="top">
					<th scope="row">
						<?php echo esc_html( $args['title'] ); ?>
					</th>
					<td>
						<fieldset class="<?php echo esc_attr( $args['class'] ); ?>">
							<legend class="screen-reader-text"><span><?php echo esc_html( $args['title'] ); ?></span></legend>
							<?php echo $this->{'get_' . $args['type'] . '_html'}( $id, $args ); /* xss ok */ ?>
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
		public function get_text_html( $id, $args ) {

			ob_start();

			?>
			<label for="<?php echo esc_attr( $id ); ?>">
				<input value="<?php echo esc_attr( $args['value'] ); ?>"
				       placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>" <?php disabled( $args['disabled'], true ); ?>
				       type="<?php echo esc_attr( $args['type'] ); ?>"
				       class="code" name="<?php echo esc_attr( $args['name'] ); ?>"
				       id="<?php echo esc_attr( $id ); ?>" />
			</label>
			<p class="description">
				<?php $this->get_description( $args ); ?>
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
		public function get_checkbox_html( $id, $args ) {

			ob_start();

			?>
			<input type="hidden" name="<?php echo esc_attr( $args['name'] ); ?>" value="<?php echo (int) $args['value']; ?>">
			<input value="1" <?php checked( (int) $args['value'], 1 ); ?> <?php disabled( $args['disabled'], true ); ?>
			       type="<?php echo esc_attr( $args['type'] ); ?>"
			       id="<?php echo esc_attr( $id ); ?>" class="ymapp-switcher" />
			<label for="<?php echo esc_attr( $id ); ?>">
				<?php $this->get_description( $args ); ?>
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
		public function get_select_html( $id, $args ) {

			ob_start();

			?>
			<select class="select" name="<?php echo esc_attr( $args['name'] ); ?>" id="<?php echo esc_attr( $id ); ?>" <?php disabled( $args['disabled'], true ); ?>>
				<?php foreach ( $args['choices'] as $key => $value ) { ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, esc_attr( $args['value'] ) ); ?>><?php echo esc_html( $value ); ?></option>
				<?php } ?>
			</select>
			<p class="description">
				<?php $this->get_description( $args ); ?>
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
		public function get_textarea_html( $id, $args ) {

			ob_start();

			?>
			<textarea rows="3" cols="20" class="input-text wide-input"
			          type="<?php echo esc_attr( $args['type'] ); ?>" name="<?php echo esc_attr( $args['name'] ); ?>" id="<?php echo esc_attr( $id ); ?>"
			          placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>" <?php disabled( $args['disabled'], true ); ?>><?php echo esc_textarea( $args['value'] ); ?></textarea>
			<p class="description">
				<?php $this->get_description( $args ); ?>
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
		public function get_color_html( $id, $args ) {

			ob_start();

			?>
			<input class="ymapp-color-picker" type="text" name="<?php echo esc_attr( $args['name'] ); ?>" id="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $args['value'] ); ?>" />
			<p class="description">
				<?php $this->get_description( $args ); ?>
			</p>
			<?php

			return ob_get_clean();
		}

		/**
		 * Retrieve image uploader html
		 * @param $id
		 * @param $args
		 * @return string
		 */
		public function get_image_html( $id, $args ) {

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
					<?php esc_html_e( 'No image selected', 'yummywp-app' ); ?><input type="button" class="ymapp-button-small ymapp-button-grey image-add" value="<?php esc_html_e( 'Select', 'yummywp-app' ); ?>">
				</div>
			</div>
			<div class="clear"></div>
			<p class="description">
				<?php $this->get_description( $args ); ?>
			</p>
			<?php

			return ob_get_clean();
		}

		/**
		 * Get setting description text
		 *
		 * @param array $args
		 * @return string
		 */
		protected function get_description( $args = array() ) {
			if ( ! empty( $args['description'] ) ) {
				echo wp_kses(
					$args['description'],
					array(
						'img' => array(
							'src' => 1,
							'style' => 1,
							'class' => 1,
						),
						'i' => array(
							'class' => 1,
						),
						'strong' => array(
							'class' => 1,
						),
					)
				);
			}

		}

		/**
		 * @param $value
		 * @return string
		 */
		public function validate_text( $value ) {

			$value = trim( $value );
			$value = strip_tags( stripslashes( $value ) );

			return $value;
		}

		/**
		 * @param $value
		 * @return bool|string
		 */
		public function validate_color( $value ) {

			$value = trim( $value );
			$value = strip_tags( stripslashes( $value ) );

			if ( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) {
				return $value;
			}

			return false;
		}

	}
endif;
