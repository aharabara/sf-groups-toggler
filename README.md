# SF Groups Toggler

A CLI tool to toggle Symfony serialization groups on PHP entity properties.

## Installation

```bash
composer install
```

## Usage

```bash
php ./parse.php ./path/to/entity.php <propertyName> <comma-separated-groups>
```

### Examples

```bash
# Add 3 groups to the 'slug' property
php ./parse.php ./entity.php slug read,edit,list

# Toggle 'edit' group (removes it if present, adds it if absent)
php ./parse.php ./entity.php slug edit
```

## How It Works

- If the specified groups **are not present** on the property, they are **added**.
- If the specified groups **are already present**, they are **removed** (toggle).
- The file is modified in-place while preserving the original formatting.
