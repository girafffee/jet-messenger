<?php
namespace JET_MSG\Admin\Pages;

/**
 * Base dashboard page
 */
abstract class Base {

	/**
	 * Page slug
	 * @return string
	 */
	abstract public function slug();

	/**
	 * Page title
	 * @return string
	 */
	abstract public function title();

	/**
	 * Page render funciton
	 * @return void
	 */
	abstract public function render();

	/**
	 * Return page config array
	 *
	 * @return [type] [description]
	 */
	abstract public function page_config();

	/**
	 * Page specific assets
	 *
	 * @return [type] [description]
	 */
	public function assets() {
	}

	/**
	 * Check if is setup page
	 *
	 * @return boolean [description]
	 */
	public function is_main_page() {
		return false;
	}

	public function get_icon() {
		if ( ! $this->is_main_page() ) {
			return;
		}

		return 'dashicons-format-status';
	}

	/**
	 * Check if is settings page
	 *
	 * @return boolean [description]
	 */
	public function is_settings_page() {
		return false;
	}

	/**
	 * Page components templates
	 *
	 * @return [type] [description]
	 */
	public function vue_templates() {
		return array();
	}

	/**
	 * Render vue templates
	 *
	 * @return [type] [description]
	 */
	public function render_vue_templates() {
		foreach ( $this->vue_templates() as $template ) {
			if ( is_array( $template ) ) {
				$this->render_vue_template( $template['file'], $template['dir'] );
			} else {
				$this->render_vue_template( $template );
			}
		}
	}

	/**
	 * Render vue template
	 *
	 * @return [type] [description]
	 */
	public function render_vue_template( $template, $path = null ) {

		if ( ! $path ) {
			$path = $this->slug();
		}

		$file = jet_msg()->plugin_path() . 'templates/admin/' . $path . '/' . $template . '.php';

		if ( ! is_readable( $file ) ) {
			return;
		}		

		ob_start();
		include $file;
		$content = ob_get_clean();

		printf(
			'<script type="text/x-template" id="jet-msg-%1$s">%2$s</script>',
			$template,
			$content
		);

	}

	/**
	 * Enqueue script
	 *
	 * @param  [type] $handle    [description]
	 * @param  [type] $file_path [description]
	 * @return [type]            [description]
	 */
	public function enqueue_script( $handle = null, $file_path = null ) {
		//die( jet_msg()->plugin_basename() );

		wp_enqueue_script(
			$handle,
			jet_msg()->plugin_url() . 'assets/js/' . $file_path,
			array( 'wp-api-fetch' ),
			jet_msg()->version(),
			true
		);

	}

	/**
	 * Enqueue style
	 *
	 * @param  [type] $handle    [description]
	 * @param  [type] $file_path [description]
	 * @return [type]            [description]
	 */
	public function enqueue_style( $handle = null, $file_path = null ) {

		wp_enqueue_style(
			$handle,
			jet_msg()->plugin_url() . 'assets/css/' . $file_path,
			array(),
			jet_msg()->version() . time()
		);

	}

	/**
	 * Set to true to hide page from admin menu
	 * @return boolean [description]
	 */
	public function is_hidden() {
		return false;
	}

	/**
	 * Returns current page url
	 *
	 * @return [type] [description]
	 */
	public function get_url() {
		return add_query_arg(
			array( 'page' => $this->slug() ),
			esc_url( admin_url( 'admin.php' ) )
		);
	}

    public function get_active_bots_for_select() {
        if ( ! jet_msg()->installer->checks_is_exists_secondary() ) return [];

        $bots = jet_msg()->general_options->get_active_bots();

        if ( empty( $bots ) ) return [];

        $rows = [];
        foreach ( $bots as $bot ) {
            $row[ 'label' ] = $bot[ 'bot_name' ];
            $row[ 'value' ] = $bot[ 'id' ];

            $rows[ $bot[ 'id' ] ] = $row;
        }

        return $rows;
    }

    public function prepare_for_js_select( $fields ) {
        $select = [];
        foreach ( $fields as $value => $field_name ) {
            if ( ! is_array( $field_name ) ) {
                $select[] = [
                    'label' => $field_name,
                    'value' => $value
                ];
            }
            else {
                $row[ 'value' ] = $value;

                foreach ($field_name as $key => $option) {
                    $row[ $key ] = $option;
                }
                $select[ $value ] = $row;
            }

        }
        return $select;
    }

}
