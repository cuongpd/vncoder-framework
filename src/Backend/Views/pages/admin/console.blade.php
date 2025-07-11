<div class="container mt-4">
    <label for="commandSelect" class="form-label fs-5">Chọn lệnh cần chạy</label>
    <div class="input-group mb-3">
        <select id="commandSelect" class="form-select">
            <option value="">-- Chọn lệnh để chạy --</option>
            @foreach($listCommand as $item)
                <option value="{{$item}}">php artisan run {{$item}}</option>
            @endforeach
        </select>
        <button id="runBtn" class="btn btn-primary">Run</button>
    </div>
    <div id="output" class="mt-3 text-success fw-bold" style="white-space: pre-wrap;"></div>
</div>


@push('footer')
    <script>
        function stripAnsi(input) {
            return input.replace(/\x1b\[[0-9;]*[A-Za-z]/g, '');
        }

        $(document).ready(function() {
            $("#runBtn").on("click", function() {
                const selected = $("#commandSelect").val();
                const output = $("#output");
                if (!selected) {
                    output.text("Vui lòng chọn lệnh.");
                    return;
                }
                const [controller, action] = selected.split(" ");
                output.html("⏳ Đang thực thi...");

                $.get(API_URL + "run-console", {
                    cmd : "console",
                    controller: controller,
                    action: action
                })
                .done(function (data) {
                    const clean = stripAnsi(data);
                    output.html(clean.replace(/\n/g, "<br>"));
                })
                .fail(function (xhr) {
                    output.text("Lỗi khi thực thi: " + xhr.status + " " + xhr.statusText);
                });
            });
        });
    </script>

@endpush