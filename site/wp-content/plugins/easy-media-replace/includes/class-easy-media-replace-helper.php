<?php
class Easy_Media_Replace_Helper
{
    public static function is_image($file)
    {
        $file_mime = wp_check_filetype($file);
        return file_is_displayable_image($file) && strpos($file_mime['type'], 'image/') !== false;
    }

    public static function file_type($mime)
    {
        if (empty($mime)) {
            return static::trans('file');
        }

        if (strpos($mime, 'image/') !== false) {
            return static::trans('image');
        }
        if (strpos($mime, 'video/') !== false) {
            return static::trans('video');
        }
        if (strpos($mime, 'audio/') !== false) {
            return static::trans('audio');
        }
        if (strpos($mime, 'application/pdf') !== false) {
            return 'PDF';
        }
        if (strpos($mime, 'application/zip') !== false) {
            return 'ZIP';
        }
        return static::trans('file');
    }
   
    public static function same_mime($file1, $file2)
    {
        $file1_mime = wp_check_filetype($file1);
        $file2_mime = wp_check_filetype($file2);
        return $file1_mime['type'] === $file2_mime['type'];
    }

    public static function trans($text)
    {
        return __($text, EMR_TEXT_DOMAIN);
    }

}
