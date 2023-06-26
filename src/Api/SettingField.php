<?php

declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Api;

use Dwnload\WpSettingsApi\Settings\FieldTypes;
use TheFrosty\WpUtilities\Models\BaseModel;

/**
 * Class SettingField
 * @package Dwnload\WpSettingsApi\Api
 * phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification
 */
class SettingField extends BaseModel
{

    /**
     * Array key values.
     * phpcs:disable Inpsyde.CodeQuality.PropertyPerClassLimit.TooMuchProperties
     */
    public const ID = 'id';
    public const ATTRIBUTES = 'attributes';
    public const CLASS_OBJECT = 'class_object';
    public const DEFAULT = 'default';
    public const DESC = 'description';
    public const LABEL = 'label';
    public const NAME = 'name';
    public const OPTIONS = 'options';
    public const SANITIZE = 'sanitize_callback';
    public const SECTION_ID = 'section_id';
    public const SIZE = 'size';
    public const TYPE = 'type';
    public const REPEATER_FIELDS = 'fields';
    public const FIELD_OBJECT = SettingField::class;

    /**
     * The Fields description.
     * @var string|null $description
     */
    private ?string $description;

    /**
     * Sets the Fields description.
     * @param string $description
     * @return self
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Gets the Fields description.
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description ?? '';
    }

    /**
     * The Fields default value.
     * @var mixed $default
     */
    private mixed $default = '';

    /**
     * Sets the Fields default value.
     * @param mixed $default
     * @return self
     */
    public function setDefault(mixed $default): self
    {
        $this->default = $default;

        return $this;
    }

    /**
     * Gets the Fields default value.
     * @return mixed
     */
    public function getDefault(): mixed
    {
        return $this->default;
    }

    /**
     * The Fields label (description).
     * @var string $label
     */
    private string $label = '';

    /**
     * Sets the Fields label (description).
     * @param string $label
     * @return self
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Gets Fields label (description).
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * The Fields name.
     * - used internally.
     * @var string $name
     */
    private string $name;

    /**
     * Sets the Fields name.
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the Fields name.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets the Fields ID (AKA name).
     * @return string
     */
    public function getId(): string
    {
        return $this->name;
    }

    /**
     * The Fields callback object.
     * - if null the Field type name needs to have a method in the FieldTypes class.
     * @var mixed $class_object
     */
    private mixed $class_object = null;

    /**
     * Set the Fields output object.
     * @param mixed $class_object
     * @return self
     */
    public function setClassObject(mixed $class_object): self
    {
        $this->class_object = $class_object;

        return $this;
    }

    /**
     * Get the Fields output object.
     * @return mixed
     */
    public function getClassObject(): mixed
    {
        return $this->class_object;
    }

    /**
     * The Fields options array.
     * - used for html array options like radios, checkboxes, etc.
     * @var array $options
     */
    private array $options = [];

    /**
     * Set the Fields options array.
     * - optional
     * @param array $options
     * @return self
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get the Fields options array.
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * The Fields sanitize method callback.
     * @var mixed $sanitize_callback
     */
    private mixed $sanitize_callback = null;

    /**
     * Set the Fields sanitize callback.
     * - optional
     * @param mixed $sanitize
     * @return self
     */
    public function setSanitizeCallback(mixed $sanitize): self
    {
        $this->sanitize_callback = $sanitize;

        return $this;
    }

    /**
     * Get the "sanitize" callback (if set).
     * @return mixed
     */
    public function getSanitizeCallback(): mixed
    {
        return $this->sanitize_callback;
    }

    /**
     * The Fields Section it's attached to.
     * @var string $section_id
     */
    private string $section_id;

    /**
     * Set the Fields Section ID.
     * - optional
     * @param string $section_id
     * @return self
     */
    public function setSectionId(string $section_id): self
    {
        $this->section_id = $section_id;

        return $this;
    }

    /**
     * Get the Fields Section ID.
     * @return string
     */
    public function getSectionId(): string
    {
        return $this->section_id;
    }

    /**
     * The Fields CSS size attribute.
     * @var string $size
     */
    private string $size;

    /**
     * Set the Fields size.
     * @param string $size
     * @return self
     */
    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get the Fields size.
     * @return string
     */
    public function getSize(): string
    {
        return $this->size ?? FieldTypes::DEFAULT_SIZE;
    }

    /**
     * The Fields input type.
     * - optional
     * @var string $type
     */
    private string $type = FieldTypes::DEFAULT_TYPE;

    /**
     * Set the Fields input type (defaults to 'text').
     * @param string $type
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the Fields type.
     * @return string
     * @since 2.8.1 Replace "_" & "-". Example: Type=datetime-local, Method=datetimeLocal().
     */
    public function getType(): string
    {
        return str_replace(['_', '-'], '', lcfirst(ucwords($this->type, '_-')));
    }

    /**
     * Repeater fields array
     * @var SettingField[] $fields
     */
    private ?array $fields = null;

    /**
     * Set the Fields input type (defaults to 'text').
     * @param SettingField[] $fields
     * @return self
     */
    public function setFields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Get the Fields type.
     * @return array|null
     */
    public function getFields(): ?array
    {
        return $this->fields;
    }

    /**
     * The Fields name.
     * @var bool $obfuscated
     */
    private bool $obfuscated = false;

    /**
     * Set the Fields as being obfuscated.
     * @param bool $obfuscated
     * @return self
     */
    public function setObfuscate(bool $obfuscated = true): self
    {
        $this->obfuscated = $obfuscated;

        return $this;
    }

    /**
     * Is the Field obfuscated?
     * @return bool
     */
    public function isObfuscated(): bool
    {
        return $this->obfuscated;
    }

    /**
     * The Fields attributes array.
     * - used for additional inline attributes.
     * @var array $attributes
     */
    private array $attributes = [];

    /**
     * Set the Fields attributes array.
     * - optional
     * @param array $attributes
     * @return self
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Get the Fields attributes array.
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Get all serializable fields for toArray().
     * @return string[]
     */
    public function getSerializableFields(): array
    {
        return [
            self::ID,
            self::ATTRIBUTES,
            self::CLASS_OBJECT,
            self::DEFAULT,
            self::DESC,
            self::LABEL,
            self::NAME,
            self::OPTIONS,
            self::SANITIZE,
            self::SECTION_ID,
            self::SIZE,
            self::TYPE,
            self::REPEATER_FIELDS,
            self::FIELD_OBJECT,
        ];
    }
}
