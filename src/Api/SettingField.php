<?php declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Api;

use Dwnload\WpSettingsApi\Settings\FieldTypes;

/**
 * Class SettingField
 *
 * @package Dwnload\WpSettingsApi\Api
 * phpcs:disable Inpsyde.CodeQuality.PropertyPerClassLimit.TooMuchProperties
 */
class SettingField
{
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
    public function __construct(array $properties = [])
    {
        if (!empty($properties)) {
            foreach ($properties as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    /**
     * Magic setter.
     *
     * @param string $key The property
     * @param mixed $value The value of the property
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
     */
    public function __set(string $key, $value)
    {
        if (\property_exists($this, $key)) {
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
     * @return $this
     */
    public function setDescription(string $description): SettingField
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Gets the Fields description.
     *
     * @return string
     */
    public function getDescription(): string
    {
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
     * @param mixed $default
     * @return $this
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
     */
    public function setDefault($default): SettingField
    {
        $this->default = $default;
        return $this;
    }

    /**
     * Gets the Fields default value.
     *
     * @return mixed
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     */
    public function getDefault()
    {
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
     * @return $this
     */
    public function setLabel(string $label): SettingField
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Gets Fields label (description).
     *
     * @return string
     */
    public function getLabel(): string
    {
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
     * @return $this
     */
    public function setName(string $name): SettingField
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Gets the Fields name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets the Fields ID (AKA name).
     *
     * @return string
     */
    public function getId(): string
    {
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
     * @return $this
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
     */
    public function setClassObject($class_object): SettingField
    {
        $this->class_object = $class_object;
        return $this;
    }

    /**
     * Get the Fields output object.
     * @return object
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     */
    public function getClassObject()
    {
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
     * @return $this
     */
    public function setOptions(array $options): SettingField
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Get the Fields options array.
     *
     * @return array
     */
    public function getOptions(): array
    {
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
     * @return $this
     */
    public function setSanitizeCallback(callable $sanitize): SettingField
    {
        $this->sanitize_callback = $sanitize;
        return $this;
    }

    /**
     * Get the sanitize callback (if set).
     *
     * @return null|callable
     */
    public function getSanitizeCallback()
    {
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
     * @return $this
     */
    public function setSectionId(string $section_id): SettingField
    {
        $this->section_id = $section_id;
        return $this;
    }

    /**
     * Get the Fields Section ID.
     *
     * @return string
     */
    public function getSectionId(): string
    {
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
     * @return $this
     */
    public function setSize(string $size): SettingField
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Get the Fields size.
     *
     * @return string
     */
    public function getSize(): string
    {
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
     * @return $this
     */
    public function setType(string $type): SettingField
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get the Fields type.
     *
     * @return string
     */
    public function getType(): string
    {
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
     * @return $this
     */
    public function setObfuscate(bool $obfuscated = true): SettingField
    {
        $this->obfuscated = $obfuscated;
        return $this;
    }

    /**
     * @return bool
     */
    public function isObfuscated(): bool
    {
        return $this->obfuscated;
    }

    /**
     * The Fields attributes array.
     * - used for additional inline attributes.
     *
     * @var array $attributes
     */
    private $attributes = [];

    /**
     * Set the Fields attributes array.
     * - optional
     *
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes): SettingField
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * Get the Fields attributes array.
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
