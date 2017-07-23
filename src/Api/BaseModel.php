<?php

namespace Dwnload\WPSettingsApi\Api;

/**
 * Class BaseModel
 *
 * @package Dwnload\WPSettingsApi\Setting
 */
abstract class BaseModel {

    /**
     * BaseModel constructor.
     *
     * @param array $fields
     */
    public function __construct( array $fields ) {
        $this->populate( $fields );
    }

    /**
     * Populate model
     *
     * @param array $fields
     */
    protected function populate( array $fields ) {
        foreach ( $fields as $field => $value ) {
            // If field value is null we just leave it blank
            if ( is_null( $value ) ) {
                continue;
            }

            $setter_method = 'set' . ucfirst( $field );

            if ( method_exists( $this, $setter_method ) ) {
                $this->$setter_method( $value );
            }
        }
    }
}
