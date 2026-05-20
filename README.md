# Norwegian Postal Codes

An Eloquent model with every Norwegian postal code (`postnummer`) and place name (`poststed`), ready to query in Laravel or any PHP app that uses Illuminate Database.

The dataset lives in memory via [Sushi](https://github.com/calebporzio/sushi)—no migrations, seeders, or external API calls. Install the package and query postal codes like any other Eloquent model.

## Features

- **5,122 postal codes** bundled in the package
- **Standard Eloquent API** — `find`, `where`, `exists`, relationships, and the rest
- **Zero database setup** — Sushi builds a temporary SQLite table from the embedded data
- **Leading zeros preserved** — postal codes are stored as four-character strings (e.g. `0001`, `7030`)

## Prerequisites

- **PHP** 7.4 or newer
- **PDO SQLite** extension (`pdo_sqlite`) — required by Sushi
- **Laravel** 6.0 or newer

## Installation

Install via Composer:

```bash
composer require apriil/postal-codes
```

## Usage

Import the model and use familiar Eloquent methods.

### Look up a postal code

```php
use Apriil\PostalCodes\PostalCode;

$postal = PostalCode::find('0150');

$postal->id;   // "0150"
$postal->name; // "Oslo"
```

### Validate that a postal code exists

```php
$isValid = PostalCode::where('id', '7030')->exists();
```

### Find all codes for a place

Many places share one name (e.g. several codes for Oslo):

```php
$osloCodes = PostalCode::where('name', 'Oslo')->get();
```

### Use in validation (Laravel)

```php
use Apriil\PostalCodes\PostalCode;
use Illuminate\Validation\Rule;

$request->validate([
    'postal_code' => [
        'required',
        'digits:4',
        Rule::exists(PostalCode::class, 'id'),
    ],
]);
```

### Autocomplete or select options

```php
$postalCodes = PostalCode::query()
    ->orderBy('name')
    ->orderBy('id')
    ->get(['id', 'name']);
```

### Relationships

Treat `PostalCode` like any other Eloquent model. For example, a `User` model can reference a postal code:

```php
use Apriil\PostalCodes\PostalCode;

class User extends Model
{
    public function postalCode()
    {
        return $this->belongsTo(PostalCode::class, 'postal_code', 'id');
    }
}
```

## Model reference

| Column | Type   | Description                                      |
|--------|--------|--------------------------------------------------|
| `id`   | string | Four-digit Norwegian postal code (primary key) |
| `name` | string | Place name (`poststed`)                          |

The model does not use timestamps (`created_at` / `updated_at`).

## License

MIT © [Apriil Digital](https://apriil.no)
