<?php

/**
 * Add functions to Twig that output TraceView Real User Monitoring JavaScript.
 * @see https://support.tv.appneta.com/support/solutions/articles/86401-php-rum-instrumentation
 */

namespace AppNeta\TraceViewTwig\Extension;

class TraceViewRUMExtension extends \Twig_Extension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'oboe_rum_header' => new \Twig_Function_Function(__CLASS__.'::getHeader',
                array('is_safe' => array('html'))),
            'oboe_rum_footer' => new \Twig_Function_Function(__CLASS__.'::getFooter',
                array('is_safe' => array('html'))),
        );
    }

    public static function getHeader($useScriptTags = true)
    {
        return oboe_get_rum_header($useScriptTags);
    }

    public static function getFooter($useScriptTags = true)
    {
        return oboe_get_rum_footer($useScriptTags);
    }

    public function getName()
    {
        return 'traceview_twig_rum_extension';
    }
}
