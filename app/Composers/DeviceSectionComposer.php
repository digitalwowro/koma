<?php

namespace App\Composers;

use App\Permission;
use Illuminate\Contracts\View\View;
use App\DeviceSection;

class DeviceSectionComposer
{
    /**
     * The deviceSection model
     *
     * @var DeviceSection
     */
    protected $deviceSection;

    /**
     * Create a new composer.
     *
     * @param DeviceSection $deviceSection
     */
    public function __construct(DeviceSection $deviceSection)
    {
        $this->deviceSection = $deviceSection;
    }

    /**
     * Bind data to the view.
     *
     * @param View $view
     */
    public function admin(View $view)
    {
        $deviceSections = $this->deviceSection->pagedForAdmin();

        $view->with('deviceSections', $deviceSections);
    }

    /**
     * Display FAQs for frontend
     *
     * @param \Illuminate\Contracts\View\View $view
     */
    public function all(View $view)
    {
        $view->with('deviceSections', $this->deviceSection->getAll());
    }

}
