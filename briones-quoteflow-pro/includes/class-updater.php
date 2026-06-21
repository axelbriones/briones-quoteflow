<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BQF_Pro_Updater {

    private $api_url = BQF_Pro_License::STORE_URL;
    private $item_name = BQF_Pro_License::ITEM_NAME;
    private $version = BQF_PRO_VERSION;
    private $author = 'Briones';

    public function __construct() {
        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
        add_filter( 'plugins_api', array( $this, 'plugin_info' ), 10, 3 );
    }

    public function check_update( $transient ) {
        if ( empty( $transient->checked ) ) {
            return $transient;
        }

        $license = get_option( 'bqf_pro_license_key' );
        $status  = get_option( 'bqf_pro_license_status' );

        if ( $status !== 'valid' ) {
            return $transient; // Do not check for updates if license is invalid
        }

        $api_params = array(
            'edd_action' => 'get_version',
            'license'    => $license,
            'item_name'  => $this->item_name,
            'url'        => home_url()
        );

        $response = wp_remote_post( $this->api_url, array( 'timeout' => 15, 'body' => $api_params ) );

        if ( ! is_wp_error( $response ) ) {
            $license_data = json_decode( wp_remote_retrieve_body( $response ) );
            if ( $license_data && isset( $license_data->new_version ) ) {
                if ( version_compare( $this->version, $license_data->new_version, '<' ) ) {
                    $plugin_file = 'briones-quoteflow-pro/briones-quoteflow-pro.php';

                    $obj = new stdClass();
                    $obj->slug = 'briones-quoteflow-pro';
                    $obj->plugin = $plugin_file;
                    $obj->new_version = $license_data->new_version;
                    $obj->url = $license_data->homepage;
                    $obj->package = $license_data->download_link;

                    $transient->response[ $plugin_file ] = $obj;
                }
            }
        }

        return $transient;
    }

    public function plugin_info( $false, $action, $arg ) {
        if ( isset( $arg->slug ) && $arg->slug === 'briones-quoteflow-pro' ) {
            $license = get_option( 'bqf_pro_license_key' );

            $api_params = array(
                'edd_action' => 'get_version',
                'license'    => $license,
                'item_name'  => $this->item_name,
                'url'        => home_url()
            );

            $response = wp_remote_post( $this->api_url, array( 'timeout' => 15, 'body' => $api_params ) );

            if ( ! is_wp_error( $response ) ) {
                $license_data = json_decode( wp_remote_retrieve_body( $response ) );
                if ( $license_data ) {
                    $info = new stdClass();
                    $info->name = $this->item_name;
                    $info->slug = 'briones-quoteflow-pro';
                    $info->version = $license_data->new_version;
                    $info->author = $this->author;
                    $info->homepage = $license_data->homepage;
                    $info->requires = '5.8';
                    $info->tested = '6.5';
                    $info->downloaded = 0;
                    $info->last_updated = $license_data->last_updated;
                    $info->sections = array(
                        'description' => $license_data->sections->description,
                        'changelog' => $license_data->sections->changelog
                    );
                    $info->download_link = $license_data->download_link;

                    return $info;
                }
            }
        }
        return $false;
    }
}
