<?php
declare(strict_types=1);

final class HtmlHelper {
    public static function escape(?string $s): string
    {
        if ($s === null || $s === '') {
            return '';
        }

        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}