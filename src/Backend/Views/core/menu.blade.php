@foreach($menuData as $menuKey => $menuData)
<li class="pc-item pc-hasmenu">
    <a href="#" class="pc-link">
        <span class="pc-micon"><i class="fa-duotone fa-solid fa-bars fa-24"></i></span>
        <span class="pc-mtext">{{$menuKey}}</span>
        <span class="pc-arrow"><i class="fa-duotone fa-solid fa-chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
@foreach($menuData as $menuName => $menuItem)
        <li class="pc-item pc-hasmenu">
            <a href="#" class="pc-link">{{$menuName}}<span class="pc-arrow"><i class="fa-duotone fa-solid fa-chevron-right"></i></span></a>
            <ul class="pc-submenu">
@foreach($menuItem as $item)
@php $xOn = '{{backend('; $xOff = ')}}'; @endphp
                <li class="pc-item"><a href="{{ $xOn }}'{{$item['link']}}'{{$xOff}}" class="pc-link">{{$item['name']}}</a></li>
@endforeach
            </ul>
        </li>
@endforeach
    </ul>
</li>
@endforeach