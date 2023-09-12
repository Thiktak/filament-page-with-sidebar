@php
    $sidebar = \AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar::make($this); //static::getResource()::sidebar($this->record);
    [$navigation, $subNavigation] = $sidebar->getSidebarNavigation(); //SidebarNavigation();
    
    $isTopNavigation = $sidebar->isLayout(\AymanAlhattami\FilamentPageWithSidebar\Enums\PageSidebarLayoutEnum::TOP);
@endphp

<div class="mt-4">
    @switch($isTopNavigation)
        @case(true)
            <x-filament-page-with-sidebar::topbar :navigation="$navigation" :hasNavigation="true" :title="$sidebar->getTitle()" :description="$sidebar->getDescription()" />

            <div style="margin-top: -2em">
                {{ $slot }}
            </div>
        @break

        @case(false)
            <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6 mt-8">
                <div
                    class="col-span-12 md:col-span-{{ config('filament-page-with-sidebar.sidebar_width.md') }} lg:col-span-{{ config('filament-page-with-sidebar.sidebar_width.lg') }} xl:col-span-{{ config('filament-page-with-sidebar.sidebar_width.xl') }} 2xl:col-span-{{ config('filament-page-with-sidebar.sidebar_width.2xl') }} rounded">
                    <div>
                        <x-filament-page-with-sidebar::sidebar :navigation="$navigation" :hasNavigation="!$isTopNavigation" :title="$sidebar->getTitle()"
                            :description="$sidebar->getDescription()" />
                    </div>
                </div>

                <div
                    class="col-span-12 md:col-span-{{ 12 - config('filament-page-with-sidebar.sidebar_width.md') }} lg:col-span-{{ 12 - config('filament-page-with-sidebar.sidebar_width.lg') }} xl:col-span-{{ 12 - config('filament-page-with-sidebar.sidebar_width.xl') }} 2xl:col-span-{{ 12 - config('filament-page-with-sidebar.sidebar_width.2xl') }}">

                    @if ($subNavigation)
                        <x-filament-page-with-sidebar::topbar :navigation="$subNavigation" :hasNavigation="true" />
                    @endif

                    <div style="margin-top: -2em">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        @break

    @endswitch
</div>
