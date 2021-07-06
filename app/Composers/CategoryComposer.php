<?php

namespace App\Composers;

use Illuminate\Contracts\View\View;
use App\Category;

class CategoryComposer
{
    /**
     * The category model
     *
     * @var Category
     */
    protected $category;

    /**
     * Create a new composer.
     *
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Bind data to the view.
     *
     * @param View $view
     */
    public function admin(View $view)
    {
        $categories = $this->category->pagedForAdmin();

        $view->with('categories', $categories);
    }

    /**
     * Display FAQs for frontend
     *
     * @param \Illuminate\Contracts\View\View $view
     */
    public function all(View $view)
    {
        $view->with('categories', $this->category->getAll());
    }

}
