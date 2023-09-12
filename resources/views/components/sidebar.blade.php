@props(['navigation'])
@props(['sidebarName'])
@props(['isMainSidebar' => false, 'isSidebarCollapsibleOnDesktop' => false, 'hasTopNavigation' => false, 'isSidebarFullyCollapsibleOnDesktop' => false, 'hasTenancy' => false, 'hasNavigation' => false])
@props(['title' => null, 'description' => null])

@php
    $openSidebarClasses = 'fi-sidebar-open max-w-none translate-x-0 shadow-xl ring-1 ring-gray-950/5 rtl:-translate-x-0 dark:ring-white/10';
    $isRtl = __('filament-panels::layout.direction') === 'rtl';
@endphp


<aside x-data="{}"
    @if ($isSidebarCollapsibleOnDesktop) x-cloak
        x-bind:class="
            $store.sidebar.isOpen
                ? @js($openSidebarClasses)
                : 'lg:max-w-[--collapsed-sidebar-width] -translate-x-full rtl:translate-x-full lg:translate-x-0 rtl:lg:-translate-x-0'
        "
    @else
        @if ($hasTopNavigation || $isSidebarFullyCollapsibleOnDesktop)
            x-cloak
        @else
            x-cloak="-lg" @endif
    x-bind:class="$store.sidebar.isOpen ? @js($openSidebarClasses) : '-translate-x-full rtl:translate-x-full'" @endif
    @class([
        'fi-sidebar z-30 grid content-start bg-white transition-all dark:bg-gray-900 lg:z-0 lg:bg-transparent lg:shadow-none lg:ring-0 dark:lg:bg-transparent',
        'lg:translate-x-0 rtl:lg:-translate-x-0' => !(
            $isSidebarCollapsibleOnDesktop ||
            $isSidebarFullyCollapsibleOnDesktop ||
            $hasTopNavigation
        ),
        'lg:-translate-x-full rtl:lg:translate-x-full' => $hasTopNavigation,
        // For main sidebar:
        'w-[--sidebar-width] inset-y-0 start-0 h-screen fixed' => $isMainSidebar,
    ])
    >
    <header @class([
        'fi-sidebar-header items-center ',
        'flex h-16bg-white px-6 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 lg:shadow-sm' => $isMainSidebar,
    ])>
        @if ($isMainSidebar)
            {{-- format-ignore-start --}}
            <div
                @if ($isSidebarCollapsibleOnDesktop) x-show="$store.sidebar.isOpen"
                x-transition:enter="lg:transition lg:delay-100"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" @endif>
                @if ($homeUrl = filament()->getHomeUrl())
                    <a href="{{ $homeUrl }}">
                        <x-filament-panels::logo />
                    </a>
                @else
                    <x-filament-panels::logo />
                @endif
            </div>
            {{-- format-ignore-end --}}
        @elseif($title || $description)
            <div class="flex items-center rtl:space-x-reverse">
                @if ($title != null || $description != null)
                    <div class="w-full">
                        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 truncate block">
                            {{ $title }}
                        </h3>
                        <p class="text-xs text-gray-500">
                            {{ $description }}
                        </p>
                    </div>
                @endif
            </div>
        @endif

        @if ($isSidebarCollapsibleOnDesktop)
            <x-filament::icon-button color="gray" :icon="$isRtl ? 'heroicon-o-chevron-left' : 'heroicon-o-chevron-right'" icon-alias="panels::sidebar.expand-button"
                icon-size="lg" :label="__('filament-panels::layout.actions.sidebar.expand.label')" x-cloak x-data="{}" x-on:click="$store.sidebar.open()"
                x-show="! $store.sidebar.isOpen" class="-mx-1.5" />
        @endif

        @if ($isSidebarCollapsibleOnDesktop || $isSidebarFullyCollapsibleOnDesktop)
            <x-filament::icon-button color="gray" :icon="$isRtl ? 'heroicon-o-chevron-right' : 'heroicon-o-chevron-left'" icon-alias="panels::sidebar.collapse-button"
                icon-size="lg" :label="__('filament-panels::layout.actions.sidebar.collapse.label')" x-cloak x-data="{}" x-on:click="$store.sidebar.close()"
                x-show="$store.sidebar.isOpen" class="-mx-1.5 ms-auto hidden lg:flex" />
        @endif
    </header>

    <nav @class([
        'fi-sidebar-nav flex flex-col gap-y-7 py-8',
        'px-0' => !$isMainSidebar,
        'overflow-y-auto overflow-x-hidden px-6' => $isMainSidebar,
    ])>
        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::sidebar.nav.start') }}

        @if ($hasTenancy)
            <div @class([
                '-mx-2' => !$isSidebarCollapsibleOnDesktop,
            ]) @if ($isSidebarCollapsibleOnDesktop)
                x-bind:class="$store.sidebar.isOpen ? '-mx-2' : '-mx-4'"
        @endif
        >
        <x-filament-panels::tenant-menu />
        </div>
        @endif

        @if ($hasNavigation)
            <ul class="-mx-2 flex flex-col gap-y-7">
                @foreach ($navigation as $group)
                    <x-filament-panels::sidebar.group :collapsible="$group->isCollapsible()" :icon="$group->getIcon()" :items="$group->getItems()"
                        :label="$group->getLabel()" />
                @endforeach
            </ul>

            @php
                $collapsedNavigationGroupLabels = collect($navigation)
                    ->filter(fn(\Filament\Navigation\NavigationGroup $group): bool => $group->isCollapsed())
                    ->map(fn(\Filament\Navigation\NavigationGroup $group): string => $group->getLabel())
                    ->values();
            @endphp

            <script>
                if (typeof(collapsedGroups) == 'undefined') {
                    let collapsedGroups = {};
                }
                /*let*/
                collapsedGroups = JSON.parse(
                    localStorage.getItem('collapsedGroups'),
                )

                if (collapsedGroups === null || collapsedGroups === 'null') {
                    localStorage.setItem(
                        'collapsedGroups',
                        JSON.stringify(@js($collapsedNavigationGroupLabels)),
                    )
                }

                collapsedGroups = JSON.parse(
                    localStorage.getItem('collapsedGroups'),
                )

                document
                    .querySelectorAll('.fi-sidebar-group')
                    .forEach((group) => {
                        if (
                            !collapsedGroups.includes(group.dataset.groupLabel)
                        ) {
                            return
                        }

                        // Alpine.js loads too slow, so attempt to hide a
                        // collapsed sidebar group earlier.
                        group.querySelector(
                            '.fi-sidebar-group-items',
                        ).style.display = 'none'
                        group
                            .querySelector('.fi-sidebar-group-collapse-button')
                            .classList.add('rotate-180')
                    })
            </script>
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::sidebar.nav.end') }}
    </nav>

    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::sidebar.footer') }}
</aside>
