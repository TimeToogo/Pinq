<?php

/* classes.twig */
class __TwigTemplate_13cc7a8a19f3999b94f6abd044d5bdcad98801bf300ce145c95aa89caf38b8fe extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("layout/base.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'body_class' => array($this, 'block_body_class'),
            'header' => array($this, 'block_header'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "layout/base.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 3
        $context["__internal_8348bc8841617805b3a35730a4763f4569b6221ec8c4179d73cb883fca92ce48"] = $this->env->loadTemplate("macros.twig");
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_title($context, array $blocks = array())
    {
        echo "All Classes | ";
        $this->displayParentBlock("title", $context, $blocks);
    }

    // line 7
    public function block_body_class($context, array $blocks = array())
    {
        echo "frame";
    }

    // line 9
    public function block_header($context, array $blocks = array())
    {
        // line 10
        echo "    <div class=\"header\">
        <h1>";
        // line 11
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "config", array(0 => "title"), "method"), "html", null, true);
        echo "</h1>

        <ul>
            <li><a href=\"";
        // line 14
        echo twig_escape_filter($this->env, $this->env->getExtension('sami')->pathForStaticFile($context, "classes-frame.html"), "html", null, true);
        echo "\">Classes</a></li>
            ";
        // line 15
        if ((isset($context["has_namespaces"]) ? $context["has_namespaces"] : $this->getContext($context, "has_namespaces"))) {
            // line 16
            echo "                <li><a href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('sami')->pathForStaticFile($context, "namespaces-frame.html"), "html", null, true);
            echo "\">Namespaces</a></li>
            ";
        }
        // line 18
        echo "        </ul>
    </div>
";
    }

    // line 22
    public function block_content($context, array $blocks = array())
    {
        // line 23
        echo "    <h1>Classes</h1>
    <ul>
        ";
        // line 25
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["classes"]) ? $context["classes"] : $this->getContext($context, "classes")));
        foreach ($context['_seq'] as $context["_key"] => $context["class"]) {
            // line 26
            echo "            <li>
                ";
            // line 27
            if ($this->getAttribute((isset($context["class"]) ? $context["class"] : $this->getContext($context, "class")), "isinterface")) {
                echo "<em>";
            }
            // line 28
            echo "                ";
            echo $context["__internal_8348bc8841617805b3a35730a4763f4569b6221ec8c4179d73cb883fca92ce48"]->getclass_link((isset($context["class"]) ? $context["class"] : $this->getContext($context, "class")), array("target" => "main"));
            echo "
                ";
            // line 29
            if ($this->getAttribute((isset($context["class"]) ? $context["class"] : $this->getContext($context, "class")), "isinterface")) {
                echo "</em>";
            }
            // line 30
            echo "            </li>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['class'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 32
        echo "    </ul>
";
    }

    public function getTemplateName()
    {
        return "classes.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  110 => 32,  103 => 30,  99 => 29,  90 => 27,  87 => 26,  83 => 25,  79 => 23,  64 => 16,  62 => 15,  58 => 14,  52 => 11,  49 => 10,  46 => 9,  40 => 7,  80 => 23,  76 => 22,  71 => 19,  60 => 13,  56 => 12,  50 => 9,  31 => 3,  94 => 28,  91 => 25,  84 => 8,  81 => 7,  75 => 6,  70 => 18,  68 => 18,  65 => 18,  47 => 8,  44 => 7,  38 => 5,  33 => 5,  22 => 1,  51 => 15,  39 => 6,  35 => 10,  32 => 4,  29 => 6,  28 => 3,);
    }
}
