<?php

namespace Framework\Mail;

/**
 * Class TemplateProcessor
 * @package Framework\Mail
 */
class TemplateProcessor
{
    /**
     * @param string $template
     * @param array $vars
     * @return null|string|string[]
     */
    public static function process(string $template, array $vars)
    {
        foreach ($vars as $key => $value) {
            $template = preg_replace("/{{ $key }}/", $value, $template);
        }

        return $template;
    }
}
