<?php

namespace App\Composers;

use Illuminate\Contracts\View\View;
use App\IpCategory;

class IpCategoryComposer
{
    /**
     * The IpCategory model
     *
     * @var IpCategory
     */
    protected $ipCategory;

    /**
     * Create a new composer.
     *
     * @param IpCategory $ipCategory
     */
    public function __construct(IpCategory $ipCategory)
    {
        $this->ipCategory = $ipCategory;
    }

    /**
     * Bind data to the view.
     *
     * @param View $view
     */
    public function admin(View $view)
    {
        $view->with('ipCategories', $this->ipCategory->pagedForAdmin());
    }

    /**
     * Display FAQs for frontend
     *
     * @param \Illuminate\Contracts\View\View $view
     */
    public function all(View $view)
    {
        $view->with('ipCategories', $this->ipCategory->getAll());
    }

}
