<?php declare(strict_types=1);

namespace Dwnload\WpSettingsApi\Settings;

use Dwnload\WpSettingsApi\Api\Options;
use Dwnload\WpSettingsApi\Api\SettingField;

/**
 * Class FieldTypes
 *
 * @package Dwnload\WpSettingsApi\Settings
 */
class FieldTypes
{

    const DEFAULT_SIZE = 'regular';
    const DEFAULT_TYPE = 'text';

    const FIELD_TYPE_TEXT = 'text';
    const FIELD_TYPE_URL = 'url';
    const FIELD_TYPE_EMAIL = 'email';
    const FIELD_TYPE_NUMBER = 'number';
    const FIELD_TYPE_CHECKBOX = 'checkbox';
    const FIELD_TYPE_MULTICHECK = 'multicheck';
    const FIELD_TYPE_RADIO = 'radio';
    const FIELD_TYPE_SELECT = 'select';
    const FIELD_TYPE_TEXTAREA = 'textarea';
    const FIELD_TYPE_HTML = 'html';
    const FIELD_TYPE_WYSIWYG = 'wysiwyg';
    const FIELD_TYPE_FILE = 'file';
    const FIELD_TYPE_IMAGE = 'image';
    const FIELD_TYPE_PASSWORD = 'password';

    /**
     * Rebuilds the SettingField object from the incoming `add_settings_field` $args Array.
     *
     * @param array $args Array of SettingField object parameters
     *
     * @return SettingField
     */
    public function getSettingFieldObject(array $args): SettingField
    {
        if (isset($args[SettingField::FIELD_OBJECT]) &&
            $args[SettingField::FIELD_OBJECT] instanceof SettingField
        ) {
            return $args[SettingField::FIELD_OBJECT];
        }

        unset($args[SettingField::FIELD_OBJECT]);

        return new SettingField($args);
    }

    /**
     * Renders an input text field.
     *
     * @param array $args Array of Field object parameters
     */
    public function text(array $args)
    {
        $output = $this->getInputField($args);
        $output .= $this->getFieldDescription($args);

        echo $output; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
    }

    /**
     * Renders an input number field.
     *
     * @param array $args Array of Field object parameters
     */
    public function number(array $args)
    {
        $args[SettingField::TYPE] = 'number';
        if (!isset($args['attributes']['step'])) {
            $args['attributes']['step'] = 'any';
        }
        $this->text($args);
    }

    /**
     * Renders an input password field.
     *
     * @param array $args Array of Field object parameters
     */
    public function password(array $args)
    {
        $args[SettingField::TYPE] = 'password';
        $this->text($args);
    }

    /**
     * Renders an input url field.
     *
     * @param array $args Array of Field object parameters
     */
    public function url(array $args)
    {
        $args[SettingField::TYPE] = 'url';
        $this->text($args);
    }

    /**
     * Renders an input email field.
     *
     * @param array $args Array of Field object parameters
     */
    public function email(array $args)
    {
        $args[SettingField::TYPE] = 'email';
        $this->text($args);
    }

