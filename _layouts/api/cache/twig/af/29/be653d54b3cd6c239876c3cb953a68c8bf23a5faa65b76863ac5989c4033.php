<?php

/* panel.twig */
class __TwigTemplate_af29be653d54b3cd6c239876c3cb953a68c8bf23a5faa65b76863ac5989c4033 extends Twig_Template
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
        echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Frameset//EN\" \"http://www.w3.org/TR/html4/frameset.dtd\">
<html lang=\"en\">
<head>
    <title>";
        // line 4
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "config", array(0 => "title"), "method"), "html", null, true);
        echo "</title>
    <link rel=\"stylesheet\" href=\"";
        // line 5
        echo twig_escape_filter($this->env, $this->env->getExtension('sami')->pathForStaticFile($context, "css/reset.css"), "html", null, true);
        echo "\" type=\"text/css\" media=\"screen\" charset=\"utf-8\">
    <link rel=\"stylesheet\" href=\"";
        // line 6
        echo twig_escape_filter($this->env, $this->env->getExtension('sami')->pathForStaticFile($context, "css/panel.css"), "html", null, true);
        echo "\" type=\"text/css\" media=\"screen\" charset=\"utf-8\">
    <script src=\"tree.js\" type=\"text/javascript\" charset=\"utf-8\"></script>
    <script src=\"";
        // line 8
        echo twig_escape_filter($this->env, $this->env->getExtension('sami')->pathForStaticFile($context, "js/jquery-1.3.2.min.js"), "html", null, true);
        echo "\" type=\"text/javascript\" charset=\"utf-8\"></script>
    <script src=\"";
        // line 9
        echo twig_escape_filter($this->env, $this->env->getExtension('sami')->pathForStaticFile($context, "js/searchdoc.js"), "html", null, true);
        echo "\" type=\"text/javascript\" charset=\"utf-8\"></script>
    <script type=\"text/javascript\" charset=\"utf-8\">
        //<![CDATA[
        \$(document).ready(function(){
            \$('#version-switcher').change(function() {
                window.parent.location = \$(this).val()
            })
        })
       \$(function() {
           \$.ajax({
             url: 'search_index.js',
             dataType: 'script',
             success: function () {
                 \$('.loader').css('display', 'none');
                 var panel = new Searchdoc.Panel(\$('#panel'), search_data, tree, parent.frames[1]);
                 \$('#search').focus();

                 for (var i=0; i < ";
        // line 26
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "config", array(0 => "default_opened_level", 1 => 0), "method"), "html", null, true);
        echo "; i++) {
                     \$('.level_' + i).each(function (\$li) {
                         panel.tree.toggle(\$(this));
                     });
                 }

                 var s = window.parent.location.search.match(/\\?q=([^&]+)/);
                 if (s) {
                     s = decodeURIComponent(s[1]).replace(/\\+/g, ' ');
                     if (s.length > 0)
                     {
                         \$('#search').val(s);
                         panel.search(s, true);
                     }
                 }
             }
           });
       })
        //]]>
    </script>
</head>
<body>
    <div class=\"panel panel_tree\" id=\"panel\">
        <div class=\"loader\">
            <img src=\"";
        // line 50
        echo twig_escape_filter($this->env, $this->env->getExtension('sami')->pathForStaticFile($context, "i/loader.gif"), "html", null, true);
        echo "\" /> loading...
        </div>
        <div class=\"header\">
            <div class=\"nav\">
                <h1>";
        // line 54
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "config", array(0 => "title"), "method"), "html", null, true);
        echo "</h1>
                ";
        // line 55
        if ((twig_length_filter($this->env, $this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "versions")) > 1)) {
            // line 56
            echo "                    <form action=\"#\" method=\"GET\">
                        <select id=\"version-switcher\" name=\"version\">
                            ";
            // line 58
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "versions"));
            foreach ($context['_seq'] as $context["_key"] => $context["version"]) {
                // line 59
                echo "                                <option value=\"../";
                echo twig_escape_filter($this->env, (isset($context["version"]) ? $context["version"] : $this->getContext($context, "version")), "html", null, true);
                echo "/index.html\"";
                echo ((((isset($context["version"]) ? $context["version"] : $this->getContext($context, "version")) == $this->getAttribute((isset($context["project"]) ? $context["project"] : $this->getContext($context, "project")), "version"))) ? (" selected") : (""));
                echo ">";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["version"]) ? $context["version"] : $this->getContext($context, "version")), "longname"), "html", null, true);
                echo "</option>
                            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['version'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 61
            echo "                        </select>
                    </form>
                ";
        }
        // line 64
        echo "                <div style=\"clear: both\"></div>
                <table>
                    <tr><td><input type=\"Search\" placeholder=\"Search\" autosave=\"searchdoc\" results=\"10\" id=\"search\" autocomplete=\"off\"></td></tr>
                </table>
            </div>
        </div>
        <div class=\"tree\">
            <ul>
            </ul>
        </div>
        <div class=\"result\">
            <ul>
            </ul>
        </div>
    </div>
</body>
</html>
";
    }

    public function getTemplateName()
    {
        return "panel.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  127 => 64,  122 => 61,  109 => 59,  24 => 4,  82 => 26,  93 => 27,  69 => 19,  63 => 5,  57 => 4,  98 => 27,  92 => 23,  85 => 23,  74 => 20,  61 => 26,  45 => 7,  144 => 39,  136 => 36,  129 => 35,  125 => 33,  118 => 32,  114 => 30,  105 => 58,  101 => 56,  95 => 54,  88 => 50,  72 => 18,  66 => 18,  55 => 14,  26 => 3,  43 => 8,  41 => 9,  21 => 2,  379 => 58,  363 => 56,  358 => 55,  355 => 54,  350 => 53,  333 => 52,  331 => 51,  329 => 50,  318 => 49,  303 => 46,  291 => 45,  265 => 40,  261 => 39,  258 => 37,  255 => 35,  253 => 34,  236 => 33,  234 => 32,  222 => 31,  211 => 28,  205 => 27,  199 => 26,  185 => 25,  174 => 22,  168 => 21,  162 => 20,  148 => 19,  135 => 16,  133 => 15,  126 => 13,  119 => 11,  117 => 10,  104 => 9,  53 => 12,  42 => 6,  37 => 8,  34 => 4,  25 => 4,  19 => 1,  110 => 32,  103 => 28,  99 => 55,  90 => 27,  87 => 24,  83 => 23,  79 => 21,  64 => 16,  62 => 16,  58 => 15,  52 => 13,  49 => 11,  46 => 9,  40 => 5,  80 => 23,  76 => 22,  71 => 19,  60 => 13,  56 => 12,  50 => 9,  31 => 5,  94 => 28,  91 => 25,  84 => 8,  81 => 7,  75 => 22,  70 => 19,  68 => 18,  65 => 17,  47 => 10,  44 => 9,  38 => 7,  33 => 5,  22 => 8,  51 => 12,  39 => 6,  35 => 4,  32 => 6,  29 => 6,  28 => 5,);
    }
}
