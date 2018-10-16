<?php declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Api;

/**
 * Class SectionBuilder
 *
 * @package Dwnload\WpSettingsApi\Api
 */
class SettingSection
{

    const SECTION_ID = 'id';
    const SECTION_TITLE = 'title';

    /**
     * SettingSection constructor.
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
     * The Section ID.
     *
     * @var string $id
     */
    private $id;

    /**
     * Sets the Sections ID.
     *
     * @param string $id
     * @return $this
     */
    public function setId(string $id): SettingSection
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the Sections ID.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * The Section title.
     *
     * @var string $title
     */
    private $title;

    /**
     * Sets the Section title.
     *
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): SettingSection
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Gets the Section title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}
