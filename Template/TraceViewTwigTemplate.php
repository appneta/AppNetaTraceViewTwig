<?php

/**
 * @file
 * Definition of AppNeta\TraceViewTwig\Template\TraceViewTwigTemplate.
 */

namespace AppNeta\TraceViewTwig\Template;

/**
 * This is the base class for compiled Twig templates.
 */
abstract class TraceViewTwigTemplate extends \Twig_Template {
    /**
     * {@inheritdoc}
     */
    public function display(array $context, array $blocks = array())
    {
        // On an untraced request, bail out early.
        if (!oboe_is_tracing()) {
            return parent::display($context, $blocks);
        }

        // Get the name of the template we're in.
        $template = $this->getTemplateName();

        // If we're not already in a 'twig' layer, start one.
        $traceview_twig_layer = $this->env->getGlobals()['_traceview_twig_layer'];
        if (!$traceview_twig_layer) {
            // Modify globals to indicate that we're now inside a 'twig' layer.
            $this->env->addGlobal('_traceview_twig_layer', TRUE);

            // Enter a layer for the entire Twig render, with backtrace.
            oboe_log("twig", "entry", array('TemplateFile' => $template), TRUE);
        }

        // Enter a profile for this template (no backtrace).
        oboe_log(NULL, "profile_entry", array(
            'TemplateFile' => $template,
            'TemplateLanguage' => 'twig',
            'ProfileName' => $template), FALSE);

        // Replicates normal display() behavior without causing broken traces
        // if an exception occurs.
        $display_exception = NULL;
        try {
            parent::display($context, $blocks);
        } catch (Exception $e) {
            // Sure wish we could use 'finally', but it's PHP 5.5+...
            $display_exception = $e;
        }

        // Exit a profile for this template (no backtrace).
        oboe_log(NULL, "profile_exit", array('ProfileName' => $template), FALSE);

        // If this is the first call to display(), exit the 'twig' layer.
        if (!$traceview_twig_layer) {
            // Exit the layer for the entire Twig render (no backtrace).
            oboe_log("twig", "exit", array(), FALSE);

            // Modify globals to indicate that we're no longer inside a 'twig' layer.
            $this->env->addGlobal('_traceview_twig_layer', FALSE);
        }

        // If there was an exception, raise it.
        if ($display_exception) {
            throw $display_exception;
        }    }
}