    /**
     * Renders an input file field.
     *
     * @param array $args Array of Field object parameters
     */
    public function file(array $args)
    {
        $field = $this->getSettingFieldObject($args);
        $value = Options::getOption($field->getId(), $field->getSectionId(), $field->getDefault());
        $_id = \sprintf('%s[%s]', $field->getSectionId(), $field->getId());

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
            \sprintf(
                '<button class="button secondary wpMediaUploader" type="button" value="%s">%s</button></div>',
                \esc_attr__('Browse media', 'custom-login'),
                \esc_html__('Browse media', 'custom-login')
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
     *
     * @param array $args Array of Field object parameters
     */
    public function image(array $args)
    {
        $args[SettingField::TYPE] = 'file';
        $this->file($args);
    }

    /**
     * Renders an input checkbox field.
     *
     * @param array $args Array of Field object parameters
     */
    public function checkbox(array $args)
    {
        $field = $this->getSettingFieldObject($args);
        $value = Options::getOption($field->getId(), $field->getSectionId(), $field->getDefault());
        $_id = \sprintf('%s[%s]', $field->getSectionId(), $field->getId());

        $output = '<div class="FieldType_checkbox">';
        $output .= \sprintf('<input type="hidden" name="%1$s" value="off">', $_id);
        $output .= \sprintf(
            '<input type="checkbox" class="checkbox" id="%1$s[%2$s]" name="%1$s[%2$s]" value="on"%4$s>',
            $field->getSectionId(),
            $field->getId(),
            \esc_attr($value),
            \checked($value, 'on', false)
        );
        $output .= \sprintf('<label for="%1$s"></label>', $_id);
        $output .= '</div>';
        $output .= $this->getFieldDescription($args);

        echo $output; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
    }

    /**
     * Renders an input multi-checkbox field.
     *
     * @param array $args Array of Field object parameters
     */
    public function multicheck(array $args)
    {
        $field = $this->getSettingFieldObject($args);
        $value = Options::getOption($field->getId(), $field->getSectionId(), $field->getDefault());
        $value = \is_array($value) ? \array_map('esc_attr', $value) : \esc_attr($value);

        $output = '<div class="FieldType_multicheckbox">';
        $output .= '<ul>';
        foreach ($field->getOptions() as $key => $label) {
            $checked = isset($value[$key]) ? $value[$key] : '0';
            $output .= '<li>';
            $output .= \sprintf(
                '<input type="checkbox" class="checkbox" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" 
value="%3$s"%4$s>',
                $field->getSectionId(),
                $field->getId(),
                $key,
                \checked($checked, $key, false)
            );
            $output .= \sprintf(
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
     *
     * @param array $args Array of Field object parameters
     */
    public function radio(array $args)
    {
        $field = $this->getSettingFieldObject($args);
        $value = Options::getOption($field->getId(), $field->getSectionId(), $field->getDefault());

        $output = '<div class="FieldType_radio">';
        $output .= '<ul>';
        foreach ($field->getOptions() as $key => $label) {
            $output .= '<li>';
            $output .= \sprintf(
                '<input type="radio" class="radio" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s"%4$s >',
                $field->getSectionId(),
                $field->getId(),
                $key,
                \checked($value, $key, false)
            );
            $output .= \sprintf(
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
     *
     * @param array $args Array of Field object parameters
     */
    public function select(array $args)
    {
        $field = $this->getSettingFieldObject($args);
        $value = Options::getOption($field->getId(), $field->getSectionId(), $field->getDefault());

        $output = '<div class="FieldType_select">';
        $output .= \sprintf(
            '<select class="chosen-select %1$s" name="%2$s[%3$s]%5$s" id="%2$s[%3$s]"%4$s>',
            $field->getSize(),
            $field->getSectionId(),
            $field->getId(),
            $this->getExtraFieldParams($args),
            isset($args['attributes']['multiple']) ? '[]' : ''
        );

        foreach ($field->getOptions() as $key => $label) {
            $view = isset($args['show_key_value']) && true === $args['show_key_value'] ? $key : $label;
            $output .= \sprintf(
                '<option value="%1$s"%2$s>%3$s</option>',
                \esc_attr($key),
                \is_array($value) && \in_array($key, $value, true) ? ' selected' : \selected($value, $key, false),
                \esc_html($view)
            );
        }
        $output .= '</select>';
        $output .= '</div>';
        $output .= $this->getFieldDescription($args);

        echo $output; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
    }

    /**
     * Renders a textarea field.
     *
     * @param array $args Array of Field object parameters
     */
    public function textarea(array $args)
    {
        $field = $this->getSettingFieldObject($args);
        $value = Options::getOption($field->getId(), $field->getSectionId(), $field->getDefault());

        $output = '<div class="FieldType_textarea">';
        $output .= \sprintf(
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
     *
     * @param array $args Array of Field object parameters
     */
    public function wysiwyg(array $args)
    {
        $field = $this->getSettingFieldObject($args);
        $value = Options::getOption($field->getId(), $field->getSectionId(), $field->getDefault());

        $output = '<div class="FieldType_wysiwyg" style="max-width: 100%">';

        $editor_settings = [
            'teeny' => true,
            'textarea_name' => $field->getSectionId() . '[' . $field->getId() . ']',
            'textarea_rows' => 10,
        ];
        $editor_settings = \array_merge($editor_settings, $field->getOptions());

        \ob_start();
        \wp_editor($value, $field->getSectionId() . '-' . $field->getId(), $editor_settings);

        $output .= \ob_get_clean();
        $output .= '</div>';
        $output .= $this->getFieldDescription($args);

        echo $output; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
    }

    /**
     * Renders a html field.
     *
     * @param array $args Array of Field object parameters
     */
    public function html(array $args)
    {
        $field = $this->getSettingFieldObject($args);
        $value = Options::getOption($field->getId(), $field->getSectionId(), $field->getDefault());

        $output = '<div class="FieldType_html">';
        $output .= \wp_kses_post($value);
        $output .= '</div>';
        $output .= $this->getFieldDescription($args);

        echo $output; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
    }

    /**
     * Renders an input field.
     *
     * @param array $args Array of Field object parameters
     *
     * @return string
     */
    protected function getInputField(array $args): string
    {
        $field = $this->getSettingFieldObject($args);
        $value = !$field->isObfuscated() ?
            Options::getOption($field->getId(), $field->getSectionId(), $field->getDefault()) :
            Options::getObfuscatedOption($field->getId(), $field->getSectionId(), $field->getDefault());

        return \sprintf(
            '<div class="FieldType_%1$s"><input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]"
value="%5$s"%6$s></div>',
            $field->getType(),
            $field->getSize(),
            $field->getSectionId(),
            $field->getId(),
            \esc_attr($value),
            $this->getExtraFieldParams($args)
        );
    }

    /**
     * Get field description for display.
     *
     * @param array $args settings field args
     *
     * @return string
     */
    protected function getFieldDescription(array $args): string
    {
        if (!empty($args[SettingField::DESC])) {
            return \sprintf('<p class="description">%s</p>', \esc_html($args[SettingField::DESC]));
        }

        return '';
    }

    /**
     * Helper to return extra parameters as a string.
     *
     * @param array $args settings field args
     *
     * @return string
     */
    protected function getExtraFieldParams(array $args): string
    {
        $return = '';
        $attributes = $this->getSettingFieldObject($args)->getAttributes() ?? [];

        if (!empty($attributes)) {
            foreach ($attributes as $key => $value) {
                $return .= \sprintf(' %s="%s"', \sanitize_key($key), \esc_attr($value));
            }
        }

        return $return;
    }
}
