<div class="navbar-wrapper">
    <div class="m-header">
        <a href="{{backend('/')}}" class="logo-container b-brand text-primary">
            <img src="{{core_static('images/logo.png')}}" class="logo-shining-image img-fluid logo-lg logo regular" alt="logo">
            <div class="logo-shine"></div>
        </a>
    </div>
    <div class="navbar-content">
        <ul class="pc-navbar">
            @foreach($__backendMenu as $menuKey => $menuData)
                <li class="pc-item pc-hasmenu">
                    <a href="#" class="pc-link">
                        <span class="pc-micon"><i class="fa-duotone fa-solid fa-{{$__backendMenuIcon[$loop->index]}}"></i></span>
                        <span class="pc-mtext">{{$menuKey}}</span>
                        <span class="pc-arrow"><i class="fa-duotone fa-solid fa-chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        @foreach($menuData as $menuName => $menuItem)
                            <li class="pc-item pc-hasmenu">
                                <a href="#" class="pc-link">{{$menuName}}<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
                                <ul class="pc-submenu">
                                    @foreach($menuItem as $item)
                                        <li class="pc-item"><a href="{{backend($item['link'])}}" class="pc-link">{{$item['name']}}</a></li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach

            <li class="pc-item pc-hasmenu">
                <a href="#" class="pc-link">
                    <span class="pc-micon"><i class="fa-duotone fa-solid fa-pen-to-square"></i></span>
                    <span class="pc-mtext">Bài viết</span>
                    <span class="pc-arrow"><i class="fa-duotone fa-solid fa-chevron-right"></i></span>
                </a>
                <ul class="pc-submenu">
                    <li class="pc-item"><a href="{{backend('core-post')}}" class="pc-link"> Bài viết </a></li>
                    <li class="pc-item"><a href="{{backend('core-post.category')}}" class="pc-link"> Danh mục </a></li>
                </ul>
            </li>
            <li class="pc-item pc-hasmenu">
                <a href="#" class="pc-link">
                    <span class="pc-micon"><i class="fa-duotone fa-solid fa-gear"></i></span>
                    <span class="pc-mtext">Cài đặt</span>
                    <span class="pc-arrow"><i class="fa-duotone fa-solid fa-chevron-right"></i></span>
                </a>
                <ul class="pc-submenu">
                    <li class="pc-item"><a href="{{backend('core-setting.config')}}" class="pc-link"> Cài đặt chung </a></li>
                    <li class="pc-item"><a href="{{backend('core-setting.website')}}" class="pc-link"> Embed Code </a></li>
                    <li class="pc-item"><a href="{{backend('core-page')}}" class="pc-link"> Trang Tĩnh </a></li>
                    <li class="pc-item"><a href="{{backend('core-setting.data')}}" class="pc-link"> Cài đặt dữ liệu </a></li>
                    <li class="pc-item"><a href="{{backend('core-setting.maintenance-mode')}}" class="pc-link"> Chế độ bảo trì </a></li>
                    <li class="pc-item"><a href="{{backend('core-users')}}" class="pc-link"> Quản trị viên </a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>




