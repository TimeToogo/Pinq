<?php

/* layout/base.twig */
class __TwigTemplate_2a22112fe433481b800dd717591ce93f7b87bc62316727e1c60e263913266773 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'head' => array($this, 'block_head'),
            'html' => array($this, 'block_html'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"en\">
    <head>
        <meta charset=\"UTF-8\" />
        <meta name=\"robots\" content=\"index, follow, all\" />
        <title>";
        // line 6
        $this->displayBlock('title', $context, $blocks);
        echo "</title>
        ";
        // line 7
        $this->displayBlock('head', $context, $blocks);
        // line 10
        echo "        ";
        if ($this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "config", array(0 => "favicon"), "method")) {
            // line 11
            echo "            <link rel=\"shortcut icon\" href=\"";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "config", array(0 => "favicon"), "method"), "html", null, true);
            echo "\" />
        ";
        }
        // line 13
        echo "        ";
        if ($this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "config", array(0 => "base_url"), "method")) {
            // line 14
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "versions"));
            foreach ($context['_seq'] as $context["_key"] => $context["version"]) {
                // line 15
                echo "<link rel=\"search\" type=\"application/opensearchdescription+xml\" href=\"";
                echo twig_escape_filter($this->env, strtr($this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "config", array(0 => "base_url"), "method"), array("%version%" => (isset($context["version"]) ? $context["version"] : $this->getContext($context, "version")))), "html", null, true);
                echo "/opensearch.xml\" title=\"";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "config", array(0 => "title"), "method"), "html", null, true);
                echo " (";
                echo twig_escape_filter($this->env, (isset($context["version"]) ? $context["version"] : $this->getContext($context, "version")), "html", null, true);
                echo ")\" />
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['version'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        }
        // line 18
        echo "    </head>
    ";
        // line 19
        $this->displayBlock('html', $context, $blocks);
        // line 21
        echo "</html>
";
    }

    // line 6
    public function block_title($context, array $blocks = array())
    {
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "config", array(0 => "title"), "method"), "html", null, true);
    }

    // line 7
    public function block_head($context, array $blocks = array())
    {
        // line 8
        echo "            <link rel=\"stylesheet\" type=\"text/css\" href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('sami')->pathForStaticFile($context, "stylesheet.css"), "html", null, true);
        echo "\">
        ";
    }

    // line 19
    public function block_html($context, array $blocks = array())
    {
        // line 20
        echo "    ";
    }

    public function getTemplateName()
    {
        return "layout/base.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  94 => 20,  91 => 19,  84 => 8,  81 => 7,  75 => 6,  70 => 21,  68 => 19,  65 => 18,  47 => 14,  44 => 13,  38 => 11,  33 => 7,  22 => 1,  51 => 15,  39 => 6,  35 => 10,  32 => 4,  29 => 6,  28 => 3,);
    }
}
