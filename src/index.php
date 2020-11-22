<?php

namespace Components;

function invariant($test, $message, $code = 500) {
  if (!$test) {
    throw new \Exception($message, $code);
  }
}

function mustBeString($variable, $name = '') {
  $message = ($name ? "`\$${name}`" : 'Variable')." must be a string";
  invariant(is_string($variable), $message);
}

function defaultValue ($value, $default) {
  if ($value === 0) {
    return 0;
  }

  return $value ? $value : $default;
}

function invokeReturn($value = null) {
  return is_callable($value) ? $value () : $value;
}

function valueWhen($valueWhenTrue, $valueWhenFalseOrCondition, $conditionWhen3Values = null) {
  $arguments = func_get_args();
  $argumentsCount = count($arguments);

  if ($argumentsCount > 3 || $argumentsCount < 2) {
    throw new \Exception("2 or 3 arguments are required", 500);
  }

  $true = $arguments[0];
  $false = $argumentsCount === 2 ? null : $arguments[1];
  $condition = $argumentsCount === 2 ? $arguments[1] : $arguments[2];

  if (is_callable($condition)) {
    try {
      return $condition() ? invokeReturn($true) : invokeReturn($false);
    } catch (\TypeError $th) {
      return invokeReturn($false);
    }
  }

  return $condition ? invokeReturn($true) : invokeReturn($false);
}


function tagProp($propName = '', $propValues = []) {
  if (!is_array($propValues)) {
    $propValues = [$propValues];
  }

  $propValues = array_filter(array_flatten($propValues), function($value) {
    return (is_string($value) && !empty($value)) || is_numeric($value);
  });

  if (empty($propValues)) {
    return;
  }

  $propName = preg_replace_callback('/[A-Z]+/', function(array $matches) {
    return (strlen($matches[0]) > 1 ? '-' : '').strtolower(substr($matches[0], 0, -1).'-'.substr($matches[0], -1));
  }, $propName);

  return "${propName}=\"".implode(" ", $propValues)."\"";
}

function tagProps(array $props = null) {
  if (!is_array($props)) {
    $props = [];
  }

  return trim(implode(' ', array_filter(array_map(function ($propName = '', $propValues) {
    if ($propName === 'children') {
      return;
    }

    return tagProp($propName, $propValues);
  }, array_keys($props), $props))));
}

function mergeProps(array $oldProps, array $newProps) {
  return array_merge($oldProps, $newProps);
}

function renderTag(string $tag = '', array $props = []) {
  mustBeString($tag, 'Tag name');

  $children = isset($props['children']) && !(empty($props['children']) && !is_numeric($props['children'])) ? $props['children'] : '';
  $attributes = tagProps($props);

  if (!empty($attributes)) {
    $attributes = ' '.$attributes;
  }

  if (in_array($tag, ['img', 'br', 'hr'])) {
    return "<${tag}${attributes} />";
  }

  if (is_array($children)) {
    $children = array_values(array_filter(array_flatten($children)));

    if (sizeof($children) === 1) {
      $children = trim($children[0]);
    } else {
      $children = implode("\n", $children);
    }
  }

  if (is_string($children) || is_numeric($children)) {
    if (!strstr($children, "\n") || ($children[0] !== '<' && $children[0] !== ' ')) {
      return "<${tag}${attributes}>".$children."</${tag}>";
    }

    $children = str_replace("\n", "\n  ", $children);

    return "<${tag}${attributes}>\n  ".$children."\n</${tag}>";
  }

  return;
}
