<?php

namespace AymanAlhattami\FilamentPageWithSidebar\Traits;

use AymanAlhattami\FilamentPageWithSidebar\Enums\PageSidebarLayoutEnum;
use AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar;
use Illuminate\Database\Eloquent\Model;

trait HasPageSidebar
{
    /**
     * Activate or not the automatic sidebar to the page
     * If you change it to FALSE then add manually the $view
     *  parameter
     * @var boolean
     */

    public static bool $hasSidebar = true;

    public static PageSidebarLayoutEnum $sidebarNavigationLayout = PageSidebarLayoutEnum::SIDE;

    /**
     * public function mountHasPageSidebar
     * 
     *   Register automatically view if available and activated
     */
    public function bootHasPageSidebar(): void
    {
        // Why boot ? https://livewire.laravel.com/docs/lifecycle-hooks#boot

        // Using ${'view'} instead of $view in order to avoid Intelephense warning
        if (static::$hasSidebar) {
            static::${'view'} = 'filament-page-with-sidebar::proxy';
        }
    }

    /**
     * public function getIncludedSidebarView
     * 
     *   Return the view that will be used in the sidebar proxy.
     * 
     * @Return string \Filament\Pages\Page View to be included
     */
    public function getIncludedSidebarView(): string
    {
        if (is_subclass_of($this, '\Filament\Pages\Page')) {
            $props = collect(
                (new \ReflectionClass($this))->getDefaultProperties()
            );

            if ($props->get('view')) {
                return $props->get('view');
            }
        }

        // Else:
        throw new \Exception('No view detected for the Sidebar. Implement Filament\Pages\Page object with valid static $view');
    }

    static public function getSidebarNavigationLayout(): PageSidebarLayoutEnum
    {
        return static::$sidebarNavigationLayout;
    }

    static public function getSidebarNavigation(FilamentPageSidebar $builder)
    {
        return $builder;
    }

    static public function getSidebarSubNavigation(FilamentPageSidebar $builder)
    {
        return $builder;
    }

    static public function sidebar(Model $record): FilamentPageSidebar
    {
        return FilamentPageSidebar::make();
    }
}
