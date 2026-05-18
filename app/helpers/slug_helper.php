<?php
function createSlug($string)
{
    $slug = strtolower($string);
    $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
    $slug = trim($slug, '-');
    $slug = $slug . '-' . uniqid(); // Ensure uniqueness
    return $slug;
}
