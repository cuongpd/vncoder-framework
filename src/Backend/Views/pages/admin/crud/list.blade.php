@push('topMenu')
    <a href="{{$__currentBackendUrlEdit}}" class="btn btn-sm btn-success f-12" title="Thêm mới : {{$crudName}}"><i class="fa-duotone fa-solid fa-plus"></i>&nbsp;Thêm mới</a>
@endpush

<div class="row">
    <div class="col-sm-12">
        @if($crudData->count() > 0)
            <table id="tableData" class="table table-responsive table-bordered table-striped order-column tr-centered">
                <thead>
                <tr>
                    <th class="text-center" width="15px">ID</th>
                    @foreach($crudColumn as $k => $n)
                        <th class="text-center">{{$n}}</th>
                    @endforeach
                    <th class="no-sort text-center" width="120px">Logs</th>
                    <th class="no-sort text-center" width="100px">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($crudData as $item)
                    <tr>
                        <td class="text-center">{{$item->id}}</td>
                        @foreach($crudColumn as $k => $n)
                            @if($item->$k)
                                @switch($k)
                                    @case('photo')
                                    @case('photo2')
                                    @case('banner')
                                    @case('image')
                                    @case('avatar')
                                    @case('thumbnail')
                                    <td class="text-center">
                                        <a href="{{url($item->$k)}}" target="_blank">
                                            <img src="{{url($item->$k)}}" class="photo-thumbnail" style="max-height:48px;">
                                        </a>
                                    </td>
                                    @break
                                    @case('link')
                                    @case('href')
                                    @case('url')
                                    <td class="text-left">
                                        <a href="{{$item->$k}}" target="_blank">{{$item->$k}}</a>
                                    </td>
                                    @break
                                    @default
                                    <td class="text-left">
                                        @if(str_ends_with($k, '_html'))
                                            {!! $item->$k !!}
                                        @else
                                            {{$item->$k}}
                                        @endif
                                    </td>
                                @endswitch
                            @else
                                <td class="text-left"></td>
                            @endif
                        @endforeach
                        <td class="text-left fs-10">
                            <span>C : {{$item->created ?? 'N/A'}}</span><br/>
                            <span>U : {{$item->updated ?? 'N/A'}}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{$__currentBackendUrlEdit}}?id={{$item->id}}" class="btn btn-sm text-info"><i class="fa-duotone fa-solid fa-pen-to-square fa-24"></i></a>
                            <a href="{{$__currentBackendUrlDelete}}?id={{$item->id}}" onclick="return confirm('Bạn có muốn xóa bỏ dữ liệu này không?');" class="btn btn-sm text-danger"><i class="fa-duotone fa-solid fa-trash-can-xmark fa-24"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="alert alert-warning text-center">Không tìm thấy dữ liệu</div>
        @endif
    </div>
</div>
@push('footer')
    <script>
        $(document).ready(function() {
            $('#tableData').DataTable();
        });
    </script>
@endpush
