<?php

/* search_index.twig */
class __TwigTemplate_73530b1bedd1e0a74327b73afeae9a167a94fa17ec962fdf8f9cadd7b3b1b1aa extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $context["__internal_d12f9a1e926f7316c955a678ccca895603ed0d1b2346eeb0b25ee3573a4764f4"] = $this->env->loadTemplate("macros.twig");
        // line 4
        echo "var search_data = {
    'index': {
        'searchIndex': ";
        // line 6
        echo twig_jsonencode_filter($this->getAttribute((isset($context["index"]) ? $context["index"] : $this->getContext($context, "index")), "searchIndex", array(), "array"));
        echo ",
        'info': [";
        // line 8
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["index"]) ? $context["index"] : $this->getContext($context, "index")), "info", array(), "array"));
        $context['loop'] = array(
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        );
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
            // line 9
            echo "[";
            // line 10
            if ((1 == $this->getAttribute((isset($context["item"]) ? $context["item"] : $this->getContext($context, "item")), 0, array(), "array"))) {
                // line 11
                echo twig_jsonencode_filter($this->getAttribute($this->getAttribute((isset($context["item"]) ? $context["item"] : $this->getContext($context, "item")), 1, array(), "array"), "shortname"));
                echo ",";
                // line 12
                echo twig_jsonencode_filter($this->getAttribute($this->getAttribute((isset($context["item"]) ? $context["item"] : $this->getContext($context, "item")), 1, array(), "array"), "namespace"));
                echo ",";
                // line 13
                echo twig_jsonencode_filter($this->env->getExtension('sami')->pathForClass($context, $this->getAttribute((isset($context["item"]) ? $context["item"] : $this->getContext($context, "item")), 1, array(), "array")));
                echo ",";
                // line 14
                echo twig_jsonencode_filter((($this->getAttribute($this->getAttribute((isset($context["item"]) ? $context["item"] : $this->getContext($context, "item")), 1, array(), "array"), "parent")) ? ((" < " . $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["item"]) ? $context["item"] : $this->getContext($context, "item")), 1, array(), "array"), "parent"), "shortname"))) : ("")));
                echo ",";
                // line 15
                echo twig_jsonencode_filter($this->env->getExtension('sami')->getSnippet($this->getAttribute($this->getAttribute((isset($context["item"]) ? $context["item"] : $this->getContext($context, "item")), 1, array(), "array"), "shortdesc")));
                echo ",";
                // line 16
                echo 1;
            } elseif ((2 == $this->getAttribute((isset($context["item"]) ? $context["item"] : $this->getContext($context, "item")), 0, array(), "array"))) {
                // line 18
                echo twig_jsonencode_filter((($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["item"]) ? $context["item"] : $this->getContext($context, "item")), 1, array(), "array"), "class"), "shortname") . "::") . $this->getAttribute($this->getAttribute((isset($context["item"]) ? $context["item"] : $this->getContext($context, "item")), 1, array(), "array"), "name")));
                echo ",";
                // line 19
                echo twig_jsonencode_filter($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["item"]) ? $context["item"] : $this->getContext($context, "item")), 1, array(), "array"), "class"), "name"));
                echo ",";
                // line 20
                echo twig_jsonencode_filter($this->env->getExtension('sami')->pathForMethod($context, $this->getAttribute((isset($context["item"]) ? $context["item"] : $this->getContext($context, "item")), 1, array(), "array")));
                echo ",";
                // line 21
                echo twig_jsonencode_filter($context["__internal_d12f9a1e926f7316c955a678ccca895603ed0d1b2346eeb0b25ee3573a4764f4"]->getmethod_parameters_signature($this->getAttribute((isset($context["item"]) ? $context["item"] : $this->getContext($context, "item")), 1, array(), "array")));
                echo ",";
                // line 22
                echo twig_jsonencode_filter($this->env->getExtension('sami')->getSnippet($this->getAttribute($this->getAttribute((isset($context["item"]) ? $context["item"] : $this->getContext($context, "item")), 1, array(), "array"), "shortdesc")));
                echo ",";
                // line 23
                echo 2;
            } elseif ((3 == $this->getAttribute((isset($context["item"]) ? $context["item"] : $this->getContext($context, "item")), 0, array(), "array"))) {
                // line 25
                echo twig_jsonencode_filter($this->getAttribute((isset($context["item"]) ? $context["item"] : $this->getContext($context, "item")), 1, array(), "array"));
                echo ",";
                // line 26
                echo "\"\"";
                echo ",";
                // line 27
                echo twig_jsonencode_filter($this->env->getExtension('sami')->pathForNamespace($context, $this->getAttribute((isset($context["item"]) ? $context["item"] : $this->getContext($context, "item")), 1, array(), "array")));
                echo ",";
                // line 28
                echo "\"\"";
                echo ",";
                // line 29
                echo "\"\"";
                echo ",";
                // line 30
                echo 3;
            }
            // line 32
            echo "]";
            // line 33
            echo (($this->getAttribute((isset($context["loop"]) ? $context["loop"] : $this->getContext($context, "loop")), "last")) ? ("") : (","));
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 35
        echo "]
    }
}
search_data['index']['longSearchIndex'] = search_data['index']['searchIndex']";
    }

    public function getTemplateName()
    {
        return "search_index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  120 => 35,  106 => 33,  89 => 26,  86 => 25,  77 => 21,  59 => 14,  48 => 10,  127 => 64,  122 => 61,  109 => 59,  24 => 4,  82 => 26,  93 => 27,  69 => 19,  63 => 5,  57 => 4,  98 => 29,  92 => 27,  85 => 23,  74 => 20,  61 => 26,  45 => 7,  144 => 39,  136 => 36,  129 => 35,  125 => 33,  118 => 32,  114 => 30,  105 => 58,  101 => 30,  95 => 28,  88 => 50,  72 => 18,  66 => 18,  55 => 14,  26 => 3,  43 => 8,  41 => 9,  21 => 4,  379 => 58,  363 => 56,  358 => 55,  355 => 54,  350 => 53,  333 => 52,  331 => 51,  329 => 50,  318 => 49,  303 => 46,  291 => 45,  265 => 40,  261 => 39,  258 => 37,  255 => 35,  253 => 34,  236 => 33,  234 => 32,  222 => 31,  211 => 28,  205 => 27,  199 => 26,  185 => 25,  174 => 22,  168 => 21,  162 => 20,  148 => 19,  135 => 16,  133 => 15,  126 => 13,  119 => 11,  117 => 10,  104 => 32,  53 => 12,  42 => 6,  37 => 8,  34 => 4,  25 => 6,  19 => 1,  110 => 32,  103 => 28,  99 => 55,  90 => 27,  87 => 24,  83 => 23,  79 => 21,  64 => 16,  62 => 15,  58 => 15,  52 => 13,  49 => 11,  46 => 9,  40 => 5,  80 => 22,  76 => 22,  71 => 19,  60 => 13,  56 => 13,  50 => 11,  31 => 5,  94 => 28,  91 => 25,  84 => 8,  81 => 7,  75 => 22,  70 => 19,  68 => 18,  65 => 16,  47 => 10,  44 => 9,  38 => 7,  33 => 5,  22 => 8,  51 => 12,  39 => 6,  35 => 4,  32 => 6,  29 => 8,  28 => 5,);
    }
}
