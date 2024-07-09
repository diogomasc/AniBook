<?php
// category_movies.php

class MovieCategory
{
    private static $categories = [
        "Ação",
        "Drama",
        "Comédia",
        "Fantasia / Ficção",
        "Romance",
        "Aventura",
        "Slice of Life",
        "Mistério",
        "Magia",
        "Shonen",
        "Shoujo",
        "Seinen",
        "Mecha",
        "Ecchi",
        "Isekai",
        "Harem",
        "Josei",
        "Kodomo",
        "Shoujo-ai",
        "Shounen-ai",
        "Yaoi",
        "Yuri",
        "Superpoderes",
        "Psicológico",
        "Escolar",
        "Esportes",
        "Sobrenatural",
        "Samurai",
        "Histórico",
        "Musical",
        "Policial",
        "Thriller",
        "Vampiros",
        "Space",
        "Cyberpunk"
    ];


    public static function getCategories()
    {
        return self::$categories;
    }

    public static function isSelected($category, $selectedCategory)
    {
        return $category === $selectedCategory ? "selected" : "";
    }
}
