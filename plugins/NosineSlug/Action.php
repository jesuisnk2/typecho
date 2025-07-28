<?php
if (!interface_exists('Stringable')) {
    interface Stringable
    {
        public function __toString(): string;
    }
}
require_once __DIR__ . '/libs/autoload.php';
if (!class_exists(\Symfony\Component\String\Slugger\AsciiSlugger::class)) {
    die('Slugger class not found — check autoload.php');
}
use Symfony\Component\String\Slugger\AsciiSlugger;


/**
 * Auto slug action using Symfony AsciiSlugger
 *
 * @package NosineSlug
 */
class NosineSlug_Action extends Typecho_Widget implements Widget_Interface_Do
{
    public function action()
    {
            // Lấy query từ URL: ?q=xxx
            $string = isset($this->request->q) ? trim(strip_tags($this->request->q)) : '';
            if ($string === '') {
                $this->response->throwJson(['result' => '']);
                return;
            }
            // Khởi tạo Slugger
            $slugger = new AsciiSlugger();
            // Tạo slug
            $slug = $slugger->slug($string)->lower();
            // Xoá ký tự đặc biệt (dấu chấm, ngoặc, v.v.)
            #$slug = preg_replace('/[[:punct:]]+/', '', $slug);
            // Trả kết quả JSON
            $this->response->setHeader('Content-Type', 'application/json');
            $this->response->throwJson(['result' => $slug]);
    }
}
