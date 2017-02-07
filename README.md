# Sluggable
Automatically create slugs for your Eloquent models by hooking into the creating event

## Installation
Simply run `composer require sachiano/sluggable` in your Laravel project.

## Usage
To add automatic slug generation to a model, use the `Sachiano\Sluggable\Sluggable` trait.

```php
namespace Acme\Models;

use Illuminate\Database\Eloquent\Model;
use Sachiano\Sluggable\Sluggable;

class Post extends Model
{
    use Sluggable;
}
```

Now when you create a new model that uses the trait, it will look for a `name` column and generate a new slug, which it will save in the `slug` column.

```php
$post = Post::create([
    'name' => 'My first post'
]);

$post->slug; // my-first-post
```

### Custom columns

If you would like to use different column names for your origin string and the slug, you can set the protected attributes `$slugOrigin` and `$slugColumn` on your model. 

```php
namespace Acme\Models;

use Illuminate\Database\Eloquent\Model;
use Sachiano\Sluggable\Sluggable;

class Post extends Model
{
    use Sluggable;
    
    protected $slugOrigin = 'title';
    
    protected $slugColumn = 'post_name';
}
```
#### Example
```php
$post = Post::create([
    'title' => 'My second post'
]);

$post->post_name; // my-second-post
```

### Existing slugs
If a slug exists for the model, it will automatically append an integer to the end of the slug until a unique one is generated.

```php
// my-second-post already exists

$post = Post::create([
    'title' => 'My second post'
]);

$post->post_name; // my-second-post-1

$post = Post::create([
    'title' => 'My second post'
]);

$post->post_name; // my-second-post-2

// ...and so on
```


### Explicitly setting slugs
You can explicitly set a slug when creating a new model and the automatic generation will be skipped

```php
$post = Post::create([
    'name' => 'How to explicitly set a slug',
    'slug' => 'explicitly-setting-slugs-with-sluggable'
]);

$post->slug; // explicitly-setting-slugs-with-sluggable
```

### Empty origin
If the origin column is empty, a `Sachiano\Sluggable\EmptyOriginException` will be thrown.