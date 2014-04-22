<?php

/* namespaces.twig */
class __TwigTemplate_458e6302099dd8eeed357490c5e96f896d6878e28973c25adfc5be3ac6d49298 extends Twig_Template
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
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = array())
    {
        echo "Namespaces | ";
        $this->displayParentBlock("title", $context, $blocks);
    }

    // line 5
    public function block_body_class($context, array $blocks = array())
    {
        echo "frame";
    }

    // line 7
    public function block_header($context, array $blocks = array())
    {
        // line 8
        echo "    <div class=\"header\">
        <h1>";
        // line 9
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "config", array(0 => "title"), "method"), "html", null, true);
        echo "</h1>

        <ul>
            <li><a href=\"";
        // line 12
        echo twig_escape_filter($this->env, $this->env->getExtension('sami')->pathForStaticFile($context, "classes-frame.html"), "html", null, true);
        echo "\">Classes</a></li>
            <li><a href=\"";
        // line 13
        echo twig_escape_filter($this->env, $this->env->getExtension('sami')->pathForStaticFile($context, "namespaces-frame.html"), "html", null, true);
        echo "\">Namespaces</a></li>
        </ul>
    </div>
";
    }

    // line 18
    public function block_content($context, array $blocks = array())
    {
        // line 19
        echo "    <h1>Namespaces</h1>

    <ul>
        ";
        // line 22
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["namespaces"]) ? $context["namespaces"] : $this->getContext($context, "namespaces")));
        foreach ($context['_seq'] as $context["_key"] => $context["namespace"]) {
            // line 23
            echo "            <li><a href=\"";
            echo twig_escape_filter($this->env, (isset($context["namespace"]) ? $context["namespace"] : $this->getContext($context, "namespace")), "html", null, true);
            echo "/namespace-frame.html\" target=\"index\">";
            echo twig_escape_filter($this->env, (isset($context["namespace"]) ? $context["namespace"] : $this->getContext($context, "namespace")), "html", null, true);
            echo "</a></li>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['namespace'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 25
        echo "    </ul>
";
    }

    public function getTemplateName()
    {
        return "namespaces.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  80 => 23,  76 => 22,  71 => 19,  60 => 13,  56 => 12,  50 => 9,  31 => 3,  94 => 20,  91 => 25,  84 => 8,  81 => 7,  75 => 6,  70 => 21,  68 => 18,  65 => 18,  47 => 8,  44 => 7,  38 => 5,  33 => 7,  22 => 1,  51 => 15,  39 => 6,  35 => 10,  32 => 4,  29 => 6,  28 => 3,);
    }
}
