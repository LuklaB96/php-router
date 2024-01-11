<?php
namespace App\Lib\Security\HTML;

class HiddenFieldGenerator
{
    private const HIDDEN_FIELD_FORMAT = '<input type="hidden" id="%s" name="%s" value="%s" />';
    /**
     * 
     * @param string $name hidden input field name
     * @param string $value hidden input field value, marked as sensitive parameter for obvious reasons
     * @param string $customFormat optional sprintf formatted text
     * @return string HTML hidden field with name and value
     */
    public static function generate(#[\SensitiveParameter] string $name, #[\SensitiveParameter] string $value, string $customFormat = self::HIDDEN_FIELD_FORMAT): string
    {
        $hiddenHTMLField = sprintf($customFormat, $name, $name, $value);
        return $hiddenHTMLField;
    }
}
?>