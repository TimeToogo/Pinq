<?php

/* pages/opensearch.twig */
class __TwigTemplate_aefa38f99ec88de4c8ceee8ad7eaa4aeb98c9cbae424a8478388ead1c82e20be extends Twig_Template
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
        if ($this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "config", array(0 => "base_url"), "method")) {
            // line 2
            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
    <OpenSearchDescription xmlns=\"http://a9.com/-/spec/opensearch/1.1/\" xmlns:referrer=\"http://a9.com/-/opensearch/extensions/referrer/\">
        <ShortName>";
            // line 4
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "config", array(0 => "title"), "method"), "html", null, true);
            echo " (";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "version"), "html", null, true);
            echo ")</ShortName>
        <Description>Searches ";
            // line 5
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "config", array(0 => "title"), "method"), "html", null, true);
            echo " (";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "version"), "html", null, true);
            echo ")</Description>
        <Tags>";
            // line 6
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "config", array(0 => "title"), "method"), "html", null, true);
            echo "</Tags>
        ";
            // line 7
            if ($this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "config", array(0 => "favicon"), "method")) {
                // line 8
                echo "<Image height=\"16\" width=\"16\" type=\"image/x-icon\">";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "config", array(0 => "favicon"), "method"), "html", null, true);
                echo "</Image>
        ";
            }
            // line 10
            echo "        <Url type=\"text/html\" method=\"GET\" template=\"";
            echo twig_escape_filter($this->env, (strtr($this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "config", array(0 => "base_url"), "method"), array("%version%" => $this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "version"))) . "/index.html?q={searchTerms}&src={referrer:source?}"), "html", null, true);
            echo "\"/>
        <InputEncoding>UTF-8</InputEncoding>
        <AdultContent>false</AdultContent>
    </OpenSearchDescription>
";
        }
    }

    public function getTemplateName()
    {
        return "pages/opensearch.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  43 => 8,  41 => 7,  21 => 2,  379 => 58,  363 => 56,  358 => 55,  355 => 54,  350 => 53,  333 => 52,  331 => 51,  329 => 50,  318 => 49,  303 => 46,  291 => 45,  265 => 40,  261 => 39,  258 => 37,  255 => 35,  253 => 34,  236 => 33,  234 => 32,  222 => 31,  211 => 28,  205 => 27,  199 => 26,  185 => 25,  174 => 22,  168 => 21,  162 => 20,  148 => 19,  135 => 16,  133 => 15,  126 => 13,  119 => 11,  117 => 10,  104 => 9,  53 => 2,  42 => 1,  37 => 6,  34 => 44,  25 => 4,  19 => 1,  110 => 32,  103 => 30,  99 => 29,  90 => 27,  87 => 6,  83 => 25,  79 => 23,  64 => 16,  62 => 15,  58 => 14,  52 => 11,  49 => 10,  46 => 9,  40 => 7,  80 => 23,  76 => 22,  71 => 19,  60 => 13,  56 => 12,  50 => 9,  31 => 5,  94 => 28,  91 => 25,  84 => 8,  81 => 7,  75 => 5,  70 => 18,  68 => 18,  65 => 18,  47 => 8,  44 => 7,  38 => 5,  33 => 5,  22 => 8,  51 => 15,  39 => 6,  35 => 10,  32 => 4,  29 => 6,  28 => 24,);
    }
}
