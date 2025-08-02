<?php
class NosineHyperText_Action extends Typecho_Widget implements Widget_Interface_Do
{
    public function action()
    {
        $link = isset($_GET['link']) ? urldecode(trim($_GET['link'])) : '';
        $this->response->setContentType('text/html');
        echo $this->renderEmbed($link);
    }

    private function renderEmbed($url)
    {
        if (strpos($url, 'youtube') !== false || strpos($url, 'youtu.be/') !== false) {
            $id = $this->getYouTubeId($url);
            return '<iframe width="100%" height="100%" src="https://www.youtube-nocookie.com/embed/' . $id . '" frameborder="0" allowfullscreen></iframe>';
        } elseif ($this->isMediaFile($url)) {
            return <<<HTML
<div id="dplayer"></div>
<script src="https://cdn.statically.io/gh/kn007/DPlayer-Lite/00dab19fc8021bdb072034c0415184a638a3e3b2/dist/DPlayer.min.js"></script>
<script>
const dp = new DPlayer({
    container: document.getElementById('dplayer'),
    video: { url: "{$url}" }
});
</script>
HTML;
        } else {
            $encoded = urlencode($url);
            return <<<HTML
<div id="place"></div>
<script>
fetch("https://noembed.com/embed?url={$encoded}")
.then(res => res.json())
.then(data => {
    document.getElementById("place").innerHTML = data.html;
});
</script>
HTML;
        }
    }

    private function getYouTubeId($url)
    {
        preg_match('/^(?:https?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:watch\?(?:.*&)?v=|embed\/|v\/|shorts\/))([^?&"\'\s]+)/', $url, $matches);
        return $matches[1] ?? '';
    }

    private function isMediaFile($url)
    {
        $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
        $video_exts = ['mp4', 'webm', 'ogg'];
        $audio_exts = ['mp3', 'wav', 'aac'];
        return in_array(strtolower($ext), array_merge($video_exts, $audio_exts));
    }
}