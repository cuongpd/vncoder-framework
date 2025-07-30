<form class="form-data" id="form-data" @if($isConsoleRunning) style="display: none;" @endif method="POST" accept-charset="utf-8" enctype="multipart/form-data" action="{{$__currentBackendUrl}}">
    <div class="container mt-4">
        <label for="commandSelect" class="form-label fs-5">Chọn lệnh cần chạy</label>
        <div class="input-group mb-3">
            <select name="command" id="commandSelect" class="form-select" required>
                <option value="">-- Chọn lệnh để chạy --</option>
                @foreach($listCommand as $item)
                    <option value="{{$item}}">php artisan run {{$item}}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Run</button>
        </div>
    </div>
</form>
@if($isConsoleRunning)
    <div class="alert alert-warning" id="is_console_running">
        <strong>Chú ý:</strong> Đang thực thi lệnh <strong>{{$currentCommand}}</strong>. Vui lòng đợi cho đến khi lệnh hoàn thành.
    </div>
@endif
<div class="container mt-4">
    <div id="data-container" class="mt-3 text-success fw-bold" style="white-space: pre-wrap;"></div>
</div>


@push('footer')
<script>
    $(document).ready(function() {
        function fetchData() {
            $.ajax({
                url: '{{$__currentBackendUrlData}}',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 1) {
                        $('#data-container').text(response.data);
                        $('#is_console_running').hide();
                        clearInterval(intervalId);
                        $('#form-data').show();
                    } else {
                        $('#data-container').text(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Lỗi kết nối:', error);
                    $('#data-container').text('Lỗi kết nối đến server.');
                }
            });
        }
        fetchData();
        intervalId = setInterval(fetchData, 10000);
    });
</script>
@endpush