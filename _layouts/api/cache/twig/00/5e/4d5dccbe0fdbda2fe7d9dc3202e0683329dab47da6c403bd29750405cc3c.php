<?php

/* index.twig */
class __TwigTemplate_005e4d5dccbe0fdbda2fe7d9dc3202e0683329dab47da6c403bd29750405cc3c extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("layout/frame.twig");

        $this->blocks = array(
            'frame_src' => array($this, 'block_frame_src'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "layout/frame.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_frame_src($context, array $blocks = array())
    {
        echo "panel.html";
    }

    public function getTemplateName()
    {
        return "index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  28 => 3,);
    }
}
