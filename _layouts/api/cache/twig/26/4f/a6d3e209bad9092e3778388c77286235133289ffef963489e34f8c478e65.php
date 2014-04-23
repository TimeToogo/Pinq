<?php

/* layout/page.twig */
class __TwigTemplate_264fa6d3e209bad9092e3778388c77286235133289ffef963489e34f8c478e65 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("default/layout/page.twig");

        $this->blocks = array(
            'frame' => array($this, 'block_frame'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "default/layout/page.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_frame($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "layout/page.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  144 => 39,  136 => 36,  129 => 35,  125 => 33,  118 => 32,  114 => 30,  105 => 29,  101 => 27,  95 => 25,  88 => 23,  72 => 18,  66 => 16,  55 => 13,  26 => 3,  43 => 8,  41 => 7,  21 => 2,  379 => 58,  363 => 56,  358 => 55,  355 => 54,  350 => 53,  333 => 52,  331 => 51,  329 => 50,  318 => 49,  303 => 46,  291 => 45,  265 => 40,  261 => 39,  258 => 37,  255 => 35,  253 => 34,  236 => 33,  234 => 32,  222 => 31,  211 => 28,  205 => 27,  199 => 26,  185 => 25,  174 => 22,  168 => 21,  162 => 20,  148 => 19,  135 => 16,  133 => 15,  126 => 13,  119 => 11,  117 => 10,  104 => 9,  53 => 2,  42 => 1,  37 => 6,  34 => 44,  25 => 4,  19 => 1,  110 => 32,  103 => 28,  99 => 26,  90 => 27,  87 => 6,  83 => 22,  79 => 23,  64 => 16,  62 => 15,  58 => 14,  52 => 11,  49 => 10,  46 => 9,  40 => 7,  80 => 21,  76 => 22,  71 => 19,  60 => 13,  56 => 12,  50 => 9,  31 => 5,  94 => 28,  91 => 25,  84 => 8,  81 => 7,  75 => 5,  70 => 18,  68 => 18,  65 => 18,  47 => 10,  44 => 9,  38 => 7,  33 => 5,  22 => 8,  51 => 12,  39 => 6,  35 => 10,  32 => 4,  29 => 6,  28 => 3,);
    }
}
