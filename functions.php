<?php

function dump(...$vars)
{
    $style = "
        <style>
            .dump-container {
                margin: 15px 0;
                font-family: 'Monaco', 'Menlo', 'Consolas', 'Courier New', monospace;
                font-size: 13px;
                line-height: 1.4;
            }
            .dump-header {
                background: #e65100;
                color: white;
                padding: 8px 12px;
                border-radius: 4px 4px 0 0;
                font-weight: bold;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .dump-content {
                background: #fff8e1;
                padding: 12px;
                border: 1px solid #ffd54f;
                border-top: none;
                border-radius: 0 0 4px 4px;
                overflow-x: auto;
                white-space: pre-wrap;
                word-wrap: break-word;
            }
            .dump-string { color: #388e3c; font-weight: bold; }
            .dump-integer { color: #1976d2; font-weight: bold; }
            .dump-float { color: #0097a7; font-weight: bold; }
            .dump-boolean { color: #f57c00; font-weight: bold; }
            .dump-null { color: #7b1fa2; font-weight: bold; }
            .dump-array { color: #5d4037; }
            .dump-object { color: #c2185b; }
            .dump-resource { color: #455a64; }
            .dump-bracket { color: #616161; }
            .dump-key { color: #d81b60; }
            .dump-index { color: #00897b; }
            .dump-length { color: #757575; font-style: italic; }
            .dump-class { color: #c2185b; font-weight: bold; }
            .dump-property { color: #7b1fa2; }
            .dump-visibility { color: #757575; font-size: 11px; }
            .dump-method { color: #1976d2; font-style: italic; }
            .dump-object-id { color: #00897b; font-size: 11px; }
            .dump-inherited { opacity: 0.7; }
            .dump-private { background: #ffebee; padding: 1px 4px; border-radius: 2px; }
            .dump-protected { background: #fff3e0; padding: 1px 4px; border-radius: 2px; }
        </style>
    ";

    static $styleIncluded = false;
    if (!$styleIncluded) {
        echo $style;
        $styleIncluded = true;
    }

    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
    $file = basename($backtrace['file']);
    $line = $backtrace['line'];

    foreach ($vars as $index => $var) {
        $output = highlightDump($var);

        echo "<div class='dump-container'>";
        echo "<div class='dump-header'>";
        echo "<span>DUMP #" . ($index + 1) . " - {$file}:{$line}</span>";
        echo "<span>" . date('H:i:s') . "</span>";
        echo "</div>";
        echo "<div class='dump-content'>" . $output . "</div>";
        echo "</div>";
    }
}

function highlightDump($var, $depth = 0, $isKey = false, $parentObject = null) {
    if ($isKey) {
        return "<span class='dump-key'>'" . htmlspecialchars($var) . "'</span>";
    }

    if (is_null($var)) {
        return "<span class='dump-null'>null</span>";
    }

    if (is_bool($var)) {
        return "<span class='dump-boolean'>" . ($var ? 'true' : 'false') . "</span>";
    }

    if (is_int($var)) {
        return "<span class='dump-integer'>int(" . $var . ")</span>";
    }

    if (is_float($var)) {
        return "<span class='dump-float'>float(" . $var . ")</span>";
    }

    if (is_string($var)) {
        $length = strlen($var);
        $display = htmlspecialchars($var);
        if (strlen($var) > 100) {
            $display = htmlspecialchars(substr($var, 0, 100)) . "...";
        }
        return "<span class='dump-string'>string({$length})</span> \"{$display}\"";
    }

    if (is_array($var)) {
        $count = count($var);
        if ($count === 0) {
            return "<span class='dump-array'>array(0)</span> <span class='dump-bracket'>[]</span>";
        }

        $output = "<span class='dump-array'>array({$count})</span> <span class='dump-bracket'>[</span>";
        if ($depth < 3) {
            $items = [];
            $counter = 0;
            foreach ($var as $key => $value) {
                if ($counter++ > 10) {
                    $items[] = "...";
                    break;
                }
                $keyOutput = is_int($key) ?
                    "<span class='dump-index'>[{$key}]</span>" :
                    "<span class='dump-key'>['" . htmlspecialchars($key) . "']</span>";
                $items[] = $keyOutput . " => " . highlightDump($value, $depth + 1);
            }
            $output .= "\n" . str_repeat("  ", $depth + 1) . implode(",\n" . str_repeat("  ", $depth + 1), $items) . "\n" . str_repeat("  ", $depth) . "<span class='dump-bracket'>]</span>";
        } else {
            $output .= " <span class='dump-length'>...</span> <span class='dump-bracket'>]</span>";
        }
        return $output;
    }

    if (is_object($var)) {
        return formatObject($var, $depth);
    }

    if (is_resource($var)) {
        $type = get_resource_type($var);
        return "<span class='dump-resource'>resource({$type})</span>";
    }

    return htmlspecialchars(strval($var));
}

function formatObject($object, $depth = 0) {
    $class = get_class($object);
    $hash = spl_object_hash($object);
    $reflection = new ReflectionClass($object);

    $output = "<span class='dump-class'>object({$class})</span> ";
    $output .= "<span class='dump-object-id'>#{$hash}</span> ";
    $output .= "<span class='dump-bracket'>{</span>\n";

    if ($depth < 2) {
        $properties = [];

        // Получаем все свойства (включая унаследованные)
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);

            $visibility = '';
            $cssClass = '';

            if ($property->isPrivate()) {
                $visibility = 'private';
                $cssClass = 'dump-private';
            } elseif ($property->isProtected()) {
                $visibility = 'protected';
                $cssClass = 'dump-protected';
            } else {
                $visibility = 'public';
            }

            $inherited = $property->getDeclaringClass()->getName() !== $class ? 'dump-inherited' : '';

            try {
                $value = $property->getValue($object);
                $valueOutput = highlightDump($value, $depth + 1, false, $object);
            } catch (Exception $e) {
                $valueOutput = "<span style='color: #f44336;'>[uninitialized]</span>";
            }

            $propertyName = "<span class='dump-property {$cssClass} {$inherited}'>\"{$property->getName()}\"</span>";
            $visibilitySpan = "<span class='dump-visibility'>{$visibility}</span>";

            $properties[] = str_repeat("  ", $depth + 1) . "{$propertyName} {$visibilitySpan} => {$valueOutput}";
        }

        // Получаем методы (только для первого уровня)
        if ($depth === 0) {
            $methods = [];
            foreach ($reflection->getMethods() as $method) {
                $modifiers = Reflection::getModifierNames($method->getModifiers());
                $visibility = implode(' ', $modifiers);
                $inherited = $method->getDeclaringClass()->getName() !== $class ? 'dump-inherited' : '';

                $methods[] = str_repeat("  ", $depth + 1) .
                    "<span class='dump-method {$inherited}'>\"{$method->getName()}\"</span> " .
                    "<span class='dump-visibility'>({$visibility})</span>";
            }

            // Ограничиваем количество методов для вывода
            if (count($methods) > 5) {
                $methods = array_slice($methods, 0, 5);
                $methods[] = str_repeat("  ", $depth + 1) . "... " . (count($reflection->getMethods()) - 5) . " more methods";
            }

            if (!empty($methods)) {
                $properties[] = "";
                $properties[] = str_repeat("  ", $depth + 1) . "<span class='dump-length'>// methods:</span>";
                $properties = array_merge($properties, $methods);
            }
        }

        // Получаем константы класса
        $constants = $reflection->getConstants();
        if (!empty($constants) && $depth === 0) {
            $constantLines = [];
            foreach ($constants as $name => $value) {
                $constantLines[] = str_repeat("  ", $depth + 1) .
                    "<span class='dump-key'>const {$name}</span> = " .
                    highlightDump($value, $depth + 1);
            }

            if (!empty($constantLines)) {
                $properties[] = "";
                $properties[] = str_repeat("  ", $depth + 1) . "<span class='dump-length'>// constants:</span>";
                $properties = array_merge($properties, $constantLines);
            }
        }

        // Ограничиваем общее количество выводимых свойств
        if (count($properties) > 15) {
            $properties = array_slice($properties, 0, 15);
            $properties[] = str_repeat("  ", $depth + 1) . "...";
        }

        $output .= implode("\n", $properties) . "\n" . str_repeat("  ", $depth);
    } else {
        $output .= str_repeat("  ", $depth + 1) . "<span class='dump-length'>... (object too deep)</span>\n" . str_repeat("  ", $depth);
    }

    $output .= "<span class='dump-bracket'>}</span>";

    return $output;
}