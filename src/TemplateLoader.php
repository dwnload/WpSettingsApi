<?php declare(strict_types=1);

namespace Dwnload\WpSettingsApi;

/**
 * Class TemplateLoader
 *
 * @package Dwnload\WpSettingsApi
 */
class TemplateLoader
{

    /**
     * Array of data to pass off to a template.
     *
     * @var array $template_args
     */
    protected static $template_args = [];

    /**
     * Like get_template_part() put lets you pass args to the template file
     * Args are available in the template as $template_args array
     *
     * @param string $file The file part
     * @param array $_template_args Optional array of args to pass to the template.
     */
    public static function getTemplatePart(string $file, array $_template_args = [])
    {
        self::$template_args = $_template_args;

        \ob_start();
        require $file;
        $data = \ob_get_clean();

        echo $data; // WPCS: XSS ok.
    }

    /**
     * Return the passed args Array to the template.
     * This is used in part with self::getTemplatePart to avoid ugly $globals
     *
     * @return array
     */
    public static function getTemplateArgs(): array
    {
        return self::$template_args;
    }

    /**
     * Create a postbox widget.
     *
     * @param string $id ID of the postbox.
     * @param string $title Title of the postbox.
     * @param string $content Content of the postbox.
     */
    public static function postbox(string $id, string $title, string $content)
    {
        ?>
        <div class="Dwnload_WP_Settings_Api__postbox metabox-holder"
             id="<?php echo \sanitize_html_class($id); ?>">
            <div class="postbox">
                <h3><?php echo \esc_html($title); ?></h3>
                <div class="Dwnload_WP_Settings_Api__inside inside">
                    <?php echo \wp_kses_post($content); ?>
                </div>
            </div>
        </div>
        <?php
    }
}
