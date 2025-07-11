<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{$__metaData->title}}</title>
    <meta name="description" content="{{$__metaData->description}}"/>
    <meta name="keywords" content="{{$__metaData->keywords}}"/>
    <meta name="author" content="{{$__metaData->author}}" />
    @include('backend::includes.page_begin')
</head>
<body>
    <nav class="pc-sidebar @if($__hideSidebar) pc-sidebar-hide @endif">@include('backend::includes.sidebar')</nav>
    <header class="pc-header">@include('backend::includes.header')</header>
    <div class="pc-container p-t-10">
        <div class="pc-content">
            @yield('pageContent')
        </div>
    </div>
    @include('backend::includes.page_end')
</body>
</html>
