# Omdb-PHP
PHP API for omdb movie database with: movie info, imdb scores and rotten tomatoes scores

## Download

Add the library with composer

composer require danielvdbilt/omdb-php

## Usages

Finding movie by title, year is optional

```PHP
$Omdb   = new \Omdb();
$result = $Omdb->findByTitle('The Godfather', 1972);
```

Finding movie by IMDb ID

```PHP
$Omdb   = new \Omdb();
$result = $Omdb->findByID('tt0068646');
```

Find movie(s) by search key

```PHP
$Omdb   = new \Omdb();
$result = $Omdb->find('The Godfather');
```

Get poster (you need an API key for this), height is optional

```PHP
$Omdb   = new \Omdb();
$result = $Omdb->findPoster('tt0068646', 1000);
```
