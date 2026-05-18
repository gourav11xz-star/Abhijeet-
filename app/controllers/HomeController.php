<?php
class HomeController extends Controller
{
    public function index()
    {
        $adModel = $this->model('Ad');
        $categoryModel = $this->model('Category');

        // Get Categories
        $categories = $categoryModel->getCategories();

        // Get Featured Ads (Random 4 for now, or real 'is_featured' if we had it populated)
        // Using getAds with limit/sort
        $featuredAds = $adModel->getAds(['limit' => 4, 'sort' => 'featured']);
        // Note: getAds doesn't strictly support 'sort' param in my previous read, it hardcodes order. 
        // But it orders by is_featured DESC. So top 4 are featured.

        // Get Recent Ads
        $recentAds = $adModel->getAds(['limit' => 8, 'offset' => 4]); // Skip first 4 (featured) to avoid dupes? Or just show latest.
        // Let's just get latest 8.
        $recentAds = $adModel->getAds(['limit' => 8]);

        $data = [
            'categories' => $categories,
            'featured' => $featuredAds, // actually first few might be featured
            'recent' => $recentAds
        ];

        $this->view('home/index', $data);
    }
}
