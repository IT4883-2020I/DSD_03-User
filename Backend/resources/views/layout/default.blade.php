<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" {{ Metronic::printAttrs('html') }} {{ Metronic::printClasses('html') }}>
    <head>
        <meta charset="utf-8"/>

        {{-- Title Section --}}
        <title>{{ config('app.name') }} | @yield('title', $page_title ?? '')</title>

        {{-- Meta Data --}}
        <meta name="description" content="@yield('page_description', $page_description ?? '')"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

        {{-- Favicon --}}
        <link rel="shortcut icon" href="{{ asset('media/logos/favicon.ico') }}" />

        {{-- Fonts --}}
        {{ Metronic::getGoogleFontsInclude() }}

        {{-- Global Theme Styles (used by all pages) --}}
        @foreach(config('layout.resources.css') as $style)
            <link href="{{ config('layout.self.rtl') ? asset(Metronic::rtlCssPath($style)) : asset($style) }}" rel="stylesheet" type="text/css"/>
        @endforeach

        {{-- Layout Themes (used by all pages) --}}
        @foreach (Metronic::initThemes() as $theme)
            <link href="{{ config('layout.self.rtl') ? asset(Metronic::rtlCssPath($theme)) : asset($theme) }}" rel="stylesheet" type="text/css"/>
        @endforeach
        <link rel="stylesheet" href="/css/pnotify.custom.min.css">
        <link rel="stylesheet" href="{{ asset('plugins/custom/daterangepicker/daterangepicker.css') }}">
        {{-- Includable CSS --}}
        @yield('styles')
        <script>
            var api_token = "{{ Auth::user() ? Auth::user()->api_token : '' }}";
        </script>
        <script src="/js/scripts/jquery.min.js"></script>
        <script src="{{ asset('plugins/custom/angular/angular.min.js') }}"></script>
        <script src="{{ asset('plugins/custom/angular/ng-file-upload.min.js') }}"></script>
        <script src="{{ asset('plugins/custom/angular/angular-sanitize.min.js') }}"></script>
        <script src="{{ asset('plugins/custom/angular/angular-route.js') }}"></script>
        <script src="{{ asset('plugins/custom/angular/angular-chosen.min.js') }}"></script>

        <script type="text/javascript" src="/js/scripts/chosen.jquery.min.js"></script>
        <script src="{{ asset('js/bootstrap.bundle.min.js').'?v='.config('app.version') }}"></script>
        <script type="text/javascript" src="{{ asset('plugins/custom/daterangepicker/moment.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('plugins/custom/daterangepicker/daterangepicker.js')}} "></script>
        <script src="{{ asset('js/angular-controller/system.js').'?v='.config('app.version') }}"></script>
        <script src="{{ asset('js/angular-controller/base-controller.js').'?v='.config('app.version') }}"></script>
        <script src="{{ asset('js/angular-controller/header-controller.js').'?v='.config('app.version') }}"></script>
        <script src="/js/pnotify.custom.min.js"></script>
    </head>

    <body {{ Metronic::printAttrs('body') }} {{ Metronic::printClasses('body') }} ng-app="Distributed" ng-cloak="">

        @if (config('layout.page-loader.type') != '')
            @include('layout.partials._page-loader')
        @endif

        @include('layout.base._layout')

        <script>var HOST_URL = "{{ route('quick-search') }}";</script>

        {{-- Global Config (global config for global JS scripts) --}}
        <script>
            var KTAppSettings = {!! json_encode(config('layout.js'), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) !!};
        </script>

        <script>
            var user = JSON.parse('<?= Auth::user()?>');
            var app_url = '<?= config('app.url')?>';
        </script>

        {{-- Global Theme JS Bundle (used by all pages)  --}}
        @foreach(config('layout.resources.js') as $script)
            <script src="{{ asset($script) }}" type="text/javascript"></script>
        @endforeach

        {{-- Includable JS --}}
        @yield('scripts')

    </body>
</html>

