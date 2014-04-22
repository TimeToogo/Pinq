<?php

/* index.twig */
class __TwigTemplate_1c05b18264df28a75c7a8095149f8b949243bbf6b75c9ce310b0da1fff5684f8 extends Twig_Template
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
