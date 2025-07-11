<script type="text/javascript" src="{{core_static('js/bootstrap.min.js')}}?v=5.3"></script>
<script type="text/javascript" src="{{core_static('js/popper.min.js')}}?v=2.11.8"></script>
<script type="text/javascript" src="{{core_static('js/tippy.umd.min.js')}}"></script>
<script type="text/javascript" src="{{core_static('js/simplebar.min.js')}}?v=6.2.5"></script>
<script type="text/javascript" src="{{core_static('js/app.min.js')}}?v=1.21"></script>
{!! $__extraFooter !!}
{!! $__extraFooterJS !!}
@stack('footer')
@if ($__message = show_message())
    <script>vncoder.showMessage('{{$__message}}', {{flash_message_status()}});</script>
@endif
