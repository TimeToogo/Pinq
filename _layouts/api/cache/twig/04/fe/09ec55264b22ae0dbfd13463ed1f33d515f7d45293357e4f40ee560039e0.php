<?php

/* layout/layout.twig */
class __TwigTemplate_04fe09ec55264b22ae0dbfd13463ed1f33d515f7d45293357e4f40ee560039e0 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("layout/base.twig");

        $this->blocks = array(
            'html' => array($this, 'block_html'),
            'body_class' => array($this, 'block_body_class'),
            'header' => array($this, 'block_header'),
            'content' => array($this, 'block_content'),
            'footer' => array($this, 'block_footer'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "layout/base.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_html($context, array $blocks = array())
    {
        // line 4
        echo "    <body id=\"";
        $this->displayBlock('body_class', $context, $blocks);
        echo "\">
        ";
        // line 5
        $this->displayBlock('header', $context, $blocks);
        // line 6
        echo "        <div class=\"content\">
            ";
        // line 7
        $this->displayBlock('content', $context, $blocks);
        // line 8
        echo "        </div>
        ";
        // line 9
        $this->displayBlock('footer', $context, $blocks);
        // line 10
        echo "    </body>
";
    }

    // line 4
    public function block_body_class($context, array $blocks = array())
    {
        echo "";
    }

    // line 5
    public function block_header($context, array $blocks = array())
    {
        echo "";
    }

    // line 7
    public function block_content($context, array $blocks = array())
    {
        echo "";
    }

    // line 9
    public function block_footer($context, array $blocks = array())
    {
        echo "";
    }

    public function getTemplateName()
    {
        return "layout/layout.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  69 => 7,  63 => 5,  57 => 4,  98 => 27,  92 => 23,  85 => 23,  74 => 18,  61 => 15,  45 => 7,  144 => 39,  136 => 36,  129 => 35,  125 => 33,  118 => 32,  114 => 30,  105 => 29,  101 => 28,  95 => 25,  88 => 23,  72 => 18,  66 => 16,  55 => 13,  26 => 3,  43 => 8,  41 => 7,  21 => 2,  379 => 58,  363 => 56,  358 => 55,  355 => 54,  350 => 53,  333 => 52,  331 => 51,  329 => 50,  318 => 49,  303 => 46,  291 => 45,  265 => 40,  261 => 39,  258 => 37,  255 => 35,  253 => 34,  236 => 33,  234 => 32,  222 => 31,  211 => 28,  205 => 27,  199 => 26,  185 => 25,  174 => 22,  168 => 21,  162 => 20,  148 => 19,  135 => 16,  133 => 15,  126 => 13,  119 => 11,  117 => 10,  104 => 9,  53 => 12,  42 => 6,  37 => 6,  34 => 4,  25 => 4,  19 => 1,  110 => 32,  103 => 28,  99 => 26,  90 => 27,  87 => 24,  83 => 22,  79 => 23,  64 => 16,  62 => 15,  58 => 14,  52 => 10,  49 => 11,  46 => 9,  40 => 5,  80 => 21,  76 => 22,  71 => 19,  60 => 13,  56 => 12,  50 => 9,  31 => 3,  94 => 28,  91 => 25,  84 => 8,  81 => 7,  75 => 9,  70 => 17,  68 => 18,  65 => 18,  47 => 8,  44 => 9,  38 => 7,  33 => 5,  22 => 8,  51 => 12,  39 => 6,  35 => 4,  32 => 3,  29 => 6,  28 => 3,);
    }
}
