<?php


namespace StainlessPlugins\OxidCookieConsentBase\Core;


use OxidEsales\Eshop\Core\Registry;

class JavaScriptRegistrator extends JavaScriptRegistrator_parent
{
    /**
     * Register JavaScript code snippet for rendering.
     *
     * @param string $script
     * @param bool   $isDynamic
     * @param array  $attributes
     */
    public function addSnippetWithAttributes($script, $isDynamic, $attributes)
    {
        $this->addSnippet($script, $isDynamic);
        $scriptId = md5(trim($script));
        $this->pushToGlobalArray('script_attributes', $attributes, $scriptId);
    }



    /**
     * Register JavaScript file (local or remote) for rendering.
     *
     * @param string $file
     * @param int    $priority
     * @param bool   $isDynamic
     * @param array  $attributes
     */
    public function addFileWithAttributes($file, $priority, $isDynamic, $attributes)
    {
        $this->addFile($file, $priority, $isDynamic);

        if (!preg_match('#^https?://#', $file)) {
            $file = $this->formLocalFileUrl($file);
        }
        if ($file) {
            $filesAttributesName = 'file_attributes';
            $this->pushToGlobalArray($filesAttributesName, $attributes, $file);
        }
    }

    /**
     * @param $name
     * @param $item
     * @param $key
     */
    private function pushToGlobalArray($name, $item, $key) {
        $config = Registry::getConfig();
        $array = (array)$config->getGlobalParameter($name);
        $array[$key] = $item;
        $config->setGlobalParameter($name, $array);
    }

}