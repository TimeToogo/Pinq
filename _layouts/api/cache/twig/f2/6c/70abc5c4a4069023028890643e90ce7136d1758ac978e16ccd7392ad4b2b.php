<?php

/* macros.twig */
class __TwigTemplate_f26c70abc5c4a4069023028890643e90ce7136d1758ac978e16ccd7392ad4b2b extends Twig_Template
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
        // line 4
        echo "
";
        // line 8
        echo "
";
        // line 18
        echo "
";
        // line 24
        echo "
";
        // line 30
        echo "
";
        // line 44
        echo "
";
        // line 48
        echo "
";
    }

    // line 1
    public function getattributes($_attributes = null)
    {
        $context = $this->env->mergeGlobals(array(
            "attributes" => $_attributes,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 2
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["attributes"]) ? $context["attributes"] : $this->getContext($context, "attributes")));
            foreach ($context['_seq'] as $context["key"] => $context["value"]) {
                echo " ";
                echo twig_escape_filter($this->env, (isset($context["key"]) ? $context["key"] : $this->getContext($context, "key")), "html", null, true);
                echo "=\"";
                echo twig_escape_filter($this->env, (isset($context["value"]) ? $context["value"] : $this->getContext($context, "value")), "html", null, true);
                echo "\"";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['key'], $context['value'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 5
    public function getnamespace_link($_namespace = null, $_attributes = null)
    {
        $context = $this->env->mergeGlobals(array(
            "namespace" => $_namespace,
            "attributes" => $_attributes,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 6
            echo "<a href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('sami')->pathForNamespace($context, (isset($context["namespace"]) ? $context["namespace"] : $this->getContext($context, "namespace"))), "html", null, true);
            echo "\"";
            echo $this->getAttribute($this, "attributes", array(0 => (isset($context["attributes"]) ? $context["attributes"] : $this->getContext($context, "attributes"))), "method");
            echo ">";
            echo twig_escape_filter($this->env, (isset($context["namespace"]) ? $context["namespace"] : $this->getContext($context, "namespace")), "html", null, true);
            echo "</a>";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 9
    public function getclass_link($_class = null, $_attributes = null, $_absolute = null)
    {
        $context = $this->env->mergeGlobals(array(
            "class" => $_class,
            "attributes" => $_attributes,
            "absolute" => $_absolute,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 10
            if ($this->getAttribute((isset($context["class"]) ? $context["class"] : $this->getContext($context, "class")), "projectclass")) {
                // line 11
                echo "<a href=\"";
                echo twig_escape_filter($this->env, $this->env->getExtension('sami')->pathForClass($context, (isset($context["class"]) ? $context["class"] : $this->getContext($context, "class"))), "html", null, true);
                echo "\"";
                echo $this->getAttribute($this, "attributes", array(0 => (isset($context["attributes"]) ? $context["attributes"] : $this->getContext($context, "attributes"))), "method");
                echo ">";
            } elseif ($this->getAttribute((isset($context["class"]) ? $context["class"] : $this->getContext($context, "class")), "phpclass")) {
                // line 13
                echo "<a href=\"http://php.net/";
                echo twig_escape_filter($this->env, (isset($context["class"]) ? $context["class"] : $this->getContext($context, "class")), "html", null, true);
                echo "\"";
                echo $this->getAttribute($this, "attributes", array(0 => (isset($context["attributes"]) ? $context["attributes"] : $this->getContext($context, "attributes"))), "method");
                echo ">";
            }
            // line 15
            echo $this->getAttribute($this, "abbr_class", array(0 => (isset($context["class"]) ? $context["class"] : $this->getContext($context, "class")), 1 => ((array_key_exists("absolute", $context)) ? (_twig_default_filter((isset($context["absolute"]) ? $context["absolute"] : $this->getContext($context, "absolute")), false)) : (false))), "method");
            // line 16
            if (($this->getAttribute((isset($context["class"]) ? $context["class"] : $this->getContext($context, "class")), "projectclass") || $this->getAttribute((isset($context["class"]) ? $context["class"] : $this->getContext($context, "class")), "phpclass"))) {
                echo "</a>";
            }
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 19
    public function getmethod_link($_method = null, $_attributes = null, $_absolute = null, $_classonly = null)
    {
        $context = $this->env->mergeGlobals(array(
            "method" => $_method,
            "attributes" => $_attributes,
            "absolute" => $_absolute,
            "classonly" => $_classonly,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 20
            echo "<a href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('sami')->pathForMethod($context, (isset($context["method"]) ? $context["method"] : $this->getContext($context, "method"))), "html", null, true);
            echo "\"";
            echo $this->getAttribute($this, "attributes", array(0 => (isset($context["attributes"]) ? $context["attributes"] : $this->getContext($context, "attributes"))), "method");
            echo ">";
            // line 21
            echo $this->getAttribute($this, "abbr_class", array(0 => $this->getAttribute((isset($context["method"]) ? $context["method"] : $this->getContext($context, "method")), "class")), "method");
            if ((!((array_key_exists("classonly", $context)) ? (_twig_default_filter((isset($context["classonly"]) ? $context["classonly"] : $this->getContext($context, "classonly")), false)) : (false)))) {
                echo "::";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["method"]) ? $context["method"] : $this->getContext($context, "method")), "name"), "html", null, true);
            }
            // line 22
            echo "</a>";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 25
    public function getproperty_link($_property = null, $_attributes = null, $_absolute = null, $_classonly = null)
    {
        $context = $this->env->mergeGlobals(array(
            "property" => $_property,
            "attributes" => $_attributes,
            "absolute" => $_absolute,
            "classonly" => $_classonly,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 26
            echo "<a href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('sami')->pathForProperty($context, (isset($context["property"]) ? $context["property"] : $this->getContext($context, "property"))), "html", null, true);
            echo "\"";
            echo $this->getAttribute($this, "attributes", array(0 => (isset($context["attributes"]) ? $context["attributes"] : $this->getContext($context, "attributes"))), "method");
            echo ">";
            // line 27
            echo $this->getAttribute($this, "abbr_class", array(0 => $this->getAttribute((isset($context["property"]) ? $context["property"] : $this->getContext($context, "property")), "class")), "method");
            if ((!((array_key_exists("classonly", $context)) ? (_twig_default_filter((isset($context["classonly"]) ? $context["classonly"] : $this->getContext($context, "classonly")), true)) : (true)))) {
                echo "#";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["property"]) ? $context["property"] : $this->getContext($context, "property")), "name"), "html", null, true);
            }
            // line 28
            echo "</a>";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 31
    public function gethint_link($_hints = null, $_attributes = null)
    {
        $context = $this->env->mergeGlobals(array(
            "hints" => $_hints,
            "attributes" => $_attributes,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 32
            if ((isset($context["hints"]) ? $context["hints"] : $this->getContext($context, "hints"))) {
                // line 33
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable((isset($context["hints"]) ? $context["hints"] : $this->getContext($context, "hints")));
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
                foreach ($context['_seq'] as $context["_key"] => $context["hint"]) {
                    // line 34
                    if ($this->getAttribute((isset($context["hint"]) ? $context["hint"] : $this->getContext($context, "hint")), "class")) {
                        // line 35
                        echo $this->getAttribute($this, "class_link", array(0 => $this->getAttribute((isset($context["hint"]) ? $context["hint"] : $this->getContext($context, "hint")), "name")), "method");
                    } elseif ($this->getAttribute((isset($context["hint"]) ? $context["hint"] : $this->getContext($context, "hint")), "name")) {
                        // line 37
                        echo $this->env->getExtension('sami')->abbrClass($this->getAttribute((isset($context["hint"]) ? $context["hint"] : $this->getContext($context, "hint")), "name"));
                    }
                    // line 39
                    if ($this->getAttribute((isset($context["hint"]) ? $context["hint"] : $this->getContext($context, "hint")), "array")) {
                        echo "[]";
                    }
                    // line 40
                    if ((!$this->getAttribute((isset($context["loop"]) ? $context["loop"] : $this->getContext($context, "loop")), "last"))) {
                        echo "|";
                    }
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
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['hint'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
            }
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 45
    public function getabbr_class($_class = null, $_absolute = null)
    {
        $context = $this->env->mergeGlobals(array(
            "class" => $_class,
            "absolute" => $_absolute,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 46
            echo "<abbr title=\"";
            echo twig_escape_filter($this->env, (isset($context["class"]) ? $context["class"] : $this->getContext($context, "class")), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, ((((array_key_exists("absolute", $context)) ? (_twig_default_filter((isset($context["absolute"]) ? $context["absolute"] : $this->getContext($context, "absolute")), false)) : (false))) ? ((isset($context["class"]) ? $context["class"] : $this->getContext($context, "class"))) : ($this->getAttribute((isset($context["class"]) ? $context["class"] : $this->getContext($context, "class")), "shortname"))), "html", null, true);
            echo "</abbr>";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 49
    public function getmethod_parameters_signature($_method = null)
    {
        $context = $this->env->mergeGlobals(array(
            "method" => $_method,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 50
            $context["__internal_fcf7affb7cde3e89be6702cd39c58584cf309adbbc464d868e4ab9b28c091a3c"] = $this->env->loadTemplate("macros.twig");
            // line 51
            echo "(";
            // line 52
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["method"]) ? $context["method"] : $this->getContext($context, "method")), "parameters"));
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
            foreach ($context['_seq'] as $context["_key"] => $context["parameter"]) {
                // line 53
                if ($this->getAttribute((isset($context["parameter"]) ? $context["parameter"] : $this->getContext($context, "parameter")), "hashint")) {
                    echo $context["__internal_fcf7affb7cde3e89be6702cd39c58584cf309adbbc464d868e4ab9b28c091a3c"]->gethint_link($this->getAttribute((isset($context["parameter"]) ? $context["parameter"] : $this->getContext($context, "parameter")), "hint"));
                    echo " ";
                }
                // line 54
                echo "\$";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["parameter"]) ? $context["parameter"] : $this->getContext($context, "parameter")), "name"), "html", null, true);
                // line 55
                if ($this->getAttribute((isset($context["parameter"]) ? $context["parameter"] : $this->getContext($context, "parameter")), "default")) {
                    echo " = ";
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["parameter"]) ? $context["parameter"] : $this->getContext($context, "parameter")), "default"), "html", null, true);
                }
                // line 56
                if ((!$this->getAttribute((isset($context["loop"]) ? $context["loop"] : $this->getContext($context, "loop")), "last"))) {
                    echo ", ";
                }
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['parameter'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 58
            echo ")";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    public function getTemplateName()
    {
        return "macros.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  379 => 58,  363 => 56,  358 => 55,  355 => 54,  350 => 53,  333 => 52,  331 => 51,  329 => 50,  318 => 49,  303 => 46,  291 => 45,  265 => 40,  261 => 39,  258 => 37,  255 => 35,  253 => 34,  236 => 33,  234 => 32,  222 => 31,  211 => 28,  205 => 27,  199 => 26,  185 => 25,  174 => 22,  168 => 21,  162 => 20,  148 => 19,  135 => 16,  133 => 15,  126 => 13,  119 => 11,  117 => 10,  104 => 9,  53 => 2,  42 => 1,  37 => 48,  34 => 44,  25 => 18,  19 => 4,  110 => 32,  103 => 30,  99 => 29,  90 => 27,  87 => 6,  83 => 25,  79 => 23,  64 => 16,  62 => 15,  58 => 14,  52 => 11,  49 => 10,  46 => 9,  40 => 7,  80 => 23,  76 => 22,  71 => 19,  60 => 13,  56 => 12,  50 => 9,  31 => 30,  94 => 28,  91 => 25,  84 => 8,  81 => 7,  75 => 5,  70 => 18,  68 => 18,  65 => 18,  47 => 8,  44 => 7,  38 => 5,  33 => 5,  22 => 8,  51 => 15,  39 => 6,  35 => 10,  32 => 4,  29 => 6,  28 => 24,);
    }
}
