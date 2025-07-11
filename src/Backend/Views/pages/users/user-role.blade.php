@push('topMenu')
    <a href="{{$linkEdit}}" title="Tạo quyền mới"><button type="button" class="btn btn-sm btn-primary"><i class="fa-duotone fa-solid fa-plus"></i> Tạo nhóm quyền</button></a>
@endpush

<div class="row">
    <div class="col-sm-12">
        <table id="tableData" class="table table-responsive table-bordered table-striped order-column tr-centered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Permissions</th>
                <th class="no-sort text-center" width="150px">Logs</th>
                <th class="no-sort text-center" width="120px">Action</th>
            </tr>
            </thead>
            @foreach($userRoleData as $item)
                <tr>
                    <td>{{$item->id}}</td>
                    <td>{{$item->name}}</td>
                    <td>{{$item->description}}</td>
                    <td>{{$item->permissions}}</td>
                    <td class="text-left fs-10">
                        <span>C : {{$item->created ?? 'N/A'}}</span><br/>
                        <span>U : {{$item->updated ?? 'N/A'}}</span>
                    </td>
                    <td class="text-center">
                        @if($item->id > 1)
                        <a href="{{$linkEdit}}?id={{$item->id}}" class="btn btn-sm text-info" title="Chỉnh sửa quyền"><i class="fa-duotone fa-solid fa-pen-to-square fa-24"></i></a>
                        <a href="{{$linkPermissions}}?id={{$item->id}}" class="btn btn-sm text-success" title="Cập nhật quyền hạn"><i class="fa-duotone fa-solid fa-shield-check fa-24"></i></a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
