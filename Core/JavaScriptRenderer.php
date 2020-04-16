<?php


namespace StainlessPlugins\OxidCookieConsentBase\Core;


use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ViewConfig;

class JavaScriptRenderer extends JavaScriptRenderer_parent
{

    /**
     * Enclose with script tag or add in function for wiget.
     *
     * @param string $scriptsOutput javascript to be enclosed.
     * @param string $widget        widget name.
     * @param bool   $isAjaxRequest is ajax request
     *
     * @return string
     */
    protected function enclose($scriptsOutput, $widget, $isAjaxRequest)
    {
        if ($scriptsOutput) {
            if ($widget && !$isAjaxRequest) {
                $scriptsOutput = "window.addEventListener('load', function() { $scriptsOutput }, false )";
            }
            return "$scriptsOutput";
        }
        return "";
    }

    protected function formSnippetsOutput($scripts, $widgetName, $ajaxRequest)
    {
        $preparedScripts = [];
        foreach ($scripts as $script) {
            if ($widgetName && !$ajaxRequest) {
                $sanitizedScript = $this->sanitize($script);
                $script = "WidgetsHandler.registerFunction('$sanitizedScript', '$widgetName');";
            }
            $attributesAsString = $this->getAttributes('script_attributes', md5(trim($script)));

            $preparedScripts[] = "<script $attributesAsString>$script</script>";
        }

        return implode(PHP_EOL, $preparedScripts);
    }


    /**
     * Form output for includes.
     *
     * @param array $includes String files to include.
     * @param string $widget Widget name.
     *
     * @return string
     */
    protected function formFilesOutput($includes, $widget)
    {
        if (!count($includes)) {
            return '';
        }

        ksort($includes); // Sort by priority.
        $usedSources = [];
        $widgets = [];
        $widgetTemplate = <<<HTML
<script %s>
    window.addEventListener('load', function() {
        WidgetsHandler.registerFile('%s', '%s');
    }, false)
</script>
HTML;
        $scriptTemplate = '<script %s src="%s"></script>';
        $template = $widget ? $widgetTemplate : $scriptTemplate;
        foreach ($includes as $priority) {
            foreach ($priority as $source) {
                if (!in_array($source, $usedSources)) {
                    $attributesAsString = $this->getAttributes('file_attributes', $source);
                    $widgets[] = sprintf($template, $attributesAsString, $source, $widget);
                    $usedSources[] = $source;
                }
            }
        }
        $output = implode(PHP_EOL, $widgets);

        return $output;
    }

    /**
     * @param $attributesName
     * @param $id
     * @return string
     */
    protected function getAttributes($attributesName, $id)
    {
        $config = Registry::getConfig();
        $viewConfig = Registry::get(ViewConfig::class);

        $scriptAttributes = $config->getGlobalParameter($attributesName);

        //TODO:
        // map service to group
        // map url to service (module config to url to service)

        $encapsulateAlsoNecessary = method_exists($viewConfig, 'isCookieConsentEncapsulateAlsoNecessary') ?
        $viewConfig->isCookieConsentEncapsulateAlsoNecessary() : false;
        $serviceAttribute = method_exists($viewConfig, 'getCookieConsentServiceAttribute') ?
            $viewConfig->getCookieConsentServiceAttribute() : '';
        $groupAttribute = method_exists($viewConfig, 'getCookieConsentGroupAttribute') ?
            $viewConfig->getCookieConsentGroupAttribute() : '';

        $attributes = $scriptAttributes[$id];

        if (isset($attributes['service']) || $encapsulateAlsoNecessary) {
            $service = isset($attributes['service']) ? $attributes['service'] : 'necessary';
            $group = isset($attributes['group']) ? $attributes['group'] : 'necessary';
            unset($attributes['group']);
            unset($attributes['service']);
            if (!empty($groupAttribute)) {
                $attributes[$groupAttribute] = $group;
            }
            if (!empty($serviceAttribute)) {
                $attributes[$serviceAttribute] = $service;
            }
            if (!$config->isAdmin()) {
                $attributes['type'] = "text/plain";
            }
        }
        $attributesAsString = join(' ', array_map(function ($key) use ($attributes) {
            return $key . '="' . $attributes[$key] . '"';
        }, array_keys($attributes)));
        return $attributesAsString;
    }

}