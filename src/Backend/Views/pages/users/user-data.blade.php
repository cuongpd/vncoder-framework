@push('topMenu')
    <a href="{{backend('core-users/edit')}}" title="Thêm quản trị viên"><button type="button" class="btn btn-sm btn-primary"><i class="fa-duotone fa-solid fa-plus"></i>&nbsp;Thêm mới</button></a>
@endpush

<div class="row">
    <div class="col-sm-12">
        <table id="tableData" class="table table-responsive table-bordered table-striped order-column tr-centered">
            <thead>
            <tr>
                <th class="text-center" width="15px">ID</th>
                <th class="">Role</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th class="no-sort text-center" width="150px">Logs</th>
                <th class="no-sort text-center" width="120px">Status</th>
                <th class="no-sort text-center" width="120px">Action</th>
            </tr>
            </thead>
            @foreach($userData as $item)
                <tr>
                    <td>{{$item->id}}</td>
                    <td>{{$item->role_name}}</td>
                    <td>{{$item->name}}</td>
                    <td>{{$item->email}}</td>
                    <td>{{$item->phone}}</td>
                    <td>{{$item->address}}</td>
                    <td class="text-left fs-10">
                        <span>C : {{$item->created ?? 'N/A'}}</span><br/>
                        <span>U : {{$item->updated ?? 'N/A'}}</span>
                    </td>
                    <td class="text-center">{!! $item->user_status !!}</td>
                    <td class="text-center">
                        <a href="{{ backend('core-users/edit') }}?uid={{ $item->id }}" class="btn btn-sm text-info" title="Chỉnh sửa người dùng"><i class="fa-duotone fa-solid fa-pen-to-square fa-24"></i></a>
                        @if($item->role != 'root' && $item->id !== $__userData['id'])
                        @if($item->status > 0)
                        <a href="{{ backend('core-users/lock-user') }}?uid={{ $item->id }}" class="btn btn-sm text-danger" title="Khóa tài khoản"><i class="fa-duotone fa-solid fa-lock fa-24"></i></a>
                        @else
                            <a href="{{ backend('core-users/unlock-user') }}?uid={{ $item->id }}" class="btn btn-sm text-warning" title="Mở khóa tài khỏan"><i class="fa-duotone fa-solid fa-lock-open fa-24"></i></a>
                        @endif
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
@push('footer')
    <script>
        $(document).ready(function() {
            $('#tableData').DataTable();
        });
    </script>
@endpush
