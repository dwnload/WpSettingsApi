<?php

declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Settings;

use Dwnload\WpSettingsApi\Api\Options;
use Dwnload\WpSettingsApi\Api\SettingField;
use function array_map;
use function array_merge;
use function esc_attr;
use function esc_html;
use function in_array;
use function is_array;
use function sprintf;

/**
 * Class FieldTypes
 * @package Dwnload\WpSettingsApi\Settings
 * phpcs:disable Generic.Files.LineLength.TooLong
 */
class FieldTypes
{

    public const DEFAULT_SIZE = 'regular';
    public const DEFAULT_TYPE = 'text';

    public const FIELD_TYPE_TEXT = 'text';
    public const FIELD_TYPE_TEXT_ARRAY = 'text_array';
    public const FIELD_TYPE_URL = 'url';
    public const FIELD_TYPE_DATE = 'date';
    public const FIELD_TYPE_DATETIME = 'datetime-local';
    public const FIELD_TYPE_EMAIL = 'email';
    public const FIELD_TYPE_COLOR = 'color';
    public const FIELD_TYPE_COLOR_ALPHA = 'coloralpha';
    public const FIELD_TYPE_NUMBER = 'number';
    public const FIELD_TYPE_CHECKBOX = 'checkbox';
    public const FIELD_TYPE_MULTICHECK = 'multicheck';
    public const FIELD_TYPE_RADIO = 'radio';
    public const FIELD_TYPE_SELECT = 'select';
    public const FIELD_TYPE_MULTISELECT = 'multiselect';
    public const FIELD_TYPE_TEXTAREA = 'textarea';
    public const FIELD_TYPE_HTML = 'html';
    public const FIELD_TYPE_WYSIWYG = 'wysiwyg';
    public const FIELD_TYPE_FILE = 'file';
    public const FIELD_TYPE_IMAGE = 'image';
    public const FIELD_TYPE_PASSWORD = 'password';
    public const FIELD_TYPE_REPEATER = 'repeater';

    /**
     * Rebuilds the SettingField object from the incoming `add_settings_field` $args Array.
     * @param array $args Array of SettingField object parameters
     * @return SettingField
     */
    public function getSettingFieldObject(array $args): SettingField
    {
        if (
            isset($args[SettingField::FIELD_OBJECT]) &&
            $args[SettingField::FIELD_OBJECT] instanceof SettingField
        ) {
            return $args[SettingField::FIELD_OBJECT];
        }

        unset($args[SettingField::FIELD_OBJECT]);

        return new SettingField($args);
    }

    /**
     * Renders an input text field.
     * @param array $args Array of Field object parameters
     * @param array|null $_args The repeater group Array
     */
    public function text(array $args, ?array $_args = null): void
    {
        $output = $this->getInputField($args, $_args);
        $output .= $this->getFieldDescription($args);

        echo $output; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
    }

