@if($current_file)
    @push('topMenu')
        <a href="{{$logs_url}}?dl={{$logs_data}}" class="btn btn-sm btn-success"><i class="fa-duotone fa-solid fa-download"></i>&nbsp; Download</a>
        <a onclick="return confirm('Bạn có muốn xóa dữ liệu log không?');" href="{{$logs_url}}?clean={{$logs_data}}" class="btn btn-sm btn-warning mx-2"><i class="fa-duotone fa-solid fa-trash-can"></i>&nbsp; Delete</a>
        <a onclick="return confirm('Bạn có muốn xóa sạch dữ liệu log không?');" href="{{$logs_url}}?reset=true{{ ($current_folder) ? '&f=' . encrypt($current_folder) : '' }}" class="btn btn-sm btn-danger"><i class="fa-duotone fa-solid fa-broom-wide"></i>&nbsp; Delete All</a>
    @endpush
@endif

@if ($logs === null)
    <div>
        Log file >50M, please download it.
    </div>
@else
    <div class="row">
        <div class="col-sm-12">
            <div class="table-container">
                <table id="tableData" class="table table-responsive table-bordered table-striped order-column tr-centered" style="max-width: 100%;">
                    <thead>
                    <tr>
                        @if ($standardFormat)
                            <th>Level</th>
                            <th>Contex</th>
                            <th>Date</th>
                        @else
                            <th>Line number</th>
                        @endif
                        <th class="no-sort">Log Detail</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($logs as $key => $log)
                        @if($log['level'])
                        <tr data-display="stack{{{$key}}}">
                            @if ($standardFormat)
                                <td class="nowrap">{{ucfirst($log['level'])}}</td>
                                <td class="nowrap">{{$log['context']}}</td>
                                <td class="nowrap fs-12">{{$log['date']}}</td>
                            @else
                                <td class="date td-top"></td>
                            @endif
                            <td class="nowrap" onclick="loadStackInfo('stack_{{{$key}}}');">
                                @if ($log['stack']) <i class="fa-duotone fa-solid fa-magnifying-glass"></i> @endif {{{$log['text']}}}
                                @if (isset($log['in_file']))
                                    <br/>{{{$log['in_file']}}}
                                @endif
                                @if ($log['stack'])
                                    <div class="stack stack-info" id="stack_{{{$key}}}" style="display: none; white-space: pre-wrap;">{{{ trim($log['stack']) }}}</div>
                                @endif
                            </td>
                        </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @push('footer')
        <script>
            function loadStackInfo(stack_id){
                const stack = $("#" + stack_id);
                if(stack.is(":visible")){
                    $(".stack-info").hide();
                    stack.hide();
                } else{
                    $(".stack-info").hide();
                    stack.show();
                }
            }
            $(document).ready(function () {
                $('#tableData').DataTable();
            });
        </script>
    @endpush
@endif


