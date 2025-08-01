<?php
use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form as Typecho_Widget_Helper_Form;
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * Tạo đường dẫn thân thiện (rewrite)
 * 
 * @package NosineSlug
 * @author valedrat
 * @version 1.0.0
 * @link https://ishare.io.vn
 *
 */
class NosineSlug_Plugin implements Typecho_Plugin_Interface
{
    public static function activate()
    {
        if (!class_exists('Transliterator')) {
            throw new Typecho_Plugin_Exception(_t('PHP extension "intl" is required.'));
        }

        Typecho_Plugin::factory('admin/write-post.php')->bottom_20 = ['NosineSlug_Plugin', 'ajax'];
        Typecho_Plugin::factory('admin/write-page.php')->bottom_20 = ['NosineSlug_Plugin', 'ajax'];

        Helper::addAction('nosine-slug', 'NosineSlug_Action');
        return _t('Plugin activated. It will auto-generate slug via Symfony AsciiSlugger.');
    }

    public static function deactivate()
    {
        Helper::removeAction('nosine-slug');
    }

    public static function config($form) { }

    public static function personalConfig($form) { }

    public static function ajax()
    {
        // include JS snippet
        ?>
        <script>
        function autoSlug() {
            var title = $('#title'), slug = $('#slug');
            // Nếu title trống thì xóa slug
            if (title.val().trim().length === 0) {
                slug.val('');
                slug.siblings('pre').text('');
                return;
            }
            // Nếu slug đã có sẵn thì không làm gì
            if (slug.val().length) return;
            // Gửi request để tạo slug từ title
            $.ajax({
                url: '<?php Helper::options()->index('/action/nosine-slug?q='); ?>' + encodeURIComponent(title.val()),
                success: function(data) {
                    if (data.result) {
                        slug.val(data.result).focus();
                        slug.siblings('pre').text(data.result);
                    }
                }
            });
        }
        
        jQuery(function(){
            $('#title').blur(autoSlug);
            $('#slug').blur(autoSlug);
        });
        </script>
<?php
    }
}