    /**
     * Renders an input text array field.
     * @param array $args Array of Field object parameters
     */
    public function textArray(array $args): void
    {
        $field = $this->getSettingFieldObject($args);
        $value = Options::getOption($field->getId(), $field->getSectionId(), $field->getDefault());
        $getInputField = fn(mixed $value, int $key): string => sprintf(
            '<div class="FieldType_%1$s"><input type="%1$s" class="%2$s-text %7$s" id="%3$s[%4$s]" name="%3$s[%4$s][]"
value="%5$s"%6$s> <a href="javascript:;" class="button dodelete-%3$s[%4$s]" data-key="%8$s">Remove</a></div>',
            $field->getType(),
            $field->getSize(),
            $field->getSectionId(),
            $field->getId(),
            esc_attr($value),
            $this->getExtraFieldParams($args),
            implode(
                ' ',
                array_map('\sanitize_html_class', $field->getAttributes()['class'] ?? [])
            ),
            $key
        );

        $output = '<ul>';
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $output .= "<li data-repeatable='$key'>";
                $output .= $getInputField($val, $key);
                $output .= '</li>';
            }
        } else {
            $output .= "<li data-repeatable='0'>";
            $output .= $getInputField($value, 0);
            $output .= '</li>';
        }
        $output .= '</ul>';

        $output .= sprintf(
            '<a href="javascript:;" class="button docopy-%1$s[%2$s]">Add</a>',
            $field->getSectionId(),
            $field->getId()
        );
        $output .= $this->getFieldDescription($args);

        echo \str_replace($field->getType(), FieldTypes::FIELD_TYPE_TEXT, $output);
    }

    /**
     * Renders an input number field.
     * @param array $args Array of Field object parameters
     */
    public function number(array $args): void
    {
        $args[SettingField::TYPE] = FieldTypes::FIELD_TYPE_NUMBER;
        if (!isset($args[SettingField::ATTRIBUTES]['step'])) {
            $args[SettingField::ATTRIBUTES]['step'] = 'any';
        }
        $this->text($args);
    }

    /**
     * Renders an input password field.
     * @param array $args Array of Field object parameters
     */
    public function password(array $args): void
    {
        $args[SettingField::TYPE] = FieldTypes::FIELD_TYPE_PASSWORD;
        $this->text($args);
    }

    /**
     * Renders an input url field.
     * @param array $args Array of Field object parameters
     */
    public function url(array $args): void
    {
        $args[SettingField::TYPE] = FieldTypes::FIELD_TYPE_URL;
        $this->text($args);
    }

    /**
     * Renders an input date field.
     * @param array $args Array of Field object parameters
     */
    public function date(array $args): void
    {
        $field = $this->getSettingFieldObject($args);
        $args[SettingField::TYPE] = FieldTypes::FIELD_TYPE_DATE;
        $field->setAttributes(
            array_merge(
                $field->getAttributes(),
                ['pattern' => '\d{4}-\d{2}-\d{2}']
            )
        );
        $this->text($args);
    }

    /**
     * Renders an input datetime-local field.
     * @param array $args Array of Field object parameters
     */
    public function datetimeLocal(array $args): void
    {
        $field = $this->getSettingFieldObject($args);
        $args[SettingField::TYPE] = FieldTypes::FIELD_TYPE_DATETIME;
        $field->setAttributes(
            array_merge(
                $field->getAttributes(),
                ['pattern' => '[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}']
            )
        );
        \ob_start();
        $this->text($args);
        echo \str_replace($field->getType(), FieldTypes::FIELD_TYPE_DATETIME, \ob_get_clean());
    }

    /**
     * Renders an input email field.
     * @param array $args Array of Field object parameters
     */
    public function email(array $args): void
    {
        $args[SettingField::TYPE] = FieldTypes::FIELD_TYPE_EMAIL;
        $this->text($args);
    }

    /**
     * Renders an input color field.
     * @param array $args Array of Field object parameters
     */
    public function color(array $args): void
    {
        $field = $this->getSettingFieldObject($args);
        $field->setAttributes(
            array_merge(
                $field->getAttributes(),
                ['class' => ['color-picker']]
            )
        );
        $field->setType(FieldTypes::FIELD_TYPE_TEXT);
        $this->text($args);
    }

    /**
     * Renders an input color alpha field.
     * @param array $args Array of Field object parameters
     */
    public function coloralpha(array $args): void
    {
        $field = $this->getSettingFieldObject($args);
        $field->setAttributes(
            array_merge(
                $field->getAttributes(),
                ['class' => ['color-picker'], 'data-alpha-enabled' => 'true']
            )
        );
        $field->setType(FieldTypes::FIELD_TYPE_TEXT);
        $this->text($args);
    }

    /**
     * Renders an input file field.
     * @param array $args Array of Field object parameters
     */
    public function file(array $args): void
    {
        $field = $this->getSettingFieldObject($args);
        $value = Options::getOption($field->getId(), $field->getSectionId(), $field->getDefault());
        $_id = sprintf('%s[%s]', $field->getSectionId(), $field->getId());

        $field->setType(self::FIELD_TYPE_TEXT);
        \ob_start();
        echo $this->getInputField($args); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
        $output = \str_replace(
            sprintf('class="FieldType_%s"', self::FIELD_TYPE_TEXT),
            sprintf('class="FieldType_%s"', self::FIELD_TYPE_FILE),
            \ob_get_clean()
        );
        $output = \str_replace(
            '</div>',
            sprintf(
                '<button class="button secondary wpMediaUploader" type="button" value="%s">%s</button></div>',
                \esc_attr__('Browse media', 'wp-settings-api'),
                \esc_html__('Browse media', 'wp-settings-api')
            ),
            $output
        );
        $output .= $this->getFieldDescription($args);

        if (!empty($value)) {
            $output .= '<div id="' . $_id . '_preview" class="FieldType__file_preview">';
            if (\preg_match('/(^.*\.jpg|jpeg|png|gif|ico*)/i', $value) !== false) {
                $output .= '<div class="FieldType__file_image">';
                $output .= \wp_get_attachment_image(\attachment_url_to_postid($value), 'medium');
                $output .= '</div>';
            }
            $output .= '</div>';
        }

        echo $output; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
    }

    /**
     * Renders an input image (file) field.
     * @param array $args Array of Field object parameters
     */
    public function image(array $args): void
    {
        $args[SettingField::TYPE] = FieldTypes::FIELD_TYPE_FILE;
        $this->file($args);
    }

    /**
     * Renders an input checkbox field.
     * @param array $args Array of Field object parameters
     */
    public function checkbox(array $args): void
    {
        $field = $this->getSettingFieldObject($args);
        $value = Options::getOption($field->getId(), $field->getSectionId(), $field->getDefault());
        $_id = sprintf('%s[%s]', $field->getSectionId(), $field->getId());

        $output = '<div class="FieldType_checkbox">';
        $output .= sprintf('<input type="hidden" name="%1$s" value="off">', $_id);
        $output .= sprintf(
            '<input type="checkbox" class="checkbox" id="%1$s[%2$s]" name="%1$s[%2$s]" value="on"%3$s>',
            $field->getSectionId(),
            $field->getId(),
            \checked($value, 'on', false)
        );
        $output .= sprintf('<label for="%1$s"></label>', $_id);
        $output .= '</div>';
        $output .= $this->getFieldDescription($args);

        echo $output; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
    }

    /**
     * Renders an input multi-checkbox field.
     * @param array $args Array of Field object parameters
     */
    public function multicheck(array $args): void
    {
        $field = $this->getSettingFieldObject($args);
        $value = Options::getOption($field->getId(), $field->getSectionId(), $field->getDefault());
        $value = is_array($value) ? array_map('esc_attr', $value) : esc_attr($value);

        $output = '<div class="FieldType_multicheckbox">';
        $output .= '<ul>';
        foreach ($field->getOptions() as $key => $label) {
            $checked = $value[$key] ?? '0';
            $output .= '<li>';
            $output .= sprintf(
                '<input type="checkbox" class="checkbox" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" 
value="%3$s"%4$s>',
                $field->getSectionId(),
                $field->getId(),
                $key,
                \checked($checked, $key, false)
            );
            $output .= sprintf(
                '<label for="%1$s[%2$s][%4$s]" title="%3$s">%3$s</label>',
                $field->getSectionId(),
                $field->getId(),
                $label,
                $key
            );
            $output .= '</li>';
        }
        $output .= '</ul>';
        $output .= '</div>';
        $output .= $this->getFieldDescription($args);

        echo $output; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
    }

    /**
     * Renders an input radio field.
     * @param array $args Array of Field object parameters
     */
    public function radio(array $args): void
    {
        $field = $this->getSettingFieldObject($args);
        $value = Options::getOption($field->getId(), $field->getSectionId(), $field->getDefault());

        $output = '<div class="FieldType_radio">';
        $output .= '<ul>';
        foreach ($field->getOptions() as $key => $label) {
            $output .= '<li>';
            $output .= sprintf(
                '<input type="radio" class="radio" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s"%4$s >',
                $field->getSectionId(),
                $field->getId(),
                $key,
                \checked($value, $key, false)
            );
            $output .= sprintf(
                '<label for="%1$s[%2$s][%4$s]" title="%3$s">%3$s</label><br>',
                $field->getSectionId(),
                $field->getId(),
                $label,
                $key
            );
            $output .= '</li>';
        }
        $output .= '</ul>';
        $output .= '</div>';
        $output .= $this->getFieldDescription($args);

        echo $output; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
    }

    /**
     * Renders a select field.
     * @param array $args Array of Field object parameters
     */
    public function select(array $args): void
    {
        $field = $this->getSettingFieldObject($args);
        $value = Options::getOption($field->getId(), $field->getSectionId(), $field->getDefault());

        $output = '<div class="FieldType_select">';
        $output .= sprintf(
            '<select class="select2 %1$s" name="%2$s[%3$s]" id="%2$s[%3$s]"%4$s>',
            $field->getSize(),
            $field->getSectionId(),
            $field->getId(),
            $this->getExtraFieldParams($args),
        );

        foreach ($field->getOptions() as $key => $label) {
            $view = isset($args['show_key_value']) && $args['show_key_value'] === true ? $key : $label;
            $output .= sprintf(
                '<option value="%1$s"%2$s>%3$s</option>',
                esc_attr($key),
                is_array($value) && in_array($key, $value, true) ? ' selected' : \selected($value, $key, false),
                esc_html($view)
            );
        }
        $output .= '</select>';
        $output .= '</div>';
        $output .= $this->getFieldDescription($args);

        echo $output; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
    }

    /**
     * Renders an input multi-select field.
     * @param array $args Array of Field object parameters
     * phpcd:disable Inpsyde.CodeQuality.NestingLevel.High
     */
    public function multiselect(array $args): void
    {
        $field = $this->getSettingFieldObject($args);
        $value = Options::getOption($field->getId(), $field->getSectionId(), $field->getDefault());
        $value = is_array($value) ? array_map('\esc_attr', $value) : esc_attr($value);

        $output = '<div class="FieldType_multiselect">';
        $output .= sprintf(
            '<select class="select2 %1$s" size="%1$s" name="%2$s[%3$s][]" id="%2$s[%3$s]" multiple%4$s>',
            $field->getSize(),
            $field->getSectionId(),
            $field->getId(),
            $this->getExtraFieldParams($args),
        );
        foreach ($field->getOptions() as $key => $label) {
            if (is_array($label)) {
                $output .= sprintf('<optgroup label="%s">', $key);
                foreach ($label as $index => $val) {
                    $output .= sprintf(
                        '<option value="%1$s"%2$s>%3$s</option>',
                        esc_attr($index),
                        is_array($value) && in_array((string)$index, $value, true) ? ' selected' : '',
                        esc_html($val)
                    );
                }
                $output .= '</optgroup>';
                continue;
            }
            $output .= sprintf(
                '<option value="%1$s"%2$s>%3$s</option>',
                esc_attr($key),
                is_array($value) && in_array((string)$key, $value, true) ? ' selected' : '',
                esc_html($label)
            );
        }
        $output .= '</select>';
        $output .= '</div>';
        $output .= $this->getFieldDescription($args);

        echo $output; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
    }

    /**
     * Renders a textarea field.
     * @param array $args Array of Field object parameters
     */
    public function textarea(array $args): void
    {
        $field = $this->getSettingFieldObject($args);
        $value = Options::getOption($field->getId(), $field->getSectionId(), $field->getDefault());

        $output = '<div class="FieldType_textarea">';
        $output .= sprintf(
            '<textarea rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]"%5$s>%4$s</textarea>',
            $field->getSize(),
            $field->getSectionId(),
            $field->getId(),
            \stripslashes($value),
            $this->getExtraFieldParams($args)
        );
        $output .= '</div>';
        $output .= $this->getFieldDescription($args);

        echo $output; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
    }

    /**
     * Renders a textarea wysiwyg field.
     * @param array $args Array of Field object parameters
     */
    public function wysiwyg(array $args): void
    {
        $field = $this->getSettingFieldObject($args);
        $value = Options::getOption($field->getId(), $field->getSectionId(), $field->getDefault());

        $output = '<div class="FieldType_wysiwyg" style="max-width: 100%">';

        $editor_settings = [
            'teeny' => true,
            'textarea_name' => $field->getSectionId() . '[' . $field->getId() . ']',
            'textarea_rows' => 10,
        ];
        $editor_settings = array_merge($editor_settings, $field->getOptions());

        \ob_start();
        \wp_editor($value ?? '', $field->getSectionId() . '-' . $field->getId(), $editor_settings);

        $output .= \ob_get_clean();
        $output .= '</div>';
        $output .= $this->getFieldDescription($args);

        echo $output; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
    }

    /**
     * Renders a repeater field.
     * @param array $args Array of Field object parameters
     * @throws \Exception
     * phpcs:disable SlevomatCodingStandard.Classes.MethodSpacing.IncorrectLinesCountBetweenMethods
     */
    public function repeater(array $args): void
    {
        $field = $this->getSettingFieldObject($args);
        $fields = $field->getFields();
        if (!is_array($fields)) {
            return;
        }

        $output = '<div class="FieldType_repeater">';
        $output .= '<div data-repeatable>';
        $output .= \sprintf(
            '<p class="FieldType_repeater__header"><a href="javascript:;" class="alignright button-secondary" data-remove>%s</a></p>',
            \esc_html__('Remove', 'wp-settings-api')
        );
        foreach ($fields as $repeaterField) {
            if (!$repeaterField instanceof SettingField) {
                continue;
            }

            $output .= '<div class="repeater-wrap">';
            \ob_start();
            $this->{$repeaterField->getType()}($repeaterField->toArray(), $args);
            $output .= \ob_get_clean();
            $output .= '</div>';
        }
        $output .= '</div><!-- [data-repeatable] -->';
        $output .= \sprintf(
            '<a href="javascript:;" class="button button-primary" data-add>%s</a>',
            \esc_html__('Add', 'wp-settings-api')
        );
        $output .= '</div>';

        echo $output; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
    }


    /**
     * Renders a html field.
     * @param array $args Array of Field object parameters
     */
    public function html(array $args): void
    {
        $field = $this->getSettingFieldObject($args);
        $value = Options::getOption($field->getId(), $field->getSectionId(), $field->getDefault());

        $output = '<div class="FieldType_html">';
        $output .= \wp_kses_post($value ?? '');
        $output .= '</div>';
        $output .= $this->getFieldDescription($args);

        echo $output; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
    }

    /**
     * Renders an input field.
     * @param array $args Array of Field object parameters
     * @param array|null $_args
     * @return string
     */
    protected function getInputField(array $args, ?array $_args = null): string
    {
        $field = $this->getSettingFieldObject($args);
        $group = $_args === null ? null : $this->getSettingFieldObject($_args);
        $value = !$field->isObfuscated() ?
            Options::getOption($field->getId(), $field->getSectionId(), $field->getDefault()) :
            Options::getObfuscatedOption($field->getId(), $field->getSectionId(), $field->getDefault());

        if ($group) {
            return '<!-- Repeater Groups Not Supported Yet -->';
        }

        return sprintf(
            '<div class="FieldType_%1$s"><input type="%1$s" class="%2$s-text %7$s" id="%3$s[%4$s]" name="%3$s[%4$s]"
value="%5$s"%6$s></div>',
            $field->getType(),
            $field->getSize(),
            $field->getSectionId(),
            $field->getId(),
            esc_attr($value),
            $this->getExtraFieldParams($args),
            implode(
                ' ',
                array_map('\sanitize_html_class', $field->getAttributes()['class'] ?? [])
            )
        );
    }

    /**
     * Get field description for display.
     * @param array $args settings field args
     * @return string
     */
    protected function getFieldDescription(array $args): string
    {
        if (!empty($args[SettingField::DESC])) {
            return sprintf('<p class="description">%s</p>', \wp_kses_post($args[SettingField::DESC]));
        }

        return '';
    }

    /**
     * Helper to return extra parameters as a string.
     * @param array $args settings field args
     * @return string
     */
    protected function getExtraFieldParams(array $args): string
    {
        $return = '';
        $attributes = $this->getSettingFieldObject($args)->getAttributes() ?? [];
        unset($attributes['class']); // Remove the `class` attribute from the list of attributes (since it requires an array value).

        if (!empty($attributes)) {
            foreach ($attributes as $key => $value) {
                $return .= sprintf(' %s="%s"', esc_attr($key), esc_attr($value));
            }
        }

        return $return;
    }
}
