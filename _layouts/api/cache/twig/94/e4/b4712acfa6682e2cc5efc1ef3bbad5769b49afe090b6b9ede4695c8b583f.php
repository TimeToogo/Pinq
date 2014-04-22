<?php

/* pages/namespaces.twig */
class __TwigTemplate_94e4b4712acfa6682e2cc5efc1ef3bbad5769b49afe090b6b9ede4695c8b583f extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'body_class' => array($this, 'block_body_class'),
            'content_header' => array($this, 'block_content_header'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return $this->env->resolveTemplate((isset($context["page_layout"]) ? $context["page_layout"] : $this->getContext($context, "page_layout")));
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 3
        $context["__internal_19d68a82a0ddb28f2ff5d461a5568600fc6aabccee41f572c7ecc6fc3817fac2"] = $this->env->loadTemplate("macros.twig");
        $this->getParent($context)->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_title($context, array $blocks = array())
    {
        echo "Namespaces | ";
        $this->displayParentBlock("title", $context, $blocks);
    }

    // line 7
    public function block_body_class($context, array $blocks = array())
    {
        echo "overview";
    }

    // line 9
    public function block_content_header($context, array $blocks = array())
    {
        // line 10
        echo "    <h1>Namespaces</h1>
";
    }

    // line 13
    public function block_content($context, array $blocks = array())
    {
        // line 14
        echo "    <table>
        ";
        // line 15
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["namespaces"]) ? $context["namespaces"] : $this->getContext($context, "namespaces")));
        foreach ($context['_seq'] as $context["_key"] => $context["namespace"]) {
            // line 16
            echo "            <tr>
                <td>";
            // line 17
            echo $context["__internal_19d68a82a0ddb28f2ff5d461a5568600fc6aabccee41f572c7ecc6fc3817fac2"]->getnamespace_link((isset($context["namespace"]) ? $context["namespace"] : $this->getContext($context, "namespace")));
            echo "</td>
            </tr>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['namespace'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 20
        echo "    </table>
";
    }

    public function getTemplateName()
    {
        return "pages/namespaces.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  69 => 7,  63 => 5,  57 => 4,  98 => 27,  92 => 23,  85 => 23,  74 => 20,  61 => 15,  45 => 7,  144 => 39,  136 => 36,  129 => 35,  125 => 33,  118 => 32,  114 => 30,  105 => 29,  101 => 28,  95 => 25,  88 => 23,  72 => 18,  66 => 16,  55 => 14,  26 => 3,  43 => 8,  41 => 7,  21 => 2,  379 => 58,  363 => 56,  358 => 55,  355 => 54,  350 => 53,  333 => 52,  331 => 51,  329 => 50,  318 => 49,  303 => 46,  291 => 45,  265 => 40,  261 => 39,  258 => 37,  255 => 35,  253 => 34,  236 => 33,  234 => 32,  222 => 31,  211 => 28,  205 => 27,  199 => 26,  185 => 25,  174 => 22,  168 => 21,  162 => 20,  148 => 19,  135 => 16,  133 => 15,  126 => 13,  119 => 11,  117 => 10,  104 => 9,  53 => 12,  42 => 6,  37 => 6,  34 => 4,  25 => 4,  19 => 1,  110 => 32,  103 => 28,  99 => 26,  90 => 27,  87 => 24,  83 => 22,  79 => 23,  64 => 16,  62 => 16,  58 => 15,  52 => 13,  49 => 11,  46 => 9,  40 => 5,  80 => 21,  76 => 22,  71 => 19,  60 => 13,  56 => 12,  50 => 9,  31 => 5,  94 => 28,  91 => 25,  84 => 8,  81 => 7,  75 => 9,  70 => 17,  68 => 18,  65 => 17,  47 => 10,  44 => 9,  38 => 7,  33 => 5,  22 => 8,  51 => 12,  39 => 6,  35 => 4,  32 => 3,  29 => 6,  28 => 3,);
    }
}
