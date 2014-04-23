<?php

/* pages/index.twig */
class __TwigTemplate_2ea28611570aca087432cf494f67d071f934001bef3caf811394ab36a6f05096 extends Twig_Template
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
        $context["__internal_90245d7872d350211a10c680b4501ed82f14a56a4acc6b1f6ec7a49302bcd977"] = $this->env->loadTemplate("macros.twig");
        $this->getParent($context)->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_title($context, array $blocks = array())
    {
        echo "Index | ";
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
        echo "    <div class=\"type\">Index</div>

    ";
        // line 12
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable(range("A", "Z"));
        foreach ($context['_seq'] as $context["_key"] => $context["letter"]) {
            // line 13
            echo "        ";
            if (($this->getAttribute((isset($context["items"]) ? $context["items"] : null), (isset($context["letter"]) ? $context["letter"] : $this->getContext($context, "letter")), array(), "array", true, true) && (twig_length_filter($this->env, $this->getAttribute((isset($context["items"]) ? $context["items"] : $this->getContext($context, "items")), (isset($context["letter"]) ? $context["letter"] : $this->getContext($context, "letter")), array(), "array")) > 1))) {
                // line 14
                echo "            <a href=\"#letter";
                echo twig_escape_filter($this->env, (isset($context["letter"]) ? $context["letter"] : $this->getContext($context, "letter")), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, (isset($context["letter"]) ? $context["letter"] : $this->getContext($context, "letter")), "html", null, true);
                echo "</a>
        ";
            } else {
                // line 16
                echo "            ";
                echo twig_escape_filter($this->env, (isset($context["letter"]) ? $context["letter"] : $this->getContext($context, "letter")), "html", null, true);
                echo "
        ";
            }
            // line 18
            echo "    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['letter'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    // line 21
    public function block_content($context, array $blocks = array())
    {
        // line 22
        echo "    ";
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["items"]) ? $context["items"] : $this->getContext($context, "items")));
        foreach ($context['_seq'] as $context["letter"] => $context["elements"]) {
            // line 23
            echo "<h2 id=\"letter";
            echo twig_escape_filter($this->env, (isset($context["letter"]) ? $context["letter"] : $this->getContext($context, "letter")), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, (isset($context["letter"]) ? $context["letter"] : $this->getContext($context, "letter")), "html", null, true);
            echo "</h2>
        <dl id=\"index\">";
            // line 25
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["elements"]) ? $context["elements"] : $this->getContext($context, "elements")));
            foreach ($context['_seq'] as $context["_key"] => $context["element"]) {
                // line 26
                $context["type"] = $this->getAttribute((isset($context["element"]) ? $context["element"] : $this->getContext($context, "element")), 0, array(), "array");
                // line 27
                $context["value"] = $this->getAttribute((isset($context["element"]) ? $context["element"] : $this->getContext($context, "element")), 1, array(), "array");
                // line 28
                if (("class" == (isset($context["type"]) ? $context["type"] : $this->getContext($context, "type")))) {
                    // line 29
                    echo "<dt>";
                    echo $context["__internal_90245d7872d350211a10c680b4501ed82f14a56a4acc6b1f6ec7a49302bcd977"]->getclass_link((isset($context["value"]) ? $context["value"] : $this->getContext($context, "value")));
                    if ((isset($context["has_namespaces"]) ? $context["has_namespaces"] : $this->getContext($context, "has_namespaces"))) {
                        echo " &mdash; <em>Class in namespace ";
                        echo $context["__internal_90245d7872d350211a10c680b4501ed82f14a56a4acc6b1f6ec7a49302bcd977"]->getnamespace_link($this->getAttribute((isset($context["value"]) ? $context["value"] : $this->getContext($context, "value")), "namespace"));
                    }
                    echo "</em></dt>
                    <dd>";
                    // line 30
                    echo $this->env->getExtension('sami')->parseDesc($context, $this->getAttribute((isset($context["value"]) ? $context["value"] : $this->getContext($context, "value")), "shortdesc"), (isset($context["value"]) ? $context["value"] : $this->getContext($context, "value")));
                    echo "</dd>";
                } elseif (("method" == (isset($context["type"]) ? $context["type"] : $this->getContext($context, "type")))) {
                    // line 32
                    echo "<dt>";
                    echo $context["__internal_90245d7872d350211a10c680b4501ed82f14a56a4acc6b1f6ec7a49302bcd977"]->getmethod_link((isset($context["value"]) ? $context["value"] : $this->getContext($context, "value")));
                    echo "() &mdash; <em>Method in class ";
                    echo $context["__internal_90245d7872d350211a10c680b4501ed82f14a56a4acc6b1f6ec7a49302bcd977"]->getclass_link($this->getAttribute((isset($context["value"]) ? $context["value"] : $this->getContext($context, "value")), "class"));
                    echo "</em></dt>
                    <dd>";
                    // line 33
                    echo $this->env->getExtension('sami')->parseDesc($context, $this->getAttribute((isset($context["value"]) ? $context["value"] : $this->getContext($context, "value")), "shortdesc"), $this->getAttribute((isset($context["value"]) ? $context["value"] : $this->getContext($context, "value")), "class"));
                    echo "</dd>";
                } elseif (("property" == (isset($context["type"]) ? $context["type"] : $this->getContext($context, "type")))) {
                    // line 35
                    echo "<dt>\$";
                    echo $context["__internal_90245d7872d350211a10c680b4501ed82f14a56a4acc6b1f6ec7a49302bcd977"]->getproperty_link((isset($context["value"]) ? $context["value"] : $this->getContext($context, "value")));
                    echo " &mdash; <em>Property in class ";
                    echo $context["__internal_90245d7872d350211a10c680b4501ed82f14a56a4acc6b1f6ec7a49302bcd977"]->getclass_link($this->getAttribute((isset($context["value"]) ? $context["value"] : $this->getContext($context, "value")), "class"));
                    echo "</em></dt>
                    <dd>";
                    // line 36
                    echo $this->env->getExtension('sami')->parseDesc($context, $this->getAttribute((isset($context["value"]) ? $context["value"] : $this->getContext($context, "value")), "shortdesc"), $this->getAttribute((isset($context["value"]) ? $context["value"] : $this->getContext($context, "value")), "class"));
                    echo "</dd>";
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['element'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 39
            echo "        </dl>";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['letter'], $context['elements'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "pages/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  144 => 39,  136 => 36,  129 => 35,  125 => 33,  118 => 32,  114 => 30,  105 => 29,  101 => 27,  95 => 25,  88 => 23,  72 => 18,  66 => 16,  55 => 13,  26 => 3,  43 => 8,  41 => 7,  21 => 2,  379 => 58,  363 => 56,  358 => 55,  355 => 54,  350 => 53,  333 => 52,  331 => 51,  329 => 50,  318 => 49,  303 => 46,  291 => 45,  265 => 40,  261 => 39,  258 => 37,  255 => 35,  253 => 34,  236 => 33,  234 => 32,  222 => 31,  211 => 28,  205 => 27,  199 => 26,  185 => 25,  174 => 22,  168 => 21,  162 => 20,  148 => 19,  135 => 16,  133 => 15,  126 => 13,  119 => 11,  117 => 10,  104 => 9,  53 => 2,  42 => 1,  37 => 6,  34 => 44,  25 => 4,  19 => 1,  110 => 32,  103 => 28,  99 => 26,  90 => 27,  87 => 6,  83 => 22,  79 => 23,  64 => 16,  62 => 15,  58 => 14,  52 => 11,  49 => 10,  46 => 9,  40 => 7,  80 => 21,  76 => 22,  71 => 19,  60 => 13,  56 => 12,  50 => 9,  31 => 5,  94 => 28,  91 => 25,  84 => 8,  81 => 7,  75 => 5,  70 => 18,  68 => 18,  65 => 18,  47 => 10,  44 => 9,  38 => 7,  33 => 5,  22 => 8,  51 => 12,  39 => 6,  35 => 10,  32 => 4,  29 => 6,  28 => 24,);
    }
}
