@php
    $topMenuStack = trim($__env->yieldPushContent('topMenu'));
@endphp
<div class="header-wrapper">
    <div class="me-auto pc-mob-drp">
        <ul class="list-unstyled">
            <li class="pc-h-item pc-sidebar-collapse">
                <a href="#" class="pc-head-link ms-0" id="sidebar-hide"><i class="fa-duotone fa-solid fa-bars fa-24"></i></a>
            </li>
            <li class="pc-h-item pc-sidebar-popup">
                <a href="#" class="pc-head-link ms-0" id="mobile-collapse"><i class="fa-duotone fa-solid fa-bars fa-24"></i></a>
            </li>
            <li class="pc-h-item @if (!empty($topMenuStack)) d-none d-sm-block @endif" id="index-title">
                <h4 class="meta-data-title" id="meta-data-title" onclick="return vncoder.gotoUrl('{{backend('/')}}', 'Đang tải trang...');">{!! $__metaData->title !!}</h4>
            </li>
        </ul>
    </div>
    <div class="ms-auto">
        <ul class="list-unstyled">
            @if (!empty($topMenuStack))
                <li class="pc-h-item">
                    <div class="menu-item">
                        {!! $topMenuStack !!}
                    </div>
                </li>
            @endif

            <li class="dropdown pc-h-item header-user-profile">
                <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
                    <img src="{{$__userData['avatar']}}" alt="user-image" class="user-avtar px-1">
                </a>
                <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
                    <a href="{{BASE_URL}}" class="dropdown-item" target="_blank"><i class="fa-duotone fa-solid fa-globe-pointer"></i><span>Website</span></a>
                    <a href="#" class="dropdown-item"><i class="fa-duotone fa-solid fa-user"></i><span>User Profile</span></a>
                    <a href="{{backend('core-setting/config')}}" class="dropdown-item"><i class="fa-duotone fa-solid fa-gear fa-spin"></i><span>Settings</span></a>
                    <a href="#" class="dropdown-item"><i class="fa-duotone fa-solid fa-headset"></i><span>Support</span></a>
                    <a href="{{backend('core-logs')}}" class="dropdown-item"><i class="fa-duotone fa-solid fa-xmarks-lines"></i><span>Xem Logs</span></a>
                    <a href="javascript:void(0);" class="dropdown-item" onclick="updateDebugBar();"><i class="fa-duotone fa-solid fa-bug"></i>Debug : {{$__debugbar}}</a>
                    <a href="{{backend('core-console/run')}}" class="dropdown-item"><i class="fa-duotone fa-solid fa-square-terminal"></i><span>Run Command</span></a>
                    <a href="javascript:void(0);" class="dropdown-item" onclick="resetCacheData();"><i class="fa-duotone fa-solid fa-broom-wide"></i><span>Reset Cache</span></a>
                    <a href="{{backend('logout.html')}}" class="dropdown-item"><i class="fa-duotone fa-solid fa-power-off fa-beat-fade"></i><span>Logout</span></a>
                </div>
            </li>
        </ul>
    </div>
</div>