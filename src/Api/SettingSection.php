<?php

declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Api;

use TheFrosty\WpUtilities\Models\BaseModel;

/**
 * Class SettingSection
 * @package Dwnload\WpSettingsApi\Api
 */
class SettingSection extends BaseModel
{

    public const SECTION_ID = 'id';
    public const SECTION_TITLE = 'title';

    /**
     * The Section ID.
     * @var string $id
     */
    private string $id;

    /**
     * Sets the Sections ID.
     * @param string $id
     * @return self
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the Sections ID.
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * The Section title.
     * @var string $title
     */
    private string $title;

    /**
     * Sets the Section title.
     * @param string $title
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the Section title.
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get serializable fields.
     * @return string[]
     */
    protected function getSerializableFields(): array
    {
        return [
            self::SECTION_ID,
            self::SECTION_TITLE,
        ];
    }
}
