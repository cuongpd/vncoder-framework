<meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
<link rel="icon" type="image/png" href="{{core_static('images/favicon.png')}}">
<title>{{$__metaData->title}}</title>
<meta name="description" content="{{$__metaData->description}}"/>
<meta name="robots" content="nofollow"/>
<meta name="copyright" content="2021 by Loader198"/>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
<link href="{{core_static('css/style.css')}}?v=1.22" rel="stylesheet" type="text/css" />
<link href="{{core_static('css/app.css')}}?v={{TIME_NOW}}" rel="stylesheet" type="text/css" />
{!! $__extraHeaderCSS !!}
<script>const BASE_URL = "{{BASE_URL}}", API_URL = "{{BASE_URL}}api/", BACKEND_URL = "{{BASE_URL}}backend/", CURRENT_URL = "{{$__currentUrl}}", PARENT_URL = "{{$__parentBackendUrl}}", CSRF_TOKEN = "{{csrf_token()}}",  TIME_NOW = {{TIME_NOW}};</script>
{!! $__extraHeaderJS !!}
<script>$.ajaxSetup({headers: {"X-CSRF-TOKEN": "{{csrf_token()}}"}});</script>
{!! $__extraHeader !!}
@stack('header')