<?php
use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form as Typecho_Widget_Helper_Form;
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * Bổ sung vào hệ quy chiếu siêu văn bản của typecho
 * 
 * @package NosineHyperText
 * @author valedrat
 * @version 1.0.0
 * @link https://ishare.io.vn
 *
 */

class NosineHyperText_Plugin implements Typecho_Plugin_Interface
{
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Abstract_Contents')->filter = ['NosineHyperText_Plugin', 'filterContent'];
        Typecho_Plugin::factory('Widget_Archive')->header = ['NosineHyperText_Plugin', 'insertCSS'];
        Helper::addAction('video_embed', 'NosineHyperText_Action');
    }

    public static function deactivate()
    {
        Helper::removeAction('video_embed');
    }

    public static function config(Typecho_Widget_Helper_Form $form) {}
    public static function personalConfig(Typecho_Widget_Helper_Form $form) {}
    
    public static function filterContent($content, $widget)
    {
        // Chỉ xử lý khi không phải trong admin
        if (!defined('__TYPECHO_ADMIN__')) {
            $content['text'] = self::parseSmileys($content['text']);
            $content['text'] = self::parseVideos($content['text']);
        }
        return $content;
    }
    
    public static function parseContent($content, $widget, $last)
    {
        // Parse smileys
        $content['text'] = self::parseSmileys($content['text']);

        // Parse videos
        $content['text'] = self::parseVideos($content['text']);

        return $content;
    }

    private static function parseSmileys($text)
    {
        $arr_emo_name = ['ami', 'anya', 'aru', 'aka', 'dauhanh', 'dora', 'le', 'menhera', 'moew', 'nam', 'pepe', 'qoobee', 'qoopepe', 'thobaymau', 'troll', 'dui', 'firefox', 'conan'];
        foreach ($arr_emo_name as $emo_name) {
            $pattern = '/\[:' . preg_quote($emo_name) . '(\d*):\]/';
            $image_url = 'https://dorew-site.github.io/assets/smileys/' . $emo_name . '/' . $emo_name . '$1.png';
            $replacement = '<img class="smile" src="' . $image_url . '" alt="$1" />';
            $text = preg_replace($pattern, $replacement, $text);
        }
        return $text;
    }

    private static function parseVideos($text)
    {
        $pattern = '/!vid\[\]\((.*?)\)/';
        $replacement = '<div class="video-wrapper" style="text-align:center;"><iframe src="' . Helper::options()->index . '/action/video_embed?link=$1" height="315" width="560" scrolling="no" allowfullscreen="" frameborder="0"></iframe></div>';
        return preg_replace($pattern, $replacement, $text);
    }
    
    public static function insertCSS()
    {
        echo "<style>
    img.smile{display:inline!important;vertical-align:baseline!important;height:2em;width:auto}
    img.smile:hover{transform:scale(1.2);transition:transform 0.2s ease;}
    </style>";
    }
}