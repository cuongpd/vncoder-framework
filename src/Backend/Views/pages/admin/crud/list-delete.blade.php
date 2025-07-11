<div class="row">
    <div class="col-sm-12">
        @if($crudData->count() > 0)
            <table id="tableData" class="table table-responsive table-bordered table-striped order-column tr-centered">
                <thead>
                <tr>
                    <th>ID</th>
                    @foreach($crudColumn as $k => $n)
                        <th>{{$n}}</th>
                    @endforeach
                    <th class="no-sort text-center" width="72px">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($crudData as $item)
                    <tr>
                        <td align="center">{{$item->id}}</td>
                        @foreach($crudColumn as $k => $n)
                            <td>{{$item->$k}}</td>
                        @endforeach
                        <td class="text-center">
                            <a href="{{$__currentBackendUrlRestore}}?id={{$item->id}}" onclick="return confirm('Bạn có muốn khôi phục dữ liệu này không???');" class="btn btn-warning btn-sm mo-mb-2"><i class="fa-duotone fa-solid fa-trash-undo fa-24"></i></a>
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
