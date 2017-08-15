<?php

namespace Dwnload\WPSettingsApi\Api;

use Dwnload\WPSettingsApi\Settings\FieldTypes;

/**
 * Class SettingField
 *
 * @package Dwnload\WPSettingsApi\Api
 */
class SettingField {

    /**
     * Array key values.
     */

    const ID = 'id';
    const CLASS_OBJECT = 'class_object';
    const DEFAULT = 'default';
    const DESC = 'description';
    const LABEL = 'label';
    const NAME = 'name';
    const OPTIONS = 'options';
    const SANITIZE = 'sanitize_callback';
    const SECTION_ID = 'section_id';
    const SIZE = 'size';
    const TYPE = 'type';
    const FIELD_OBJECT = SettingField::class;

    /**
     * SettingField constructor.
     *
     * @param array $properties
     */
    public function __construct( array $properties = [] ) {
        if ( ! empty( $properties ) ) {
            foreach ( $properties as $key => $value ) {
                $this->__set( $key, $value );
            }
        }
    }

    /**
     * Magic setter.
     *
     * @param string $key The property
     * @param mixed $value The value of the property
     */
    public function __set( string $key, $value ) {
        if ( property_exists( $this, $key ) ) {
            $this->$key = $value;
        }
    }

    /**
     * The Fields description.
     *
     * @var string $description
     */
    private $description;

    /**
     * Sets the Fields description.
     *
     * @param string $description
     */
    public function setDescription( string $description ) {
        $this->description = $description;
    }

    /**
     * Gets the Fields description.
     *
     * @return string
     */
    public function getDescription() : string {
        return $this->description ?? '';
    }

    /**
     * The Fields default value.
     *
     * @var mixed $default
     */
    private $default;

    /**
     * Sets the Fields default value.
     *
     * @param string $default
     */
    public function setDefault( $default ) {
        $this->default = $default;
    }

    /**
     * Gets the Fields default value.
     *
     * @return mixed
     */
    public function getDefault() {
        return $this->default;
    }

    /**
     * The Fields label (description).
     *
     * @var string $label
     */
    private $label;

    /**
     * Sets the Fields label (description).
     *
     * @var string $label
     */
    public function setLabel( string $label ) {
        $this->label = $label;
    }

    /**
     * Gets Fields label (description).
     *
     * @return string
     */
    public function getLabel() : string {
        return $this->label;
    }

    /**
     * The Fields name.
     * - used internally.
     *
     * @var string $name
     */
    private $name;

    /**
     * Sets the Fields name.
     *
     * @param string $name
     */
    public function setName( string $name ) {
        $this->name = $name;
    }

    /**
     * Gets the Fields name.
     *
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * Gets the Fields ID (AKA name).
     *
     * @return string
     */
    public function getId() : string {
        return $this->name;
    }

    /**
     * The Fields callback object.
     * - if null the Field type name needs to have a method in the FieldTypes class.
     *
     * @var object $class_object
     */
    private $class_object;

    /**
     * Set the Fields output object.
     *
     * @param $class_object
     */
    public function setClassObject( $class_object ) {
        $this->class_object = $class_object;
    }

    /**
     * Get the Fields output object.
     */
    public function getClassObject() {
        return $this->class_object;
    }

    /**
     * The Fields options array.
     * - used for html array options like radios, checkboxes, etc.
     *
     * @var array $options
     */
    private $options = [];

    /**
     * Set the Fields options array.
     * - optional
     *
     * @param array $options
     */
    public function setOptions( array $options ) {
        $this->options = $options;
    }

    /**
     * Get the Fields options array.
     *
     * @return array
     */
    public function getOptions() : array {
        return $this->options;
    }

    /**
     * The Fields sanitize method callback.
     *
     * @var callable $sanitize_callback
     */
    private $sanitize_callback;

    /**
     * Set the Fields sanitize callback.
     * - optional
     *
     * @param callable $sanitize
     */
    public function setSanitizeCallback( callable $sanitize ) {
        $this->sanitize_callback = $sanitize;
    }

    /**
     * Get the sanitize callback (if set).
     *
     * @return null|callable
     */
    public function getSanitizeCallback() {
        return $this->sanitize_callback;
    }

    /**
     * The Fields Section it's attached to.
     *
     * @var string $section_id
     */
    private $section_id;

    /**
     * Set the Fields Section ID.
     * - optional
     *
     * @param string $section_id
     */
    public function setSectionId( string $section_id ) {
        $this->section_id = $section_id;
    }

    /**
     * Get the Fields Section ID.
     *
     * @return string
     */
    public function getSectionId() : string {
        return $this->section_id;
    }

    /**
     * The Fields CSS size attribute.
     *
     * @var string $size
     */
    private $size;

    /**
     * Set the Fields size.
     *
     * @param string $size
     */
    public function setSize( string $size ) {
        $this->size = $size;
    }

    /**
     * Get the Fields size.
     *
     * @return string
     */
    public function getSize() : string {
        return $this->size ?? FieldTypes::DEFAULT_SIZE;
    }

    /**
     * The Fields input type.
     * - optional
     *
     * @var string $type
     */
    private $type = 'text';

    /**
     * Set the Fields input type (defaults to 'text').
     *
     * @param string $type
     */
    public function setType( string $type ) {
        $this->type = $type;
    }

    /**
     * Get the Fields type.
     *
     * @return string
     */
    public function getType() : string {
        return $this->type;
    }

    /**
     * The Fields name.
     *
     * @var bool $obfuscated
     */
    private $obfuscated = false;

    /**
     * Set the Fields input type (defaults to 'text').
     *
     * @param bool $obfuscated
     */
    public function setObfuscate( bool $obfuscated = true ) {
        $this->obfuscated = $obfuscated;
    }

    /**
     * @return bool
     */
    public function isObfuscated(): bool {
        return $this->obfuscated;
    }
}
