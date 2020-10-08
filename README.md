# Render HTML Tag for PHP

Render (HTML) tags with proper indenting. It resembles React.createElement( component, props, ...children) API except the children argument.


## API

### `function renderTag(string $tag = '', array $props = [])`

- *string* `$htmlTag` – div, span, section, header... name it
- *array* `$props` – Associative array of attributes and attribute values. See [Props](#props) for example.

### `function mergeProps(array $oldProps, array $newProps)`
Alias for [array_merge()](https://www.php.net/manual/en/function.array-merge.php).

### `function valueWhen(mixed $valueIfTrue, boolean|mixed $condition)`
Ternary-like operator helper, when all you need to pick value if true otherwise passe `null`. Sometimes this is more readable. Pick your choice.

### `function defaultValue ($value, $default)`
Returns value or the default when value is not present.

When passed 3 params, works as ternary: `function valueWhen(mixed $valueIfTrue, mixed $valueIfFalse, boolean|mixed $condition)`.


## Props

Props (arguments) should be an associative array of attributes and attribute values.

```php
$props = [
  "id" => 'single-value',
  "class" => [
    'multiple',
    null, // Empty values are skipped
    'values',
  ],
  "children" => 'Text inside',
  ...
]

echo renderTag('p', $props);
```

Renders:

```html
<p id="single-value" class="multiple values">Text inside</p>
```

