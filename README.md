laravel-fragment-caching
========================

Add a Fragment caching support helper.

Installation
==

Run: `composer require gchaincl/laravel-fragment-caching:dev-master`
or
 * add: 	`"require": { "gchaincl/laravel-fragment-caching": "dev-master" }, `to composer.json
 * and run: `composer install`

A new helper called `cache` will be available.

Usage
==

`cache($key, Closure)`
* `key` is the caching key
* `Closure` should output the text we want to render


In your view:
```php
<ul>
@foreach ($posts as $post)

<?php echo cache("post" . $post->id, function() use ($post) { ?>
    <li> {{ link_to_route('post.show', $post->title, $post->id) }} ({{ $post->user->username }})</li>
<?php }); ?>

@endforeach
</ul>
```

First time we load that view, Framwwork will run 3 queries:
```sql
select * from "posts"
select * from "users" where "users"."id" = '5' limit 1
select * from "users" where "users"."id" = '5' limit 1
```

Second time, as fragments are already cached, there will be just one query:
```sql
select * from "posts"
```

Tip
--

To update view rendering on model changes, you should expire your fragments:

```php
// app/model/Post.php

class Post extends Eloquent {

    public static function boot() {
        parent::boot();
        static::updated(function($model) {
            Cache::forget("post" . $model->id);
        });
    }
}
```

Warning
==

This software is an alpha release, that mean that it has no tests, no docs, api may change and world can collapse.
