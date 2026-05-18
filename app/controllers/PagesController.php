<?php
class PagesController extends Controller
{
    public function corporate()
    {
        $data = [
            'title' => 'Corporate Information',
            'description' => 'Learn more about MarketSphere and our mission.'
        ];
        $this->view('pages/corporate', $data);
    }

    public function help()
    {
        $data = [
            'title' => 'Help Center',
            'description' => 'Get help with your account, buying, or selling.'
        ];
        $this->view('pages/help', $data);
    }
}
