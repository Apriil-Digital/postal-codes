# Norwegian Postal Codes

An Eloquent model with every Norwegian postal code (`postnummer`), place name (`poststed`), and delivery category, ready to query in Laravel or any PHP app that uses Illuminate Database.

The dataset lives in memory via [Sushi](https://github.com/calebporzio/sushi)—no migrations, seeders, or external API calls. Install the package and query postal codes like any other Eloquent model.

## Features

- **5,122 postal codes** bundled in the package
- **Standard Eloquent API** — `find`, `where`, `exists`, relationships, and the rest
- **Zero database setup** — Sushi builds a temporary SQLite table from the embedded data
- **Leading zeros preserved** — postal codes are stored as four-character strings (e.g. `0001`, `7030`)
- **Delivery categories** — each code is tagged as street address, PO box, both, or service point via a typed `Category` enum

## Prerequisites

- **PHP** 8.1 or newer
- **PDO SQLite** extension (`pdo_sqlite`) — required by Sushi
- **Laravel** 8.69 or newer

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

$postal = PostalCode::find('0001');

$postal->id;       // "0001"
$postal->name;     // "Oslo"
$postal->category; // Category::Mailbox
```

### Filter by category

Each postal code has a `category` that describes what kind of delivery it supports. The value is cast to the `Category` backed enum:

```php
use Apriil\PostalCodes\Enums\Category;
use Apriil\PostalCodes\PostalCode;

// Street-address codes only
$streetCodes = PostalCode::where('category', Category::Address)->get();

// PO box codes in Oslo
$osloPostboxes = PostalCode::where('name', 'Oslo')
    ->where('category', Category::Mailbox)
    ->get();

// Compare on the model
$postal = PostalCode::find('0050');
$postal->category === Category::Address; // true
```

| Enum case | Stored value | Meaning |
|-----------|--------------|---------|
| `Category::Address` | `G` | Street address delivery only |
| `Category::Mailbox` | `P` | PO box (`postboks`) delivery only |
| `Category::Both` | `B` | Both street address and PO box |
| `Category::ServicePoint` | `S` | Service point / pickup location |

Most codes are street-address (`G`) or PO box (`P`). `Both` and `ServicePoint` are less common but matter when you need to restrict which codes users can enter (for example, only `G` for home delivery forms).

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
    ->get(['id', 'name', 'category']);
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

| Column     | Type       | Description                                      |
|------------|------------|--------------------------------------------------|
| `id`       | string     | Four-digit Norwegian postal code (primary key) |
| `name`     | string     | Place name (`poststed`)                          |
| `category` | `Category` | Delivery type (`G`, `P`, `B`, or `S`) — see above |

The model does not use timestamps (`created_at` / `updated_at`).

## License

MIT © [Apriil Digital](https://apriil.no)
